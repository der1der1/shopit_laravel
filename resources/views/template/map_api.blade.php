<script>
/* 
裡面一共有5個函式
1. initMap()：初始化地圖、標記和自動完成輸入框，並設置事件監聽器。
2. setupEventListeners()：設置地圖和標記的事件監聽器，包括地址輸入、地圖拖動、標記拖動和地圖點擊事件。
3. geocodeAddress(address)：根據輸入的地址進行地理編碼，將地址轉換為地理坐標，並更新地圖和標記位置。
4. reverseGeocode(location)：根據地理坐標進行反向地理編碼，將坐標轉換為地址，並更新地址輸入框的值。
5. updateMapAndMarker(location)：更新地圖中心和標記位置。
*/

    let map;
    let geocoder;
    let marker;
    let autocomplete;

    function initMap() {
        const defaultCenter = {
            lat: 25.0330,
            lng: 121.5654
        };
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultCenter,
            zoom: 15,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });
        geocoder = new google.maps.Geocoder();
        marker = new google.maps.Marker({
            position: defaultCenter,
            map: map,
            draggable: true,
            title: "拖動我來選擇位置"
        });
        const input = document.getElementById("map-address-input");
        autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        setupEventListeners();
    }

    function setupEventListeners() {
        const input = document.getElementById("map-address-input");
        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) {
                alert("找不到該地址的位置資訊");
                return;
            }
            updateMapAndMarker(place.geometry.location);
        });
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                geocodeAddress(input.value);
            }
        });
        map.addListener('dragend', () => {
            const center = map.getCenter();
            marker.setPosition(center);
            reverseGeocode(center);
        });
        marker.addListener('dragend', () => {
            const position = marker.getPosition();
            map.setCenter(position);
            reverseGeocode(position);
        });
        map.addListener('click', (event) => {
            const clickedLocation = event.latLng;
            updateMapAndMarker(clickedLocation);
            reverseGeocode(clickedLocation);
        });
    }

    function geocodeAddress(address) {
        if (!address.trim()) return;
        geocoder.geocode({
            address: address
        }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const location = results[0].geometry.location;
                updateMapAndMarker(location);
                document.getElementById("map-address-input").value = results[0].formatted_address;
            } else {
                alert('找不到該地址: ' + status);
            }
        });
    }

    function reverseGeocode(location) {
        geocoder.geocode({
            location: location
        }, (results, status) => {
            if (status === 'OK' && results[0]) {
                document.getElementById("map-address-input").value = results[0].formatted_address;
            } else {
                console.log('反向地理編碼失敗: ' + status);
            }
        });
    }

    function updateMapAndMarker(location) {
        map.setCenter(location);
        marker.setPosition(location);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>