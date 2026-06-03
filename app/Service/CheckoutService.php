<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\WishlistRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    protected $productRepository;

    protected $wishlistRepository;

    protected $orderRepository;

    protected $couponService;

    public function __construct(
        ProductRepository $productRepository,
        WishlistRepository $wishlistRepository,
        OrderRepository $orderRepository,
        CouponService $couponService
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->orderRepository = $orderRepository;
        $this->couponService = $couponService;
    }

    public function getCheckoutData()
    {
        $user = Auth::user();
        $marqee = $this->productRepository->getAllMarqee();

        if ($user) {
            // 已登入：若 session 存有來賓購物車，先合併進 DB 再清除
            $this->mergeGuestCartToDb($user);
            // 從資料庫取得使用者的 want 清單
            $wantString = $this->wishlistRepository->getUserWantList($user->account);
        } else {
            // 未登入來賓：從 session 取得購物車
            $wantString = session('guest_cart', '');
        }

        // 抓出want欄位並處理
        $wantedIdsVarisntIds = $this->wishlistRepository->parseWantIds($wantString);
        // 取得想要的商品資訊
        $wantedProducts = $this->productRepository->findProductsByIds($wantedIdsVarisntIds);

        foreach ($wantedProducts as $wantedProduct) {
            // 品項從物件轉陣列
            $variants = [];
            $variants[] = $wantedProduct->variant;
            $wantedProduct['variant'] = $variants[0];

            // 簡化傳遞到前端的價格
            $wantedProduct['variant']['price'] = $wantedProduct->variant->use_oriprice ? $wantedProduct->variant->ori_price : $wantedProduct->variant->price;
            // 刪除key $wantedProduct['variant']['ori_price']
            unset($wantedProduct['variant']['ori_price']);
        }

        return [
            'user' => $user,
            'marqee' => $marqee,
            'wanted_product' => $wantedProducts,
        ];
    }

    /**
     * 若已登入且 session 有來賓購物車，則合併至 DB 的 want 欄位並清除 session
     */
    private function mergeGuestCartToDb($user)
    {
        $guestCart = session('guest_cart');
        if (empty($guestCart)) {
            return;
        }

        // 取得現有 DB 購物車字串並附加來賓購物車內容
        $currentWant = $this->wishlistRepository->getUserWantList($user->account) ?? '';
        $merged = rtrim($currentWant, ',').','.ltrim($guestCart, ',');
        $this->wishlistRepository->updateUserWantList($user->account, $merged);

        // 清除來賓 session 購物車
        session()->forget('guest_cart');
    }

    public function processCheckout(Request $request)
    {
        $itemIds = $request->input('selected_items', []);
        $quantities = $request->input('quantity', []);
        $couponCode = trim($request->input('coupon_code', ''));

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

        // 驗證優惠碼（若有填寫）
        if (! empty($couponCode)) {
            $couponValidation = $this->couponService->validateCode($couponCode);
            if (! $couponValidation['valid']) {
                return ['error' => '優惠碼無效或已過期，請重新確認', 'redirect' => 'check_show'];
            }
        }

        // 過濾空值
        $quantities = array_filter($quantities);

        // 取得使用者購物車（含 variantId）以查詢正確的 variant 價格
        if (Auth::check()) {
            $wantString = $this->wishlistRepository->getUserWantList(Auth::user()->account);
        } else {
            $wantString = session('guest_cart', '');
        }
        $cartPairs = $this->wishlistRepository->parseWantIds($wantString);

        // 建立 productId => variantId 對照表
        $productVariantMap = [];
        foreach ($cartPairs as $pair) {
            $parts = explode('-', $pair);
            if (isset($parts[0], $parts[1])) {
                $productVariantMap[$parts[0]] = $parts[1];
            }
        }

        // 依 itemIds 順序取得 variant 價格與對齊的數量
        $prices = [];
        $orderedQuantities = [];
        $variantIds = [];
        foreach ($itemIds as $productId) {
            $variantId = $productVariantMap[$productId] ?? null;
            $variantIds[] = $variantId;
            $prices[] = $variantId ? ((int) $this->productRepository->getVariantPrice($variantId)) : 0;
            $orderedQuantities[] = (int) ($quantities[$productId] ?? 1);
        }

        // 合併商品資訊並計算原始總價
        // purchased string 第一欄儲存 variantId（供 parsePurchasedProducts 正確查詢品項）
        $purchasedData = $this->preparePurchasedData($variantIds, $orderedQuantities, $prices);
        $subtotal = $this->calculateTotalPrice($purchasedData['merged_arr']);

        // 建立供分類折扣計算用的購物車項目陣列
        $cartItems = array_map(function ($productId, $quantity, $price) {
            return ['productId' => $productId, 'quantity' => $quantity, 'price' => $price];
        }, $itemIds, $orderedQuantities, $prices);

        // 計算折扣
        $discountResult = $this->couponService->calculateDiscount(
            (float) $subtotal,
            $cartItems,
            $couponCode ?: null
        );
        $discountAmount = $discountResult['discount_amount'];

        // 最終帳單金額（不低於 0）
        $totalPrice = max(0, $subtotal - $discountAmount);

        if (Auth::check()) {
            // 已登入：使用現有的使用者帳號
            $userAccount = Auth::user()->account;
        } else {
            // 未登入來賓：以 session ID 作為臨時帳號識別字串並存入 session
            $userAccount = 'guest_'.session()->getId();
            session(['guest_account' => $userAccount]);
        }

        // 建立購買記錄
        $this->orderRepository->createPurchaseOrder(
            $userAccount,
            $purchasedData['purchased_string'],
            $totalPrice
        );

        return ['success' => '儲存成功', 'redirect' => 'pay_show'];
    }

    private function preparePurchasedData($itemIds, $quantities, $prices)
    {
        // 使用 array_map 並合併三個陣列
        $mergedArr = array_map(function ($v1, $v2, $v3) {
            return $v1.','.$v2.','.$v3;
        }, $itemIds, $quantities, $prices);

        // 陣列轉字串
        $purchasedString = implode(';', $mergedArr);

        return [
            'merged_arr' => $mergedArr,
            'purchased_string' => $purchasedString,
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

    public function removeCartItem($productId)
    {
        if (Auth::check()) {
            // 已登入：從資料庫移除
            $user = Auth::user();
            $currentWantString = $this->wishlistRepository->getUserWantList($user->account);
            $currentWantIds = $this->wishlistRepository->parseWantIds($currentWantString);
            $newWantList = $this->wishlistRepository->removeItemsFromWantList($currentWantIds, [$productId]);
            $this->wishlistRepository->updateUserWantList($user->account, $newWantList);
        } else {
            // 未登入來賓：從 session 移除
            $guestCart = session('guest_cart', '');
            $guestIds = $this->wishlistRepository->parseWantIds($guestCart);
            $newGuestIds = $this->wishlistRepository->removeItemsFromWantList($guestIds, [$productId]);
            session(['guest_cart' => $newGuestIds.',']);
        }

        return ['success' => true];
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
