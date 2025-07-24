<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shopit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            color: #00372eff;
            margin: 0;
            padding: 20px;
        }
        #trading {
            background-color: #ffffff;
            border: 2px solid #004d40;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #00372eff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #004d40;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #004d40;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #e0f2f1;
        }
        tr:hover {
            background-color: #b2dfdb;
        }
    </style>
</head>
<body>
    <div id="trading" data-aos="fade-left" data-aos-duration="700">
        <h1>購買確認清單</h1>
        <p>收件姓名：{{ $purchased->name ??'' }}</p>
        <p>訂購帳號：{{ $purchased->account ??'' }}</p>
        <p>扣款帳號：{{ $purchased->bank_account ??'' }}</p>
        @if ( $purchased->shop1_addr2 == "1" )
        <p>貨運方式：超商取貨(位置以下)</p>
        <p>收件商店：{{ $purchased->to_shop }}</p>
        @elseif ( $purchased->shop1_addr2 == "2" )
        <p>貨運方式：快遞到家(位置以下)</p>
        <p>收件地址：{{ $purchased->to_address }}</p>
        @else
        <p>貨運方式：未選擇</p>
        @endif

        <table id="check_list"> 
            <tr><th>名稱</th><th>編號</th><th>數量</th><th>單價</th><th>小計</th></tr>
            @foreach ($products as $product)
            <tr>
                <td>{{ $product['product_name'] ??'' }}</td>
                <td>{{ $product['id'] ??'' }}</td>
                <td>{{ $product['num'] ??'' }}</td>
                <td>{{ $product['price'] ??'' }}</td>
                <td>{{ (int) $product['num'] * (float) $product['price'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">交易金額：</td>
                <td>${{ $purchased->bill }}</td>
            </tr>
        </table>

        您的訂單已經成功送出嘍！<br>
        請確認以上資訊無誤；若有任何問題，請<a href="{{ url('/contact') }}" style="color: #00372eff; text-decoration: underline;">聯繫我們</a> 。
    </div>
</body>
</html>
