<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\MarqueeRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeService
{
    protected $productService;
    protected $marqueeService;

    public function __construct(
        ProductService $productService,
        MarqueeService $marqueeService
    ) {
        $this->productService = $productService;
        $this->marqueeService = $marqueeService;
    }

    public function getHomePageData()
    {
        $user = Auth::user();
        $data = [
            'user' => $user,
            'marqee' => $this->marqueeService->getAllMarquees(),
            'allProducts' => $this->productService->getAllProducts(),
            'few_products' => $this->productService->getRandomProducts(5),
            'products_category' => $this->productService->getAllCategories(),
            'infos' => $this->getUserInfos($user)
        ];

        return $data;
    }

    public function getSearchPageData(string $category)
    {
        $products = $this->productService->getProductsByCategory($category);
        if ($products->isEmpty()) {
            return false;
        }

        $data = [
            'user' => Auth::user(),
            'marqee' => $this->marqueeService->getAllMarquees(),
            'allProducts' => $products,
            'few_products' => $this->productService->getRandomProducts(5),
            'products_category' => $this->productService->getAllCategories(),
            'infos' => $this->getUserInfos(Auth::user())
        ];

        return $data;
    }

    public function getItemPageData(int $productId)
    {
        $data = [
            'user' => Auth::user(),
            'marqee' => $this->marqueeService->getAllMarquees(),
            'products' => $this->productService->getProductById($productId),
            'few_products' => $this->productService->getRandomProducts(4)
        ];

        return $data;
    }

    protected function getUserInfos($user)
    {
        if (!$user || empty($user->info0)) {
            return [];
        }

        return array_filter(explode(';', $user->info0));
    }
}