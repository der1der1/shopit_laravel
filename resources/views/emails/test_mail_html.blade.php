<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shopit</title>
</head>
<body>
    <h1>Desmoco Mail</h1>
    <p>這是從 Shopit 發送的 HTML 格式測試信件。</p>
    <!-- 內嵌圖片，路徑從public之後開始寫 -->
    <p>blade頁面的圖片下</p>
    <img src="{{ $message->embed(public_path('img/pictureTarget/laptop2.png')) }}">
    <p>blade頁面的圖片上</p>
</body>
</html>
