<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\WishlistRepository;
use App\Repository\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    protected $productRepository;
    protected $wishlistRepository;
    protected $orderRepository;

    public function __construct(
        ProductRepository $productRepository,
        WishlistRepository $wishlistRepository,
        OrderRepository $orderRepository
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getCheckoutData()
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();

        // 抓出使用者的want欄位並處理
        $wantString = $this->wishlistRepository->getUserWantList($user->account);
        $wantedIds = $this->wishlistRepository->parseWantIds($wantString);

        // 取得想要的商品資訊
        $wantedProducts = $this->productRepository->findProductsByIds($wantedIds);

        return [
            'user' => $user,
            'marqee' => $marqee,
            'wanted_product' => $wantedProducts
        ];
    }

    public function processCheckout(Request $request)
    {
        $user = Auth::user();
        $itemIds = $request->input('selected_items', []);
        $quantities = $request->input('quantity', []);

        // 驗證選擇的商品
        if (empty($itemIds)) {
            return ['error' => '請選擇商品', 'redirect' => 'check_show'];
        }

        // 驗證商品數量
        foreach ($quantities as $quantity) {
            if ($quantity == 0) {
                return ['error' => '選取的商品數量不可為 0 喔!', 'redirect' => 'check_show'];
            }
        }

        // 過濾空值
        $quantities = array_filter($quantities);

        // 取得價格
        $prices = $this->productRepository->getProductPrices($itemIds);

        // 合併商品資訊並計算總價
        $purchasedData = $this->preparePurchasedData($itemIds, $quantities, $prices);
        $totalPrice = $this->calculateTotalPrice($purchasedData['merged_arr']);

        // 更新使用者的想要清單
        // $this->updateUserWantList($user->account, $itemIds);

        // 建立購買記錄
        $this->orderRepository->createPurchaseOrder(
            $user->account,
            $purchasedData['purchased_string'],
            $totalPrice
        );

        return ['success' => '儲存成功', 'redirect' => 'pay_show'];
    }

    private function preparePurchasedData($itemIds, $quantities, $prices)
    {
        // 使用 array_map 並合併三個陣列
        $mergedArr = array_map(function($v1, $v2, $v3) {
            return $v1 . ',' . $v2 . ',' . $v3;
        }, $itemIds, $quantities, $prices);

        // 陣列轉字串
        $purchasedString = implode(';', $mergedArr);

        return [
            'merged_arr' => $mergedArr,
            'purchased_string' => $purchasedString
        ];
    }

    private function calculateTotalPrice($mergedArr)
    {
        $totalPrice = 0;
        foreach ($mergedArr as $item) {
            $itemData = explode(',', $item);
            $totalPrice += $itemData[1] * $itemData[2]; // quantity * price
        }
        return $totalPrice;
    }

    public function updateUserWantList($userAccount, $purchasedItemIds)
    {
        // 取得原先的想要清單
        $currentWantString = $this->wishlistRepository->getUserWantList($userAccount);
        $currentWantIds = $this->wishlistRepository->parseWantIds($currentWantString);

        // 移除已購買的商品
        $newWantList = $this->wishlistRepository->removeItemsFromWantList($currentWantIds, $purchasedItemIds);

        // 更新使用者的想要清單
        $this->wishlistRepository->updateUserWantList($userAccount, $newWantList);
    }
}