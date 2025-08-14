<?php

namespace App\Services;

use App\Models\productsModel as Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getRandomProducts(int $limit = 5)
    {
        return $this->productRepository->getRandomProducts($limit);
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllProducts();
    }

    public function getProductsByCategory(string $category)
    {
        return $this->productRepository->getProductsByCategory($category);
    }

    public function getProductById(int $id)
    {
        return $this->productRepository->findById($id);
    }

    public function getAllCategories()
    {
        return $this->productRepository->getAllCategories();
    }

    public function createProduct(array $data, UploadedFile $image = null)
    {
        $productData = $data;
        
        if ($image) {
            $imagePath = $this->handleImageUpload($image);
            $productData['pic_dir'] = $imagePath;
        }

        return $this->productRepository->create($productData);
    }

    public function updateProduct(int $id, array $data, UploadedFile $image = null)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            return false;
        }

        if ($image) {
            $imagePath = $this->handleImageUpload($image);
            $data['pic_dir'] = $imagePath;
        }

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id)
    {
        return $this->productRepository->delete($id);
    }

    protected function handleImageUpload(UploadedFile $image)
    {
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('img/pictureTarget'), $imageName);
        return 'img/pictureTarget/' . $imageName;
    }

    public function searchProducts(string $searchWord)
    {
        return $this->productRepository->searchByCategory($searchWord);
    }
}