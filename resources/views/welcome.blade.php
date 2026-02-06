<!DOCTYPE html>
<html lang="en">

@include('template.head_template')

<body id="top">
    <!-- 先跑要給使用者的訊息 -->
    @if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
    @elseif(session(key: 'success'))
    <script>
        alert("{{ session('success') }}");
    </script>
    @endif

    @include('template.header_template')

    <!-- Modal for Storage Link Error -->
    <!-- @include('template.error_msg') -->

    <!-- 普通螢幕，顯示的aside(小螢幕點開顯示) -->
    <div id="nav">
        <!-- 第一個分類是回到主頁 -->
        <a href="{{ route('home') }}">
            全部
        </a>
        @foreach ($products_category as $products_categorys)
        <a href="{{ route('home_with_search', ['search' => $products_categorys->category]) }}">
            {{ $products_categorys->category }}
        </a>
        @endforeach
    </div>
    <!-- <main> -->
    <!-- 下分三大類greatPromotion、interested、normal -->

    <div id="greatPromotion">

        <!-- Retrieved the div code from Bootstrap -->
        <!-- 廣告圖display -->
        <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel"
            data-aos="zoom-in" data-aos-duration="400">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="4000">
                    <img src="{{ asset('img/pictureTarget/ad1.png') }}" class="d-block w-100">
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="{{ asset('img/pictureTarget/ad2.png') }}" class="d-block w-100">
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="{{ asset('img/pictureTarget/ad3.png') }}" class="d-block w-100">
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="{{ asset('img/pictureTarget/ad4.png') }}" class="d-block w-100">
                </div>
            </div>
            <button class="carousel-control-prev" type="button"
                data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button"
                data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
    </div>
    <!-- Bootstrap end -->
    </div>
    
    <!-- Grid Layout Selector -->
    <div id="grid-selector">        
        <span class="grid-label">顯示行數:</span>
        
        <a href="{{ request()->fullUrlWithQuery(['grid' => 2]) }}" class="grid-btn {{ $gride == 2 ? 'active' : '' }}" title="2行顯示">
            <div class="grid-icon-line">
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
            </div>
            <div class="grid-icon-line">
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
            </div>
        </a>
        
        <a href="{{ request()->fullUrlWithQuery(['grid' => 3]) }}" class="grid-btn {{ $gride == 3 ? 'active' : '' }}" title="3行顯示">
            <div class="grid-icon-line">
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
            </div>
            <div class="grid-icon-line">
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
            </div>
        </a>
        
        <a href="{{ request()->fullUrlWithQuery(['grid' => 4]) }}" class="grid-btn {{ $gride == 4 ? 'active' : '' }}" title="4行顯示">
            <div class="grid-icon-line">
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
            </div>
            <div class="grid-icon-line">
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
                <div class="grid-icon-square"></div>
            </div>
        </a>
        
    </div>
    
    <!-- Sort Selector -->
    <div id="sort-selector">
        <span class="sort-label">排序:</span>
        
        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" 
           class="sort-btn {{ request()->query('sort') == 'price_asc' ? 'active' : '' }}" 
           title="低價優先">
            低價優先
        </a>
        
        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" 
           class="sort-btn {{ request()->query('sort') == 'price_desc' ? 'active' : '' }}" 
           title="高價優先">
            高價優先
        </a>
        
        <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" 
           class="sort-btn {{ request()->query('sort') == 'newest' ? 'active' : '' }}" 
           title="最近更新">
            最近更新
        </a>
        
        <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" 
           class="sort-btn {{ request()->query('sort') == 'oldest' ? 'active' : '' }}" 
           title="最舊商品">
            最舊商品
        </a>
    </div>
    
    <div id="normal">
        <div>
            @foreach ($allProducts->chunk($gride) as $productRow)
            <div class="product-row">
                @foreach ($productRow as $allProductss)
                <a href="{{ route('itemPage', ['id' => $allProductss->id]) }}" style="margin-right: 10px; margin-bottom: 10px;">
                    <button id="item">
                        <img src="{{ asset($allProductss->pic_dir) }}" title="優質特賣" width="160px"
                            height="190px">
                        <div class="product_info"> {{ $allProductss->product_name }}</div>
                        <div class="product_info dscp description-truncate">{{ $allProductss->description }}</div>
                        <div class="product_info">NT$ &nbsp;{{ $allProductss->price }}</div>                    
                    </button>
                </a>
                @endforeach
            </div>
            @endforeach
        </div>
        <div id="sidePanel">
            <div id="searchBar2">
                商品搜尋  </br></br>
                <form action="{{ route('toHome_words_search') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="text" id="search2" name="search_word" value="{{ request('search') }}" placeholder="搜尋!" height="10px" title="搜尋商品名與種類">
                    <button type="submit" title="可搜尋商品名與種類" style="border: none; background: none; padding: 0; cursor: pointer;">
                        <img src="{{ asset('img/search.png') }}"
                            alt="search icon"
                            width="20px"
                            height="20px"
                            style="margin-top: -3px"
                            cursor="pointer">
                    </button>
                </form>
            </div>
            <div id="category">
                商品分類 </br></br>
                @foreach ($products_category->take(5) as $products_categorys)
                <a href="{{ route('home_with_search', ['search' => $products_categorys->category]) }}" style="margin-bottom: -10px; color: gray;">
                    {{ $products_categorys->category }} 
                </a> </br>
                @endforeach
            </div>
            <div id="newest">
                最新商品 </br> </br>
                @foreach (\App\Models\productsModel::orderBy('updated_at', 'desc')->take(3)->get() as $newestProduct)
                    <a href="{{ route('itemPage', ['id' => $newestProduct->id]) }}" style="display:flex; align-items:center; margin-bottom:10px; color: #333; text-decoration:none;">
                        <img src="{{ asset($newestProduct->pic_dir) }}" alt="{{ $newestProduct->product_name }}" style="width:60px; height:60px; object-fit:cover; margin-right:8px; border-radius:4px;">
                        <div>
                            <div style="font-size:16px;">{{ $newestProduct->product_name }}</div>
                            <div style="font-size:14px; color:gray;">NT$ {{ $newestProduct->price }}</div>
                        </div>
                    </a> </br>
                @endforeach
            </div>
        </div>
    </div>
    <div id="normal_for_cellphone">
        @foreach ($allProducts->take(16)->chunk(4) as $productRow)
        <div class="product-row">
            @foreach ($productRow as $allProductss)
            <a href="{{ route('itemPage', ['id' => $allProductss->id]) }}" style="margin-right: 10px; margin-bottom: 10px;">
                <button id="item">
                    <img src="{{ asset($allProductss->pic_dir) }}" title="優質特賣" width="160px"
                        height="190px">
                </button>
            </a>
            @endforeach
        </div>
        @endforeach
    </div>

    <div id="interested" data-aos="zoom-in" data-aos-duration="800">
        <div id="interested_title">
            <img src="{{ asset('img/good3.png') }}" id="likeGuyIcon" alt="like" height="55px" width="55">
            <p style="margin-top: -12px;">推薦你可能也喜歡!</p>
        </div>
        </button>
        <div id="interest_intems">
            @foreach ($few_products as $few_productss)
            <a href="{{ route('itemPage', ['id' => $few_productss->id]) }}">
                <button id="item_interested" height="210px" ; width="180px" ;>
                    <img src="{{ asset( $few_productss->pic_dir ) }}" alt="" title="優質特賣" width="110px"
                        height="110px">
                    <div id="level1">
                        <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt=""
                                height="15px" width="15"></div>
                        <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt=""
                                height="15px" width="15"></div>
                        <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt=""
                                height="15px" width="15"></div>
                        <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt=""
                                height="15px" width="15"></div>
                        <div id="stars"><img src="{{ asset('img/icon/star-stroke.svg') }}" alt=""
                                height="15px" width="15"></div>
                        <div id="stars"><img src="{{ asset('img/icon/star-regular.svg') }}" alt=""
                                height="15px" width="15"></div>
                    </div>
                    <div id="level2">
                        <img src="{{ asset('img/icon/dollar.svg') }}" alt="" height="15px" width="15">
                        <div id="price1">{{ $few_productss->ori_price }}</div>
                        <img src="{{ asset('img/icon/arrow-right.png') }}" alt="" height="15px" width="15">
                        <img src="{{ asset('img/icon/dollar.svg') }}" alt="" height="15px" width="15">
                        <div id="price">{{ $few_productss->price }}</div>
                    </div>
                    <div id="intro">{{ $few_productss->description }}</div>
                </button>
            </a>
            @endforeach
        </div>
    </div>
    </div>
    <!-- </main> -->


    <script>
        AOS.init({
            duration: 500,
        });
    </script>

</body>

@include('template.footer_template')

<a id="chatbot" href="{{ route('testApi_show') }}" title="AI chat"><img id="chatbot_img" src="{{ asset('img/icon/chatbot.png') }}" alt="AI chatta!"></a>
<span id="toTop"> <a href="#top"><img src="{{ asset('img/icon/arrow-up.svg') }}" alt="" title="to top" height="35px" width="35px"></a></span>

</html>