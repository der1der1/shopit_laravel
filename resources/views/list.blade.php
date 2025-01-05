<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Lists</title>
    <!-- 將 CSS 文件連結到 HTML -->
    <link rel="stylesheet" href="{{ asset('list.css') }}">
</head>

<body id="top">
    <!-- 先跑要給使用者的訊息 -->
    @if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
    @endif

    <div id="contener">
        
        @include('template.header_template')

    
        <main>
            <div id="outer">
                
                @if (empty($new_lists))
                <div>目前尚無訂單需要處理！</div>
                @endif

                @foreach ($new_lists as $new_list)
                <form method="POST" action="{{ route('list_store') }}" enctype="multipart/form-data">
                @csrf
                    <div id="item">
                        <div id="row1" class="row">
                            <div id="acount">單號 :&nbsp;&nbsp; {{ $new_list['id'] }} </div>
                            <div id="acount">acount :&nbsp;&nbsp; {{ $new_list['account'] }} </div>
                            <input type="submit" id="done" value="done" title="完成此訂單，刪除。" style=" height:22px"></div>
                            <!-- 一些要傳遞回後端的訊息 -->
                            <input type="text" name="id_done" value="{{ $new_list['id'] ??'' }}" style="display:none;">
                            <input type="text" name="account_done" value="{{ $new_list['account'] ??'' }}" style="display:none;">

                        <div id="row2" class="row">
                            <div id="name">name :&nbsp;&nbsp;{{ $new_list['name'] ??'' }}</div>
                            @if (($new_list->shop1_addr2 ?? 0) == 1)
                            <div id="to_shop">shop :&nbsp;&nbsp;{{ $new_list['to_address'] ??'' }}</div>
                            @else
                            <div id="to_home">home :&nbsp;&nbsp;{{ $new_list['to_shop'] ??'' }}</div>
                            @endif
                        </div>

                        <div id="row4" class="row">
                        <div id="product">
                            <div id="product_id">編號</div>
                            <div id="product_name">商品</div>
                            <div id="product_price">價錢</div>
                            <div id="product_num">數量</div>
                            <div id="product_sum">小計</div>
                        </div>
                            @foreach ($new_list['product'] as $single_product)
                            <div id="product">
                                <div id="product_id">{{ $single_product['id'] ??'' }}</div>
                                <div id="product_name">{{ $single_product['product_name'] ??'' }}</div>
                                <div id="product_price">{{ $single_product['price'] ??'' }}</div>
                                <div id="product_num">{{ $single_product['num'] ??'' }}</div>
                                <div id="product_sum">{{ (int)($single_product['price'] ??'') * (int)($single_product['num'] ??'') }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </form>
                @endforeach
            </div>
        </main>

    </div>

    @include('template.footer_template')

</body>
<span id="toTop"> <a href="#top"><img src="{{ asset('img/icon/arrow-up.svg') }}" alt="" title="to top" height="35px" width="35px"></a></span>
</html>