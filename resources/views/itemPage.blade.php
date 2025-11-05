<!DOCTYPE html>
<html lang="en">

<head>
    <!-- 將 CSS 文件連結到 HTML -->
    <link rel="stylesheet" href="{{ asset('itemPage.css') }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品個別展示</title>
</head>

<body id="top">

    @include('template.header_template')

    <main>
        <form method="POST" action="{{ route('want') }}" enctype="multipart/form-data">
            @csrf
            <div id="interested_product">
                <div id="picture1">
                    <img id="main_pic" src="{{ asset('storage/' . $products->pic_dir) }}" alt="" height="340px" width="340px">
                </div>
                <div id="info">
                    <div id="title_select">
                        <div id="title">{{ $products->product_name }}</div>
                        <input type="submit" name="submit" id="select" value="加入購物車">
                    </div>
                    <div id="paragraph">{!! $products->description !!} </div>
                </div>
                <input type="text" name="product_id" value="{{ $products->id }}" style="display: none;">
            </div>
        </form>

        <div id="may_interesteds">
            @foreach ($few_products as $few_productss)
                <a href="{{ route('itemPage', ['id' => $few_productss->id]) }}">
                    <div id="title2">{{ $few_productss->product_name }}</div>
                    <div id="picture2"><img src="{{ asset('storage/' . $few_productss->pic_dir) }}" height=100px width=100px></div>
                    <div id="paragraph2">{{ $few_productss->description }}</div>
                </a>
            @endforeach
        </div>

        @include('template.footer_template')
        
    </main>
</body>
<span id="toTop"> <a href="#top"><img src="{{ asset('img/icon/arrow-up.svg') }}" alt="" title="to top" height="35px"
            width="35px"></a></span>

</html>