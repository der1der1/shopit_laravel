<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps 地址定位</title>
    <style>
        #map { height: 400px; width: 100%; }
        #address-input { width: 100%; padding: 10px; margin-bottom: 10px; font-size: 16px; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <input type="text" id="address-input" placeholder="請輸入地址...">
        <div id="map"></div>
    </div>

    @include('template.map_api')
</body>
</html>