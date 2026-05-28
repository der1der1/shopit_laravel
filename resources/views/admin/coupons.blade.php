@extends('layouts.admin')

@section('title', '優惠券管理 - 管理後台')
@section('page-title', '優惠券管理')

@section('styles')
<style>
    .coupon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 10px;
    }

    .coupon-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 28px 24px 22px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: box-shadow 0.2s, transform 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .coupon-card:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        transform: translateY(-3px);
        text-decoration: none;
        color: inherit;
    }

    .coupon-card-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .icon-blue   { background: #e3f2fd; }
    .icon-green  { background: #e8f5e9; }
    .icon-purple { background: #f3e5f5; }
    .icon-orange { background: #fff3e0; }

    .coupon-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .coupon-card-desc {
        font-size: 14px;
        color: #7f8c8d;
        line-height: 1.6;
        flex: 1;
    }

    .coupon-card-features {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .coupon-card-features li {
        font-size: 13px;
        color: #555;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .feature-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot-blue   { background: #2196f3; }
    .dot-green  { background: #4caf50; }
    .dot-purple { background: #9c27b0; }
    .dot-orange { background: #ff9800; }

    .coupon-card-btn {
        margin-top: 8px;
        padding: 10px 0;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: opacity 0.2s;
        text-align: center;
        display: block;
    }

    .btn-blue   { background: #2196f3; color: #fff; }
    .btn-green  { background: #4caf50; color: #fff; }
    .btn-purple { background: #9c27b0; color: #fff; }
    .btn-orange { background: #ff9800; color: #fff; }

    .coupon-card-btn:hover { opacity: 0.85; }

    .page-intro {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 24px;
        padding: 14px 18px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }
</style>
@endsection

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h2 style="color: #2c3e50; margin: 0;">優惠券管理</h2>
</div>

<div class="page-intro">
    選擇下方功能區塊以設定對應的優惠或折扣方案，點擊卡片或按鈕進入編輯頁面。
</div>

<div class="coupon-grid">

    <!-- 1. 全站折扣 -->
    <a href="{{ route('admin.coupons.sitewide') }}" class="coupon-card">
        <div class="coupon-card-icon icon-blue">🌐</div>
        <div class="coupon-card-title">全站折扣</div>
        <div class="coupon-card-desc">對全站所有商品套用統一折扣比例，可設定生效時間區間，適用於節慶促銷活動。</div>
        <ul class="coupon-card-features">
            <li><span class="feature-dot dot-blue"></span>啟用 / 停用 Toggle</li>
            <li><span class="feature-dot dot-blue"></span>折扣數字輸入</li>
            <li><span class="feature-dot dot-blue"></span>折扣日期區間</li>
        </ul>
        <span class="coupon-card-btn btn-blue">進入編輯 →</span>
    </a>

    <!-- 2. 分類商品折扣 -->
    <a href="{{ route('admin.coupons.category') }}" class="coupon-card">
        <div class="coupon-card-icon icon-green">🏷️</div>
        <div class="coupon-card-title">分類商品折扣</div>
        <div class="coupon-card-desc">針對特定商品分類套用折扣，可多選分類並設定折扣比例與期間。</div>
        <ul class="coupon-card-features">
            <li><span class="feature-dot dot-green"></span>啟用 / 停用 Toggle</li>
            <li><span class="feature-dot dot-green"></span>商品分類多選勾選</li>
            <li><span class="feature-dot dot-green"></span>折扣數字輸入</li>
            <li><span class="feature-dot dot-green"></span>折扣日期區間</li>
        </ul>
        <span class="coupon-card-btn btn-green">進入編輯 →</span>
    </a>

    <!-- 3. 網紅折扣碼 -->
    <a href="{{ route('admin.coupons.influencer') }}" class="coupon-card">
        <div class="coupon-card-icon icon-purple">⭐</div>
        <div class="coupon-card-title">網紅折扣碼</div>
        <div class="coupon-card-desc">為合作網紅或 KOL 建立專屬折扣代碼，包含聯絡資訊、折扣比例與有效期限。</div>
        <ul class="coupon-card-features">
            <li><span class="feature-dot dot-purple"></span>姓名、連結、Email</li>
            <li><span class="feature-dot dot-purple"></span>折數與折扣代碼</li>
            <li><span class="feature-dot dot-purple"></span>折扣日期區間</li>
            <li><span class="feature-dot dot-purple"></span>啟用 / 停用 Toggle</li>
        </ul>
        <span class="coupon-card-btn btn-purple">進入編輯 →</span>
    </a>

    <!-- 4. 折扣碼設定 -->
    <a href="{{ route('admin.coupons.code') }}" class="coupon-card">
        <div class="coupon-card-icon icon-orange">🎟️</div>
        <div class="coupon-card-title">折扣碼設定</div>
        <div class="coupon-card-desc">建立通用折扣代碼，可自訂標題、折扣比例與使用期限，供顧客在結帳時輸入。</div>
        <ul class="coupon-card-features">
            <li><span class="feature-dot dot-orange"></span>標題與折扣代碼</li>
            <li><span class="feature-dot dot-orange"></span>折數設定</li>
            <li><span class="feature-dot dot-orange"></span>折扣日期區間</li>
            <li><span class="feature-dot dot-orange"></span>啟用 / 停用 Toggle</li>
        </ul>
        <span class="coupon-card-btn btn-orange">進入編輯 →</span>
    </a>

</div>
@endsection
