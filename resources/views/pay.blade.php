
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay</title>

    <!-- 連結到AOS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <!-- 將 CSS 文件連結到 HTML -->
    <link rel="stylesheet" href="{{ asset('pay.css') }}">
</head>

<body>
    <!-- 先跑要給使用者的訊息 -->
    @if(session('error'))
    <script>alert("{{ session('error') }}");</script>
    @elseif(session(key: 'success'))
    <script>alert("{{ session('success') }}");</script>
    @endif

    <div id="contener">

        @include('template.header_template')
        <main>
            <div id="items" data-aos="fade-right" data-aos-duration="500">
                @foreach ($products as $product)
                    <div id="item">
                        <div id="picture"><img src="{{ $product['pic_dir'] ??'' }}" alt="{{ $product['pic_dir'] ??'' }}" title="{{ $product['product_name'] ??'' }}" width="120px" height="120px"></div>
                        <div id="info">
                            <div id="infoName">{{ $product['product_name'] ??'' }}</div>
                            <div id="infoPrice">${{ $product['price'] ??'' }} x {{ $product['num'] ??'' }} = ${{ $product['price'] * $product['num'] }} </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="where" data-aos="fade-down" data-aos-duration="900">
                <!-- 本div中有5個部分 1. toStore; 2. toHome; 3. 收貨姓名; 4. 付款帳號; 5. 地圖API-->
                    
                <form method="POST" action="{{ route('pay_to_shop') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- 1. toStore -->
                    <div id="toStore">
                        <button id="market" title="market">
                            <img src="{{ asset('img/icon/shop.png') }}" alt="to store" height="50px" width="50px">
                        </button>
                        <!--711的下拉式表單，此有機會可以再加入mysql-->
                        <select name="store">
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
                        <input type="submit" value="請選擇貨運711">
                    </div>
                </form>


                <form method="POST" action="{{ route('pay_to_home') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- 2. toHome -->
                    <div id="toHome">
                        <button id="express" title="express">
                            <img src="{{ asset('img/icon/home.png') }}" alt="to home" height="50px" width="50px">
                        </button>

                        <label for="deliver to your home">輸入住址：</label>
                        @if ( empty($ppl_info->to_address) )
                        <input type="text"   name="address"  value="eg. 台南市善化區小新里">
                        @else 
                        <input type="text"   name="address"  value= {{ $ppl_info->to_address ??''}}>
                        @endif
                        <input type="submit" value="選擇宅配到家">
                    </div>
                </form>



                <form method="POST" action="{{ route('pay_name') }}" enctype="multipart/form-data">
                    @csrf
                <!--3. 收貨姓名; 4. 付款帳號 -->
                    <div id="check_name">
                        <label for="deliver to your home">請輸入收貨人姓名：</label>
                        @if ( empty($ppl_info->name) )
                        <input type="text"   name="name_input"  value="王大明">
                        @else
                        <input type="text"   name="name_input"  value=" {{ $ppl_info->name }} ">
                        @endif
                        <input type="submit" value="確認">
                    </div>
                </form>


                <form method="POST" action="{{ route('pay_account') }}" enctype="multipart/form-data">
                    @csrf
                    <div id="check_account">
                        <label for="deliver to your home">請輸入扣款帳號：</label>
                        @if ( empty($ppl_info->name) )
                        <input type="text"   name="account_input"  value="0191227-0082229">
                        @else
                        <input type="text"   name="account_input"  value=" {{ $ppl_info->bank_account }} ">
                        @endif
                        <input type="submit" value="確認">
                    </div>
                </form>

                <!-- 5. 地圖API -->
                <div id="LocationMap">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d13321.760098507479!2d120.21569612878777!3d22.998651853240307!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1szh-TW!2stw!4v1705164389576!5m2!1szh-TW!2stw" width="100%" height="255px" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>


            <div id="trading" data-aos="fade-left" data-aos-duration="700">

                <table id="check_list"> 購買確認清單
                    <tr><td>收件姓名：</td><td> {{ $purchased->name ??'' }} </td></tr>
                    <tr><td>訂購帳號：</td><td> {{ $purchased->account ??'' }} </td></tr>
                    <tr><td>扣款帳號：</td><td> {{ $purchased->bank_account ??'' }} </td></tr>

                    @if ( $purchased->shop1_addr2 == "1" )
                    <tr><td>貨運方式：</td><td>超商取貨(位置以下)</td></tr>
                    <tr><td>收件商店：</td><td>{{ $purchased->to_shop }}</td></tr>
                    @elseif ( $purchased->shop1_addr2 == "2" )
                    <tr><td>貨運方式：</td><td>快遞到家(位置以下)</td></tr>
                    <tr><td>收件地址：</td><td>{{ $purchased->to_address }}</td></tr>
                    @else
                    <tr><td>貨運方式：</td><td style="color:gray;">請在左側選擇</td></tr>
                    <tr><td>收件地址：</td><td>{{ $purchased->to_address }}</td></tr>
                    @endif

                    <tr><th>名稱</th><th>編號</th><th>數量</th><th>單價</th><th>小計</th></tr>
                    <tr><th>_____</th><th>_____</th><th>_____</th><th>_____</th><th>_____</th></tr>

                    @foreach ($products as $product)
                    <tr><th>{{ $product['product_name'] ??'' }}</th><th>{{ $product['id'] ??'' }}</th><th>{{ $product['num'] ??'' }}</th><th>{{ $product['price'] ??'' }}</th><th> {{ $product['num'] * $product['price'] }} </th></tr>
                    @endforeach

                    <tr><th>_____</th><th>_____</th><th>_____</th><th>_____</th><th>_____</th></tr>
                    <tr><td>交易金額：</td><td> ${{ $purchased->bill }} </td></tr>
                    </table>
                
                    <form method="POST" action="{{ route('pay_confirm') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- 送回後端要存在訂單的 -->
                    <input type="text" name="name" value="{{$purchased->name ??''}}" style="display:none;">
                    <input type="text" name="bank_account" value="{{$purchased->bank_account ??''}}" style="display:none;">
                    <input type="text" name="shop1_addr2" value="{{$purchased->shop1_addr2}}" style="display:none;">
                    <input type="text" name="to_shop" value="{{$purchased->to_shop}}" style="display:none;">
                    <input type="text" name="to_address" value="{{$purchased->to_address}}" style="display:none;">

                        <button title="結帳" type="submit" id="checkit">
                            <h3>結帳</h3>
                            <img src="{{ asset('img/icon/bill.png') }}" alt="pay for it" width="50x" height="50px">
                        </button>
                    </form>
            </div>

        @include('template.footer_template')

        </main>
    </div>
    <script>
        AOS.init();
    </script>
</body>

</html>
