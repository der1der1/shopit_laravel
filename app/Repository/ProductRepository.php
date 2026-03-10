<?php

namespace App\Repository;

use App\Models\productsModel as Product;
use App\Models\ProductVariantModel;
use App\Models\marqeeModel;

class ProductRepository
{
    public function findProductById($productId)
    {
        return Product::where('id', $productId)->first();
    }

    /**
     * 取得商品及其品項
     */
    public function findProductWithVariants($productId)
    {
        return Product::with('variants')->where('id', $productId)->first();
    }

    /**
     * 取得商品的上架品項
     */
    public function getActiveVariants($productId)
    {
        return ProductVariantModel::where('product_id', $productId)
                                   ->where('is_active', true)
                                   ->where('quantity', '>', 0)
                                   ->orderBy('sort_order')
                                   ->orderBy('id')
                                   ->get();
    }

    /**
     * 透過品項 ID 取得品項
     */
    public function findVariantById($variantId)
    {
        return ProductVariantModel::find($variantId);
    }

    /**
     * 取得品項價格
     */
    public function getVariantPrice($variantId)
    {
        $variant = $this->findVariantById($variantId);
        return $variant ? $variant->getDisplayPrice() : null;
    }

    public function findProductsByIds($wantedIdsVarisntIds)
    {
        // dump($wantedIdsVarisntIds);
        $products = [];
        foreach ($wantedIdsVarisntIds as $wantedIdVarisntId) {
            if (!empty($wantedIdVarisntId)) {
                $productId = explode('-', $wantedIdVarisntId)[0]; // 取得商品 ID，忽略品項 ID
                $variantId = explode('-', $wantedIdVarisntId)[1] ?? null; // 取得品項 ID（如果存在）
                $product = $this->findProductById($productId);
                $variant = $this->findVariantById($variantId);
                $product->variant = $variant; // 將品項資訊加到商品物件上
                if ($product) {
                    $products[] = $product;
                }
            }
        }
        return array_values(array_unique($products));
    }

    public function getProductPrice($productId)
    {
        return Product::where('id', $productId)->pluck('price')->first();
    }

    public function getProductPrices($productIds)
    {
        $prices = [];
        foreach ($productIds as $productId) {
            $prices[] = $this->getProductPrice($productId);
        }
        return $prices;
    }

    public function getAllMarqee()
    {
        return marqeeModel::getAllMarqee();
    }

    public function getAllProducts($sort = null)
    {
        $query = Product::query();
        
        return $this->applySorting($query, $sort)->get();
    }

    public function getRandomProducts($limit = 5)
    {
        return Product::inRandomOrder()->limit($limit)->get();
    }

    public function getProductCategories()
    {
        return Product::select('category')->distinct()->inRandomOrder()->get();
    }

    public function getProductsByCategory($category, $sort = null)
    {
        $query = Product::where('category', $category);
        
        return $this->applySorting($query, $sort)->get();
    }

    public function searchProductsByCategory($searchWord, $sort = null)
    {
        $query = Product::where('category', $searchWord);
        
        return $this->applySorting($query, $sort)->get();
    }
    
    protected function applySorting($query, $sort)
    {
        switch ($sort) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'newest':
                return $query->orderBy('updated_at', 'desc');
            case 'oldest':
                return $query->orderBy('updated_at', 'asc');
            default:
                return $query->inRandomOrder();
        }
    }
}