<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>結帳 - Checkout</title>

    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('pay.css') }}">

    <!-- Map API Styles -->
    <style>
        #map { 
            height: 300px; 
            width: 100%; 
            border-radius: 12px;
            border: 2px solid var(--border-color);
        }
    </style>
</head>

@include('template.header_template')

<body>
    <div class="checkout-page-container">
        
        <!-- 結帳頁面標題 -->
        <div class="page-header">
            <h1 class="page-title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V4l-8-2-8 2v8c0 6 8 10 8 10z"/>
                </svg>
                安全結帳
            </h1>
            <p class="page-subtitle">請填寫配送資訊並確認訂單</p>
        </div>

        <main class="checkout-main">
            
            <!-- 左側：配送與付款資訊 -->
            <div class="checkout-left-section">
                
                <!-- 1. 購買商品清單 -->
                <div class="section-card" data-aos="fade-up" data-aos-duration="500">
                    <div class="card-header">
                        <h2 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            訂購商品
                        </h2>
                        <span class="item-count">{{ count($products) }} 件商品</span>
                    </div>
                    <div class="product-list">
                        @foreach ($products as $product)
                            <div class="product-item">
                                <div class="product-image">
                                    <img src="{{ asset($product['pic_dir']) }}" alt="{{ $product['product_name'] ?? '' }}">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">{{ $product['product_name'] ?? '' }}</h3>
                                    <div class="product-quantity">數量：{{ $product['num'] ?? '' }}</div>
                                </div>
                                <div class="product-pricing">
                                    <div class="unit-price">NT$ {{ $product['price'] ?? '' }}</div>
                                    <div class="item-subtotal">NT$ {{ ($product['price'] ?? 0) * ($product['num'] ?? 0) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 2. 配送方式選擇 -->
                <div class="section-card" data-aos="fade-up" data-aos-duration="600">
                    <div class="card-header">
                        <h2 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                            配送方式
                        </h2>
                    </div>
                    <div class="delivery-options">
                        <button type="button" class="delivery-option" id="market" onclick="showDeliveryOption('store')">
                            <div class="option-icon">
                                <img src="{{ asset('img/icon/shop.png') }}" alt="超商取貨">
                            </div>
                            <div class="option-details">
                                <div class="option-title">超商取貨</div>
                                <div class="option-desc">7-11 門市取貨</div>
                                <div class="option-fee">運費 NT$ 60</div>
                            </div>
                            <div class="option-check">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </button>
                        
                        <button type="button" class="delivery-option" id="express" onclick="showDeliveryOption('home')">
                            <div class="option-icon">
                                <img src="{{ asset('img/icon/home.png') }}" alt="宅配到府">
                            </div>
                            <div class="option-details">
                                <div class="option-title">宅配到府</div>
                                <div class="option-desc">快速配送到指定地址</div>
                                <div class="option-fee">運費 NT$ 80</div>
                            </div>
                            <div class="option-check">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- 超商取貨表單 -->
                    <form method="POST" action="{{ route('pay_to_shop') }}" id="storeForm" class="delivery-form">
                        @csrf
                        <div class="form-group">
                            <label for="store-select">選擇取貨門市</label>
                            <select name="store" id="store-select" class="form-select">
                                <option value="新竹中山店">新竹中山店 - 新竹市東區中山路176號</option>
                                <option value="大安仁愛店">大安仁愛店 - 台北市大安區仁愛路四段345號</option>
                                <option value="中正忠孝店">中正忠孝店 - 台北市中正區忠孝西路一段50號</option>
                                <option value="西屯文心店">西屯文心店 - 台中市西屯區文心路三段100號</option>
                                <option value="南屯大墩店">南屯大墩店 - 台中市南屯區大墩路一段766號</option>
                                <option value="前鎮中華店">前鎮中華店 - 高雄市前鎮區中華五路596號</option>
                                <option value="苓雅中華店">苓雅中華店 - 高雄市苓雅區中華四路282號</option>
                                <option value="板橋文化店">板橋文化店 - 新北市板橋區文化路一段128號</option>
                                <option value="新店中正店">新店中正店 - 新北市新店區中正路151號</option>
                                <option value="桃園中正店">桃園中正店 - 桃園市桃園區中正路550號</option>
                                <option value="中壢中北店">中壢中北店 - 桃園市中壢區中北路二段466號</option>
                            </select>
                            <button type="submit" class="btn-submit">確認取貨門市</button>
                        </div>
                    </form>

                    <!-- 宅配到家表單 -->
                    <form method="POST" action="{{ route('pay_to_home') }}" id="homeForm" class="delivery-form">
                        @csrf
                        <div class="form-group">
                            <label for="map-address-input">配送地址</label>
                            <input type="text" 
                                   id="map-address-input" 
                                   name="address" 
                                   class="form-input"
                                   placeholder="請輸入完整配送地址..."
                                   value="{{ $ppl_info->to_address ?? '' }}">
                            <button type="submit" class="btn-submit">確認配送地址</button>
                        </div>
                        
                        <!-- Google Map -->
                        <div class="map-container">
                            <div id="map"></div>
                        </div>
                    </form>
                </div>

                <!-- 3. 收件資訊 -->
                <div class="section-card" data-aos="fade-up" data-aos-duration="700">
                    <div class="card-header">
                        <h2 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            收件資訊
                        </h2>
                    </div>
                    
                    <form method="POST" action="{{ route('pay_name') }}" class="info-form">
                        @csrf
                        <div class="form-row">
                            <label for="name-input">收件人姓名</label>
                            <div class="input-group">
                                <input type="text" 
                                       id="name-input"
                                       name="name_input" 
                                       class="form-input"
                                       value="{{ $ppl_info->name ?? '王大明' }}">
                                <button type="submit" class="btn-confirm">確認</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 4. 付款資訊 -->
                <div class="section-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="card-header">
                        <h2 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            付款方式
                        </h2>
                    </div>
                    
                    <form method="POST" action="{{ route('pay_account') }}" class="info-form">
                        @csrf
                        <div class="form-row">
                            <label for="account-input">扣款帳號</label>
                            <div class="input-group">
                                <input type="text" 
                                       id="account-input"
                                       name="account_input" 
                                       class="form-input"
                                       value="{{ $ppl_info->bank_account ?? '0191227-0082229' }}">
                                <button type="submit" class="btn-confirm">確認</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 右側：訂單摘要 -->
            <div class="checkout-right-section">
                <div class="summary-sticky" data-aos="fade-left" data-aos-duration="500">
                    
                    <!-- 訂單摘要卡片 -->
                    <div class="summary-card">
                        <h2 class="summary-title">訂單摘要</h2>
                        
                        <div class="summary-section">
                            <h3 class="section-subtitle">收件資訊</h3>
                            <div class="info-row">
                                <span class="info-label">收件人：</span>
                                <span class="info-value">{{ $purchased->name ?? '尚未填寫' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">訂購帳號：</span>
                                <span class="info-value">{{ $purchased->account ?? $purchased->email ?? '尚未填寫' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">扣款帳號：</span>
                                <span class="info-value">{{ $purchased->bank_account ?? '尚未填寫' }}</span>
                            </div>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-section">
                            <h3 class="section-subtitle">配送資訊</h3>
                            @if ($purchased->shop1_addr2 == "1")
                                <div class="info-row">
                                    <span class="info-label">配送方式：</span>
                                    <span class="info-value highlight">超商取貨</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">取貨門市：</span>
                                    <span class="info-value">{{ $purchased->to_shop }}</span>
                                </div>
                            @elseif ($purchased->shop1_addr2 == "2")
                                <div class="info-row">
                                    <span class="info-label">配送方式：</span>
                                    <span class="info-value highlight">宅配到府</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">配送地址：</span>
                                    <span class="info-value">{{ $purchased->to_address }}</span>
                                </div>
                            @else
                                <div class="info-row">
                                    <span class="info-label">配送方式：</span>
                                    <span class="info-value incomplete">請選擇配送方式</span>
                                </div>
                            @endif
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-section">
                            <h3 class="section-subtitle">商品明細</h3>
                            <div class="items-table">
                                <div class="table-header">
                                    <span>商品</span>
                                    <span>數量</span>
                                    <span>單價</span>
                                    <span>小計</span>
                                </div>
                                @foreach ($products as $product)
                                    <div class="table-row">
                                        <span class="item-name">{{ Str::limit($product['product_name'] ?? '', 12) }}</span>
                                        <span class="item-qty">{{ $product['num'] ?? '' }}</span>
                                        <span class="item-price">NT$ {{ $product['price'] ?? '' }}</span>
                                        <span class="item-total">NT$ {{ ($product['num'] ?? 0) * ($product['price'] ?? 0) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="price-summary">
                            <div class="price-row subtotal">
                                <span>商品小計</span>
                                <span>NT$ {{ $purchased->bill ?? 0 }}</span>
                            </div>
                            <div class="price-row">
                                <span>運費</span>
                                <span>NT$ 60</span>
                            </div>
                            <div class="price-row total">
                                <span>訂單總額</span>
                                <span>NT$ {{ ($purchased->bill ?? 0) + 60 }}</span>
                            </div>
                        </div>

                        <!-- 確認結帳按鈕 -->
                        <form method="POST" action="{{ route('pay_confirm') }}">
                            @csrf
                            <input type="hidden" name="name" value="{{ $purchased->name ?? '' }}">
                            <input type="hidden" name="bank_account" value="{{ $purchased->bank_account ?? '' }}">
                            <input type="hidden" name="shop1_addr2" value="{{ $purchased->shop1_addr2 ?? '' }}">
                            <input type="hidden" name="to_shop" value="{{ $purchased->to_shop ?? '' }}">
                            <input type="hidden" name="to_address" value="{{ $purchased->to_address ?? '' }}">
                            
                            <button type="submit" class="btn-checkout">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V4l-8-2-8 2v8c0 6 8 10 8 10z"/>
                                </svg>
                                確認並結帳
                            </button>
                        </form>

                        <!-- 安全提示 -->
                        <div class="security-note">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="16" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                            </svg>
                            您的付款資訊將透過加密連線安全傳輸
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        AOS.init();
        
        // 配送方式切換功能
        function showDeliveryOption(type) {
            const storeForm = document.getElementById('storeForm');
            const homeForm = document.getElementById('homeForm');
            const marketBtn = document.getElementById('market');
            const expressBtn = document.getElementById('express');
            
            if (type === 'store') {
                storeForm.style.display = 'block';
                homeForm.style.display = 'none';
                marketBtn.classList.add('active');
                expressBtn.classList.remove('active');
            } else if (type === 'home') {
                storeForm.style.display = 'none';
                homeForm.style.display = 'block';
                marketBtn.classList.remove('active');
                expressBtn.classList.add('active');
            }
        }
        
        // 初始化顯示第一個選項（超商取貨）
        document.addEventListener('DOMContentLoaded', function() {
            showDeliveryOption('store');
        });
    </script>

    <!-- Google Map API -->
    @include('template.map_api')
    
</body>

@include('template.footer_template')

</html>
