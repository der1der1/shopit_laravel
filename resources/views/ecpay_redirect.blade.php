<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>付款跳轉中…</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center;
               justify-content: center; height: 100vh; margin: 0; background: #f5f5f5; }
        .box { text-align: center; }
        .spinner { width: 40px; height: 40px; border: 4px solid #ccc;
                   border-top-color: #555; border-radius: 50%;
                   animation: spin .8s linear infinite; margin: 0 auto 16px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="box">
        <div class="spinner"></div>
        <p>正在跳轉至綠界付款頁面，請稍候…</p>
        {!! $ecpayForm !!}
    </div>
</body>
</html>
