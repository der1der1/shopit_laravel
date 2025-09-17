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

    public function getAllProducts()
    {
        return Product::all();
    }

    public function getRandomProducts($limit = 5)
    {
        return Product::inRandomOrder()->limit($limit)->get();
    }

    public function getProductCategories()
    {
        return Product::select('category')->distinct()->get();
    }

    public function getProductsByCategory($category)
    {
        return Product::where('category', $category)->get();
    }

    public function searchProductsByCategory($searchWord)
    {
        return Product::where('category', $searchWord)->get();
    }
}