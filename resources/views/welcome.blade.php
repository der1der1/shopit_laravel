<!DOCTYPE html>
<html lang="en">

@include('template.head_template')

<body id="top">
    <!-- 先跑要給使用者的訊息 -->
    @if(session('error'))
    <script>alert("{{ session('error') }}");</script>
    @elseif(session(key: 'success'))
    <script>alert("{{ session('success') }}");</script>
    @endif

    <div id="contener">
        
        @include('template.header_template')

        <main>
            @csrf
            <div id="middbody" class="ROW">
                <!-- 普通螢幕，顯示的aside(小螢幕點開顯示) -->
                <aside id="aside" class="col-pc-2 col-mobile-12">
                    <div id="infos" style="margin:0 0 20px 0;">
                        @if ($user)
                        <h5 style="margin:0 0 10px 0;">您的訊息：</h5>
                        @if($infos[0] == null)
                        <div id="info1" data-aos="fade-right" data-aos-duration="400" class="info">
                            目前尚無新訊息喔! </div>
                        @else
                        @foreach ( $infos as $info )
                        <div id="info1" data-aos="fade-right" data-aos-duration="400" class="info">
                            {{ $info }} </div>
                        @endforeach

                        @endif
                        @endif
                    </div>
                    <!-- 訊息框結束 -->

                   <h4>產品分類：</h4>
                    <div id="Products">
                        <ul>
                            <!-- 第一個分類是回到主頁 -->
                            <a href="{{ route('home') }}">
                                <li>全部</li>
                            </a>
                            @foreach ($products_category as $products_categorys)
                            <a href="{{ route('home_with_search', ['search' => $products_categorys->category]) }}">
                                <li>{{ $products_categorys->category }}</li>
                            </a>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <div id="contents" class="col-pc-10 col-mobile-12">
                    <!-- 下分三大類greatPromotion、interested、normal -->
                    <div id="greatPromotion">
                        <!-- Retrieved the div code from Bootstrap -->
                        <!-- 廣告圖display -->
                        <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel"
                            data-aos="zoom-in" data-aos-duration="400">
                            <div class="carousel-inner">
                                <div class="carousel-item active" data-bs-interval="4000">
                                    <img src="{{ asset('img/pictureTarget/ad1.png') }}" class="d-block w-100"
                                        height="255px">
                                </div>
                                <div class="carousel-item" data-bs-interval="4000">
                                    <img src="{{ asset('img/pictureTarget/ad2.png') }}" class="d-block w-100"
                                        height="255px">
                                </div>
                                <div class="carousel-item" data-bs-interval="4000">
                                    <img src="{{ asset('img/pictureTarget/ad3.png') }}" class="d-block w-100"
                                        height="255px">
                                </div>
                                <div class="carousel-item" data-bs-interval="4000">
                                    <img src="{{ asset('img/pictureTarget/ad4.png') }}" class="d-block w-100"
                                        height="255px">
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
                    <div id="interested" data-aos="zoom-in" data-aos-duration="800">
                        <div id="interested_title">
                            <p>推薦你可能也喜歡!</p>
                        </div>
                        </button>
                        <div id="interest_intems">
                            @foreach ($few_products as $few_productss)
                            <a href="{{ route('itemPage', ['id' => $few_productss->id]) }}">
                                <button id="item" height="210px" ; width="180px" ;>
                                    <img src="{{ asset($few_productss->pic_dir) }}" alt="" title="優質特賣" width="110px"
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

                <div id="normal" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                    @foreach ($allProducts as $allProductss)
                        <a href="{{ route('itemPage', ['id' => $allProductss->id]) }}">
                            <button id="item" height="210px" ; width="180px" ;>
                                <img src="{{ asset($allProductss->pic_dir) }}" alt="" title="優質特賣" width="110px"
                                    height="110px">
                                <div id="level1">
                                    <div id="stars"><img src="{{ asset(path: 'img/icon/star-solid.svg') }}" alt="" height="15px"
                                            width="15"></div>
                                    <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt="" height="15px"
                                            width="15"></div>
                                    <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt="" height="15px"
                                            width="15"></div>
                                    <div id="stars"><img src="{{ asset('img/icon/star-solid.svg') }}" alt="" height="15px"
                                            width="15"></div>
                                    <div id="stars"><img src="{{ asset('img/icon/star-stroke.svg') }}" alt="" height="15px"
                                            width="15"></div>
                                    <div id="stars"><img src="{{ asset('img/icon/star-regular.svg') }}" alt="" height="15px"
                                            width="15"></div>
                                </div>
                                <div id="level2">
                                    <img src="{{ asset('img/icon/dollar.svg') }}" alt="" height="15px" width="15">
                                    <div id="price1">{{ $allProductss->ori_price }}</div>
                                    <img src="{{ asset('img/icon/arrow-right.png') }}" alt="" height="15px" width="15">
                                    <img src="{{ asset('img/icon/dollar.svg') }}" alt="" height="15px" width="15">
                                    <div id="price">{{ $allProductss->price }}</div>
                                </div>
                                <div id="intro">{{ $allProductss->description }}</div>
                            </button>
                        </a>
                    @endforeach
                            
                </div>
            </div>
    </div>
    </main>
    </div>
    
    @include('template.footer_template')

    <script>
        AOS.init({
            duration: 500,
        });

    </script>

</body>
<span id="toTop"> <a href="#top"><img src="{{ asset('img/icon/arrow-up.svg') }}" alt="" title="to top" height="35px" width="35px"></a></span>
</html>