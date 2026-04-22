<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>付款結果</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center;
               justify-content: center; height: 100vh; margin: 0; background: #f5f5f5; }
        .card { background: #fff; padding: 40px 56px; border-radius: 12px;
                box-shadow: 0 4px 16px rgba(0,0,0,.1); text-align: center; max-width: 420px; }
        .icon { font-size: 56px; margin-bottom: 12px; }
        h2 { margin: 0 0 8px; }
        p  { color: #666; margin: 0 0 24px; }
        a  { display: inline-block; padding: 10px 28px; background: #333;
             color: #fff; text-decoration: none; border-radius: 6px; }
        a:hover { background: #555; }
    </style>
</head>
<body>
    <div class="card">
        @if($success)
            <div class="icon">✅</div>
            <h2>付款成功</h2>
            <p>感謝您的購買！訂單已完成付款。</p>
        @else
            <div class="icon">❌</div>
            <h2>付款失敗</h2>
            <p>{{ $rtnMsg ?: '交易未完成，請重新嘗試或聯絡客服。' }}</p>
        @endif
        <a href="{{ route('home') }}">回首頁</a>
    </div>
</body>
</html>
