<!DOCTYPE html>
<html lang="zh-TW">

@include('template.head_template')

<body id="top">
    <!-- 訊息提示 -->
    @if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
    @elseif(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
    @endif

    @include('template.header_template')

    <!-- 主要內容區域 -->
    <div class="shop-page-container">
        
        <!-- 側邊欄分類導航 -->
        <aside class="sidebar-nav">
            <div class="sidebar-section">
                <h3 class="sidebar-title">商品分類</h3>
                <ul class="category-list">
                    <li class="category-item">
                        <a href="{{ route('home') }}" class="category-link {{ !request('search') ? 'active' : '' }}">
                            <span class="category-icon">📦</span>
                            <span>全部商品</span>
                            <span class="category-count">({{ $allProducts->count() }})</span>
                        </a>
                    </li>
                    @foreach ($products_category as $products_categorys)
                    <li class="category-item">
                        <a href="{{ route('home_with_search', ['search' => $products_categorys->category]) }}" 
                           class="category-link {{ request('search') == $products_categorys->category ? 'active' : '' }}">
                            <span class="category-icon">🏷️</span>
                            <span>{{ $products_categorys->category }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- 搜尋框 -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">商品搜尋</h3>
                <form action="{{ route('toHome_words_search') }}" method="post" class="sidebar-search-form">
                    @csrf
                    <div class="search-input-group">
                        <input type="text" name="search_word" value="{{ request('search') }}" 
                               placeholder="搜尋商品..." class="sidebar-search-input">
                        <button type="submit" class="sidebar-search-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 最新商品 -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">最新上架</h3>
                <div class="sidebar-products">
                    @foreach (\App\Models\productsModel::orderBy('updated_at', 'desc')->take(4)->get() as $newestProduct)
                        <a href="{{ route('itemPage', ['id' => $newestProduct->id]) }}" class="sidebar-product-item">
                            <img src="{{ asset($newestProduct->pic_dir) }}" alt="{{ $newestProduct->product_name }}" class="sidebar-product-img">
                            <div class="sidebar-product-info">
                                <h4 class="sidebar-product-name">{{ Str::limit($newestProduct->product_name, 30) }}</h4>
                                <p class="sidebar-product-price">NT$ {{ $newestProduct->price }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- 價格篩選 -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">價格區間</h3>
                <div class="price-filter">
                    <div class="price-range-labels">
                        <span>NT$ 0</span>
                        <span>NT$ 5000+</span>
                    </div>
                    <p class="filter-note">* 價格篩選功能待實作</p>
                </div>
            </div>
        </aside>

        <!-- 主要內容區 -->
        <main class="main-content">
            
            <!-- 輪播廣告橫幅 -->
            <div class="banner-carousel">
                <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="2"></button>
                        <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="3"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="4000">
                            <img src="{{ asset('img/pictureTarget/ad1.png') }}" class="d-block w-100" alt="廣告1">
                        </div>
                        <div class="carousel-item" data-bs-interval="4000">
                            <img src="{{ asset('img/pictureTarget/ad2.png') }}" class="d-block w-100" alt="廣告2">
                        </div>
                        <div class="carousel-item" data-bs-interval="4000">
                            <img src="{{ asset('img/pictureTarget/ad3.png') }}" class="d-block w-100" alt="廣告3">
                        </div>
                        <div class="carousel-item" data-bs-interval="4000">
                            <img src="{{ asset('img/pictureTarget/ad4.png') }}" class="d-block w-100" alt="廣告4">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>

            <!-- 工具列：排序與顯示選項 -->
            <div class="products-toolbar">
                <div class="toolbar-left">
                    <span class="products-count">共 {{ $allProducts->count() }} 件商品</span>
                </div>
                
                <div class="toolbar-right">
                    <!-- 排序選擇 -->
                    <div class="sort-selector">
                        <label class="sort-label">排序：</label>
                        <select class="sort-dropdown" onchange="window.location.href=this.value">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => '']) }}" {{ !request('sort') ? 'selected' : '' }}>預設排序</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>價格：低到高</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>價格：高到低</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>最新上架</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" {{ request('sort') == 'oldest' ? 'selected' : '' }}>最舊商品</option>
                        </select>
                    </div>

                    <!-- 顯示行數選擇 -->
                    <div class="grid-selector">
                        <a href="{{ request()->fullUrlWithQuery(['grid' => 2]) }}" 
                           class="grid-btn {{ $gride == 2 ? 'active' : '' }}" 
                           title="2行顯示">
                            <div class="grid-icon">
                                <div class="grid-line">
                                    <div class="grid-square"></div>
                                    <div class="grid-square"></div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['grid' => 3]) }}" 
                           class="grid-btn {{ $gride == 3 ? 'active' : '' }}" 
                           title="3行顯示">
                            <div class="grid-icon">
                                <div class="grid-line">
                                    <div class="grid-square"></div>
                                    <div class="grid-square"></div>
                                    <div class="grid-square"></div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['grid' => 4]) }}" 
                           class="grid-btn {{ $gride == 4 ? 'active' : '' }}" 
                           title="4行顯示">
                            <div class="grid-icon">
                                <div class="grid-line">
                                    <div class="grid-square"></div>
                                    <div class="grid-square"></div>
                                    <div class="grid-square"></div>
                                    <div class="grid-square"></div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 商品網格 -->
            <div class="products-grid grid-cols-{{ $gride }}">
                @foreach ($allProducts as $product)
                    <div class="product-card">
                        <a href="{{ route('itemPage', ['id' => $product->id]) }}" class="product-card-link">
                            <div class="product-image-wrapper">
                                <img src="{{ asset($product->pic_dir) }}" alt="{{ $product->product_name }}" class="product-image">
                                <div class="product-overlay">
                                    <span class="overlay-text">查看詳情</span>
                                </div>
                                @if($loop->iteration % 3 == 0)
                                    <span class="product-badge hot">HOT</span>
                                @elseif($loop->iteration % 5 == 0)
                                    <span class="product-badge new">NEW</span>
                                @endif
                            </div>
                            <div class="product-card-body">
                                <h3 class="product-name">{{ $product->product_name }}</h3>
                                <p class="product-description">{{ Str::limit(strip_tags($product->description), 50) }}</p>
                                <div class="product-rating">
                                    <span class="stars">★★★★☆</span>
                                    <span class="rating-count">(4.5)</span>
                                </div>
                                <div class="product-price-row">
                                    @if(isset($product->ori_price) && $product->ori_price > $product->price)
                                        <span class="price-original">NT$ {{ $product->ori_price }}</span>
                                    @endif
                                    <span class="price-current">NT$ {{ $product->price }}</span>
                                </div>
                                <button class="btn-add-to-cart">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                        <line x1="3" y1="6" x2="21" y2="6"/>
                                        <path d="M16 10a4 4 0 0 1-8 0"/>
                                    </svg>
                                    加入購物車
                                </button>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- 推薦商品區 -->
            @if($few_products && $few_products->count() > 0)
            <div class="recommended-section">
                <div class="section-header">
                    <div class="section-title-wrapper">
                        <img src="{{ asset('img/good3.png') }}" class="section-icon" alt="推薦">
                        <h2 class="section-title">為您推薦</h2>
                    </div>
                    <p class="section-subtitle">精選優質商品，您可能也喜歡</p>
                </div>
                
                <div class="recommended-carousel">
                    @foreach ($few_products as $recommendedProduct)
                        <div class="recommended-card">
                            <a href="{{ route('itemPage', ['id' => $recommendedProduct->id]) }}">
                                <div class="recommended-image">
                                    <img src="{{ asset($recommendedProduct->pic_dir) }}" alt="{{ $recommendedProduct->product_name }}">
                                </div>
                                <div class="recommended-info">
                                    <h4 class="recommended-name">{{ Str::limit($recommendedProduct->product_name, 30) }}</h4>
                                    <div class="recommended-rating">
                                        <span class="stars-small">★★★★☆</span>
                                    </div>
                                    <div class="recommended-price">
                                        @if(isset($recommendedProduct->ori_price) && $recommendedProduct->ori_price > $recommendedProduct->price)
                                            <span class="price-old">NT$ {{ $recommendedProduct->ori_price }}</span>
                                        @endif
                                        <span class="price-new">NT$ {{ $recommendedProduct->price }}</span>
                                    </div>
                                    <p class="recommended-desc">{{ Str::limit(strip_tags($recommendedProduct->description), 50) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </main>
    </div>

    @include('template.footer_template')

    <!-- 浮動按鈕 -->
    <a id="chatbot" href="{{ route('testApi_show') }}" title="AI 客服">
        <img id="chatbot_img" src="{{ asset('img/icon/chatbot.png') }}" alt="AI 客服">
    </a>
    
    <span id="toTop">
        <a href="#top">
            <img src="{{ asset('img/icon/arrow-up.svg') }}" alt="回到頂部" title="回到頂部">
        </a>
    </span>

    <script>
        // AOS 動畫初始化
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 500,
                once: true
            });
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
