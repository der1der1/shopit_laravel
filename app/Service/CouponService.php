<?php

namespace App\Service;

use App\Models\CategoryDiscountModel;
use App\Models\CouponCodeModel;
use App\Models\CouponSettingModel;
use App\Models\InfluencerCouponModel;
use App\Models\SitewideDiscountModel;
use App\Repository\ProductRepository;

class CouponService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * 驗證優惠碼是否有效（供 AJAX 端點使用）。
     * 同時檢查 coupon_codes 與 influencer_coupons 兩張資料表。
     *
     * @return array{valid: bool, type?: string, code?: string, title?: string, discount_value?: int, message: string}
     */
    public function validateCode(string $code): array
    {
        $code = strtoupper(trim($code));

        if (empty($code)) {
            return ['valid' => false, 'message' => '請輸入優惠碼'];
        }

        // 先查一般優惠碼
        $coupon = CouponCodeModel::getActiveByCode($code);
        if ($coupon) {
            return [
                'valid' => true,
                'type' => 'coupon_code',
                'code' => $coupon->code,
                'title' => $coupon->title,
                'discount_value' => $coupon->discount_value,
                'message' => '優惠券有效：折扣 '.$coupon->discount_value.'%',
            ];
        }

        // 再查網紅推薦碼
        $influencer = InfluencerCouponModel::getActiveByCode($code);
        if ($influencer) {
            return [
                'valid' => true,
                'type' => 'influencer_coupon',
                'code' => $influencer->code,
                'title' => $influencer->name.' 推薦碼',
                'discount_value' => $influencer->discount_value,
                'message' => '推薦碼有效：折扣 '.$influencer->discount_value.'%',
            ];
        }

        return ['valid' => false, 'message' => '優惠碼無效或已過期'];
    }

    /**
     * 計算訂單總折扣金額。
     *
     * 規則：
     * - allow_stacking = false → 只套用使用者輸入的優惠碼折扣
     * - allow_stacking = true  → 套用所有有效折扣（全站、分類、優惠碼）
     *
     * @param  float  $subtotal  原始小計（未折扣）
     * @param  array  $cartItems  [['productId'=>int, 'quantity'=>int, 'price'=>float], ...]
     * @param  string|null  $couponCode  使用者輸入的優惠碼（可為 null）
     * @return array{discount_amount: int, coupon_info: array|null}
     */
    public function calculateDiscount(float $subtotal, array $cartItems, ?string $couponCode = null): array
    {
        $setting = CouponSettingModel::getSetting();
        $allowStacking = $setting->allow_stacking;

        $couponDiscountAmount = 0;
        $couponInfo = null;

        // 驗證並取得使用者輸入的優惠碼折扣
        if (! empty($couponCode)) {
            $validation = $this->validateCode($couponCode);
            if ($validation['valid']) {
                $couponDiscountAmount = (int) round($subtotal * $validation['discount_value'] / 100);
                $couponInfo = $validation;
            }
        }

        // 不允許疊加：只用輸入的優惠碼
        if (! $allowStacking) {
            return [
                'discount_amount' => $couponDiscountAmount,
                'coupon_info' => $couponInfo,
            ];
        }

        // 允許疊加：累加所有有效折扣
        $totalDiscount = $couponDiscountAmount;

        // 全站折扣
        $sitewide = SitewideDiscountModel::getActive();
        if ($sitewide) {
            $totalDiscount += (int) round($subtotal * $sitewide->discount_value / 100);
        }

        // 分類折扣
        $categoryDiscounts = CategoryDiscountModel::getActive();
        if ($categoryDiscounts->isNotEmpty()) {
            $totalDiscount += $this->calculateCategoryDiscount($cartItems, $categoryDiscounts);
        }

        return [
            'discount_amount' => $totalDiscount,
            'coupon_info' => $couponInfo,
        ];
    }

    /**
     * 計算符合分類折扣的商品折扣小計。
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $categoryDiscounts
     */
    private function calculateCategoryDiscount(array $cartItems, $categoryDiscounts): int
    {
        $total = 0;

        foreach ($cartItems as $item) {
            $product = $this->productRepository->findProductById($item['productId']);
            if (! $product) {
                continue;
            }

            $itemSubtotal = $item['quantity'] * $item['price'];

            foreach ($categoryDiscounts as $categoryDiscount) {
                $cats = $categoryDiscount->categories ?? [];
                if (in_array($product->category, $cats, true)) {
                    $total += (int) round($itemSubtotal * $categoryDiscount->discount_value / 100);
                    break; // 同一商品每個分類折扣只套用一次
                }
            }
        }

        return $total;
    }
}
