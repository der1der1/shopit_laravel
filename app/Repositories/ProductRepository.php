<?php

namespace App\Repositories;

use App\Models\productsModel as Product;

class ProductRepository
{
    public function getAllProducts()
    {
        return Product::all();
    }

    public function getRandomProducts(int $limit)
    {
        return Product::inRandomOrder()->limit($limit)->get();
    }

    public function getProductsByCategory(string $category)
    {
        return Product::where('category', $category)->get();
    }

    public function getAllCategories()
    {
        return Product::select('category')->distinct()->get();
    }

    public function findById(int $id)
    {
        return Product::where('id', $id)->first();
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(int $id, array $data)
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        foreach ($data as $key => $value) {
            if ($product->$key != $value) {
                $product->$key = $value;
            }
        }

        return $product->save();
    }

    public function delete(int $id)
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    public function searchByCategory(string $searchWord)
    {
        return Product::where('category', $searchWord)->get();
    }
}