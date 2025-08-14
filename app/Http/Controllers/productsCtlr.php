<?php

namespace App\Http\Controllers;

use App\Services\ProductService;

class productsCtlr extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function products()
    {
        $products = $this->productService->getAllProducts();
        
        return view('welcome', compact('products'));
    }
}
