<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;
use App\Models\purchasedModel as Order;
use Illuminate\Support\Facades\Auth;

class CartService
{
    protected $userRepository;
    protected $productRepository;
    protected $orderRepository;

    public function __construct(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository
    ) {
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getWishlistProducts(string $userAccount)
    {
        $user = $this->userRepository->findByEmail($userAccount);
        if (!$user || empty($user->want)) {
            return [];
        }

        // Clean up the want field string
        $wantedIds = preg_replace('/[^\d,]/', '', $user->want);
        $wantedIds = isset($wantedIds[0]) && $wantedIds[0] === ',' ? substr($wantedIds, 1) : $wantedIds;
        
        // Convert to array and remove empty values
        $wantedIdsArray = array_filter(explode(",", $wantedIds));
        
        // Get product details
        $products = [];
        foreach ($wantedIdsArray as $id) {
            if ($product = $this->productRepository->findById($id)) {
                $products[] = $product;
            }
        }

        return array_values(array_unique($products));
    }

    public function createOrder(array $items, array $quantities)
    {
        if (empty($items)) {
            throw new \Exception('請選擇商品');
        }

        // Validate quantities
        foreach ($quantities as $quantity) {
            if ($quantity == 0) {
                throw new \Exception('選取的商品數量不可為 0 喔!');
            }
        }

        // Get prices for all items
        $prices = [];
        foreach ($items as $itemId) {
            $product = $this->productRepository->findById($itemId);
            $prices[] = $product->price;
        }

        // Create purchase string
        $purchaseData = array_map(function($id, $qty, $price) {
            return "{$id},{$qty},{$price}";
        }, $items, $quantities, $prices);
        
        $purchased = implode(';', $purchaseData);

        // Calculate total price
        $total = 0;
        foreach ($purchaseData as $data) {
            $items = explode(',', $data);
            $total += $items[1] * $items[2];
        }

        // Update user's wishlist
        $this->removeFromWishlist(Auth::user(), $items);

        // Create order
        return Order::create([
            'account' => Auth::user()->account,
            'purchased' => $purchased,
            'bill' => $total,
            'payed' => "0",
            'delivered' => "0",
            'recieved' => "0",
            'show' => "0",
        ]);
    }

    protected function removeFromWishlist($user, array $itemIds)
    {
        if (empty($user->want)) {
            return;
        }

        $wantedIds = preg_replace('/[^\d,]/', '', $user->want);
        $wantedArray = array_filter(array_unique(explode(",", $wantedIds)));
        $newWantedArray = array_diff($wantedArray, $itemIds);
        
        $user->want = implode(",", $newWantedArray) . ',';
        $user->save();
    }
}