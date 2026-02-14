<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <link rel="stylesheet" href="{{ asset('itemPage.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $products->product_name }} - 商品詳情</title>
</head>

<body id="top">

    @include('template.header_template')

    <main class="product-page">
        <!-- 麵包屑導覽 -->
        <div class="breadcrumb-container">
            <nav class="breadcrumb">
                <a href="/">首頁</a>
                <span>/</span>
                <a href="/products">商品列表</a>
                <span>/</span>
                <span>{{ $products->product_name }}</span>
            </nav>
        </div>

        <div class="product-container">
            <!-- 左側：產品圖片區 -->
            <div class="product-images">
                <div class="main-image-wrapper">
                    <img id="mainProductImage" src="{{ asset($products->pic_dir) }}" alt="{{ $products->product_name }}">
                    <div class="image-badge">熱賣商品</div>
                </div>
                
                <!-- 縮圖區 - 目前使用相同圖片，可自行替換 -->
                <div class="thumbnail-gallery">
                    <div class="thumbnail active" onclick="changeMainImage('{{ asset($products->pic_dir) }}', this)">
                        <img src="{{ asset($products->pic_dir) }}" alt="圖片1">
                    </div>
                    <div class="thumbnail" onclick="changeMainImage('{{ asset($products->pic_dir) }}', this)">
                        <img src="{{ asset($products->pic_dir) }}" alt="圖片2">
                    </div>
                    <div class="thumbnail" onclick="changeMainImage('{{ asset($products->pic_dir) }}', this)">
                        <img src="{{ asset($products->pic_dir) }}" alt="圖片3">
                    </div>
                    <div class="thumbnail" onclick="changeMainImage('{{ asset($products->pic_dir) }}', this)">
                        <img src="{{ asset($products->pic_dir) }}" alt="圖片4">
                    </div>
                </div>
            </div>

            <!-- 右側：產品資訊區 -->
            <div class="product-info">
                <h1 class="product-title">{{ $products->product_name }}</h1>
                
                <!-- 產品評分 -->
                <div class="product-rating">
                    <div class="stars">
                        <span class="star filled">★</span>
                        <span class="star filled">★</span>
                        <span class="star filled">★</span>
                        <span class="star filled">★</span>
                        <span class="star half">★</span>
                    </div>
                    <span class="rating-text">(4.5分 | 128則評價)</span>
                </div>

                <!-- 價格區 -->
                <div class="product-price">
                    <div class="price-main">
                        <span class="currency">NT$</span>
                        <span class="price-amount">1,280</span>
                        <!-- 提示：請從資料庫新增價格欄位 -->
                    </div>
                    <div class="price-original">
                        <span>原價：</span>
                        <span class="strikethrough">NT$ 1,580</span>
                    </div>
                </div>

                <!-- 庫存狀態 -->
                <div class="stock-status">
                    <span class="stock-label">庫存狀態：</span>
                    <span class="stock-value in-stock">現貨供應</span>
                </div>

                <!-- 產品簡述 -->
                <div class="product-short-description">
                    <p>{!! Str::limit(strip_tags($products->description), 150) !!}</p>
                </div>

                <!-- 數量選擇器 -->
                <form method="POST" action="{{ route('want') }}" enctype="multipart/form-data" id="addToCartForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $products->id }}">
                    
                    <div class="quantity-selector">
                        <label for="quantity">數量：</label>
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn minus" onclick="decreaseQuantity()">−</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" readonly>
                            <button type="button" class="qty-btn plus" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>

                    <!-- 購物按鈕區 -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-add-cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M9 2L7.17 4H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3.17L15 2H9z"/>
                                <circle cx="12" cy="13" r="3"/>
                            </svg>
                            加入購物車
                        </button>
                        <button type="button" class="btn-buy-now">立即購買</button>
                        <button type="button" class="btn-wishlist">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- 商品特色標籤 -->
                <div class="product-features">
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>品質保證</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>快速出貨</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/>
                        </svg>
                        <span>安全購物</span>
                    </div>
                </div>

                <!-- 分享按鈕 -->
                <div class="social-share">
                    <span>分享：</span>
                    <button class="share-btn facebook">Facebook</button>
                    <button class="share-btn line">LINE</button>
                    <button class="share-btn link">複製連結</button>
                </div>
            </div>
        </div>

        <!-- 產品詳細資訊 Tabs -->
        <div class="product-details-tabs">
            <div class="tabs-navigation">
                <button class="tab-btn active" onclick="switchTab('description')">商品描述</button>
                <button class="tab-btn" onclick="switchTab('specs')">規格說明</button>
                <button class="tab-btn" onclick="switchTab('shipping')">配送方式</button>
                <button class="tab-btn" onclick="switchTab('reviews')">顧客評價</button>
            </div>

            <div class="tabs-content">
                <div id="description" class="tab-pane active">
                    <div class="description-content">
                        {!! $products->description !!}
                        
                        <div class="notice-box">
                            <h3>注意事項</h3>
                            <ul>
                                <li>商品圖片僅供參考，實際商品以收到為準</li>
                                <li>因拍攝光線及螢幕設定，商品色澤可能略有差異</li>
                                <li>若有任何問題，歡迎聯繫客服</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="specs" class="tab-pane">
                    <div class="specs-content">
                        <table class="specs-table">
                            <tbody>
                                <tr>
                                    <td class="spec-label">品牌</td>
                                    <td class="spec-value">待填入品牌資訊</td>
                                </tr>
                                <tr>
                                    <td class="spec-label">型號</td>
                                    <td class="spec-value">待填入型號資訊</td>
                                </tr>
                                <tr>
                                    <td class="spec-label">產地</td>
                                    <td class="spec-value">待填入產地資訊</td>
                                </tr>
                                <tr>
                                    <td class="spec-label">重量/容量</td>
                                    <td class="spec-value">待填入規格資訊</td>
                                </tr>
                                <tr>
                                    <td class="spec-label">保存期限</td>
                                    <td class="spec-value">待填入效期資訊</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="shipping" class="tab-pane">
                    <div class="shipping-content">
                        <h3>配送資訊</h3>
                        <div class="shipping-option">
                            <h4>🚚 宅配到府</h4>
                            <p>本島免運費，外島酌收運費 NT$100</p>
                            <p>預計 2-3 個工作天送達</p>
                        </div>
                        <div class="shipping-option">
                            <h4>🏪 超商取貨</h4>
                            <p>支援 7-11、全家、萊爾富取貨</p>
                            <p>預計 3-5 個工作天到店</p>
                        </div>
                    </div>
                </div>

                <div id="reviews" class="tab-pane">
                    <div class="reviews-content">
                        <div class="reviews-summary">
                            <div class="overall-rating">
                                <div class="rating-number">4.5</div>
                                <div class="rating-stars">★★★★☆</div>
                                <div class="rating-count">基於 128 則評價</div>
                            </div>
                        </div>
                        
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name">王小明</span>
                                <span class="review-date">2024/01/15</span>
                            </div>
                            <div class="review-rating">★★★★★</div>
                            <p class="review-text">商品品質很好，包裝精美，非常滿意！</p>
                        </div>
                        
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name">李小華</span>
                                <span class="review-date">2024/01/10</span>
                            </div>
                            <div class="review-rating">★★★★☆</div>
                            <p class="review-text">整體不錯，配送速度快，值得推薦。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 相關商品推薦 -->
        <div class="related-products-section">
            <h2 class="section-title">您可能也喜歡</h2>
            <div class="related-products-grid">
                @foreach ($few_products as $few_productss)
                    <a href="{{ route('itemPage', ['id' => $few_productss->id]) }}" class="product-card">
                        <div class="product-card-image">
                            <img src="{{ asset($few_productss->pic_dir) }}" alt="{{ $few_productss->product_name }}">
                            <div class="product-badge">HOT</div>
                        </div>
                        <div class="product-card-info">
                            <h3 class="product-card-title">{{ $few_productss->product_name }}</h3>
                            <p class="product-card-description">{{ Str::limit(strip_tags($few_productss->description), 60) }}</p>
                            <div class="product-card-footer">
                                <span class="product-card-price">NT$ 999</span>
                                <button class="product-card-btn">查看詳情</button>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </main>

    @include('template.footer_template')

    <!-- 回到頂部按鈕 -->
    <span id="toTop">
        <a href="#top">
            <img src="{{ asset('img/icon/arrow-up.svg') }}" alt="回到頂部" title="回到頂部">
        </a>
    </span>

    <!-- JavaScript -->
    <script>
        // 切換主圖片
        function changeMainImage(imageSrc, thumbnail) {
            document.getElementById('mainProductImage').src = imageSrc;
            
            // 移除所有縮圖的 active 類
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            
            // 添加 active 類到點擊的縮圖
            thumbnail.classList.add('active');
        }

        // 增加數量
        function increaseQuantity() {
            const input = document.getElementById('quantity');
            let value = parseInt(input.value);
            if (value < 99) {
                input.value = value + 1;
            }
        }

        // 減少數量
        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        }

        // 切換 Tab
        function switchTab(tabName) {
            // 隱藏所有 tab 內容
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // 移除所有按鈕的 active 類
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // 顯示選中的 tab
            document.getElementById(tabName).classList.add('active');
            
            // 添加 active 類到對應按鈕
            event.target.classList.add('active');
        }

        // 回到頂部按鈕顯示/隱藏
        window.addEventListener('scroll', function() {
            const toTopBtn = document.getElementById('toTop');
            if (window.pageYOffset > 300) {
                toTopBtn.style.opacity = '1';
                toTopBtn.style.visibility = 'visible';
            } else {
                toTopBtn.style.opacity = '0';
                toTopBtn.style.visibility = 'hidden';
            }
        });
    </script>

</body>
</html>
