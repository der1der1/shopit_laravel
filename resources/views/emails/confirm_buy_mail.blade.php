<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shopit</title>
</head>
<body>
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
    </div>
</body>
</html>
