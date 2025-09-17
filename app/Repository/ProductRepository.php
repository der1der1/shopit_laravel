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
}