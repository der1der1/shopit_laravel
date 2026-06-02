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
        margin-bottom: 16px;
        padding: 14px 18px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }

    /* ── 疊加設定卡片 ── */
    .stacking-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 16px 22px;
        margin-bottom: 28px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        gap: 16px;
    }

    .stacking-card-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .stacking-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #fff8e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .stacking-card-text h4 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        color: #2c3e50;
    }

    .stacking-card-text p {
        margin: 0;
        font-size: 13px;
        color: #7f8c8d;
        line-height: 1.5;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .toggle-slider {
        position: absolute;
        inset: 0;
        background: #ccc;
        border-radius: 28px;
        cursor: pointer;
        transition: background 0.25s;
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        left: 4px;
        top: 4px;
        transition: transform 0.25s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .toggle-switch input:checked + .toggle-slider {
        background: #27ae60;
    }

    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(24px);
    }

    .stacking-status {
        font-size: 13px;
        font-weight: 600;
        min-width: 36px;
        text-align: right;
    }

    .stacking-status.on  { color: #27ae60; }
    .stacking-status.off { color: #aaa; }

    .stacking-meta {
        font-size: 12px;
        color: #aaa;
        white-space: nowrap;
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

{{-- 優惠疊加設定 --}}
<div class="stacking-card">
    <div class="stacking-card-left">
        <div class="stacking-card-icon">🔀</div>
        <div class="stacking-card-text">
            <h4>是否允許優惠疊加使用？</h4>
            <p>開啟後，顧客可同時套用多種優惠（全站折扣、分類折扣、折扣碼等）；關閉則每筆訂單僅允許套用單一優惠。</p>
            @if($couponSetting->updated_at && $couponSetting->updated_by)
                <p class="stacking-meta">
                    最後更新：{{ $couponSetting->updated_at->format('Y-m-d H:i') }}
                    ／{{ optional($couponSetting->updatedByUser)->name ?? '未知管理員' }}
                </p>
            @endif
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <span class="stacking-status {{ $couponSetting->allow_stacking ? 'on' : 'off' }}"
              id="stackingStatusText">
            {{ $couponSetting->allow_stacking ? '開啟' : '關閉' }}
        </span>
        <label class="toggle-switch" title="切換優惠疊加">
            <input type="checkbox" id="stackingToggle"
                   {{ $couponSetting->allow_stacking ? 'checked' : '' }}>
            <span class="toggle-slider"></span>
        </label>
    </div>
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

@section('scripts')
<script>
document.getElementById('stackingToggle').addEventListener('change', function () {
    const checkbox   = this;
    const statusText = document.getElementById('stackingStatusText');
    const original   = checkbox.checked ? false : true; // 原始值（反向）

    checkbox.disabled = true;

    fetch('{{ route('admin.coupons.settings.stacking') }}', {
        method : 'POST',
        headers: {
            'Content-Type'    : 'application/json',
            'X-CSRF-TOKEN'    : '{{ csrf_token() }}',
            'Accept'          : 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(res => {
        if (!res.ok) throw new Error('請求失敗');
        return res.json();
    })
    .then(data => {
        const isOn = data.allow_stacking;
        checkbox.checked        = isOn;
        statusText.textContent  = isOn ? '開啟' : '關閉';
        statusText.className    = 'stacking-status ' + (isOn ? 'on' : 'off');
    })
    .catch(() => {
        // 還原狀態
        checkbox.checked        = original;
        statusText.textContent  = original ? '開啟' : '關閉';
        statusText.className    = 'stacking-status ' + (original ? 'on' : 'off');
        alert('切換失敗，請重試。');
    })
    .finally(() => {
        checkbox.disabled = false;
    });
});
</script>
@endsection
