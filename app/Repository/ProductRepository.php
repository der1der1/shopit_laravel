<?php

namespace App\Repository;

use App\Models\productsModel as Product;
use App\Models\marqeeModel;

class ProductRepository
{
    public function findProductById($productId)
    {
        return Product::where('id', $productId)->first();
    }

    public function findProductsByIds($productIds)
    {
        $products = [];
        foreach ($productIds as $productId) {
            if (!empty($productId)) {
                $product = $this->findProductById($productId);
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