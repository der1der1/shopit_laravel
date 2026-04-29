<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>付款結果</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
            position: relative;
            overflow: hidden;
        }

        .bg-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }

        .card {
            background: rgba(255, 255, 255, 0.75);
            padding: 40px 56px;
            border-radius: 20px;
            text-align: center;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
        }

        h2 {
            margin: 0 0 8px;
        }

        p {
            color: #666;
            margin: 0 0 24px;
        }

        a {
            display: inline-block;
            padding: 10px 28px;
            background: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }

        a:hover {
            background: #555;
        }
    </style>
</head>

<body>
    <img src="{{ asset('img/icon/walk.jpg') }}" alt="" class="bg-image">
    <div class="card">
        @if($success)
        <img src="{{ asset('img/icon/check2.png') }}" alt="OK!" class="icon">
        <!-- <div class="icon">✅</div> -->
        <h2>付款成功</h2>
        <p>感謝您的購買！訂單已完成付款。</p>
        @if(!empty($orderId))
        <p style="color:#333;font-size:0.9rem;">訂單編號：#{{ $orderId }}</p>
        @endif
        @else
        <img src="{{ asset('img/icon/crosss.png') }}" alt="Error!" class="icon">
        <!-- <div class="icon">❌</div> -->
        <h2>付款失敗</h2>
        <p>{{ $rtnMsg ?: '交易未完成，請重新嘗試或聯絡客服。' }}</p>
        @if(!empty($orderId))
        <p style="color:#333;font-size:0.9rem;">訂單編號：#{{ $orderId }}</p>
        @endif
        @endif
        <a href="{{ route('home') }}">回首頁</a>
    </div>
</body>

</html>