<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeService
{
    protected $productRepository;
    protected $userRepository;

    public function __construct(ProductRepository $productRepository, UserRepository $userRepository)
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    public function getHomeData($sort = null)
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();
        $allProducts = $this->productRepository->getAllProducts($sort);
        $few_products = $this->productRepository->getRandomProducts(5);
        $products_category = $this->productRepository->getProductCategories();
        $infos = $this->userRepository->parseUserInfo($user);

        return [
            'user' => $user,
            'marqee' => $marqee,
            'allProducts' => $allProducts,
            'few_products' => $few_products,
            'products_category' => $products_category,
            'infos' => $infos
        ];
    }

    public function getHomeDataWithCategorySearch($category, $sort = null)
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();
        $few_products = $this->productRepository->getRandomProducts(5);
        $products_category = $this->productRepository->getProductCategories();
        $allProducts = $this->productRepository->getProductsByCategory($category, $sort);
        $infos = $this->userRepository->parseUserInfo($user);

        return [
            'user' => $user,
            'marqee' => $marqee,
            'allProducts' => $allProducts,
            'few_products' => $few_products,
            'products_category' => $products_category,
            'infos' => $infos
        ];
    }

    public function searchProductsByWords(Request $request, $sort = null)
    {
        $searchWord = $request->search_word;
        $allProducts = $this->productRepository->searchProductsByCategory($searchWord, $sort);

        if ($allProducts->isEmpty()) {
            return ['error' => '找不到您搜尋的項目，將為您返回。', 'redirect' => 'home'];
        }

        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();
        $few_products = $this->productRepository->getRandomProducts(5);
        $products_category = $this->productRepository->getProductCategories();
        $infos = $this->userRepository->parseUserInfo($user);

        return [
            'success' => true,
            'user' => $user,
            'marqee' => $marqee,
            'allProducts' => $allProducts,
            'few_products' => $few_products,
            'products_category' => $products_category,
            'infos' => $infos
        ];
    }

    public function getItemPageData($id)
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();
        $products = $this->productRepository->findProductById($id);
        $few_products = $this->productRepository->getRandomProducts(4);

        return [
            'user' => $user,
            'marqee' => $marqee,
            'products' => $products,
            'few_products' => $few_products
        ];
    }
}