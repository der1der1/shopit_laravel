<script>
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
        const input = document.getElementById("address-input");
        autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        setupEventListeners();
    }

    function setupEventListeners() {
        const input = document.getElementById("address-input");
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
                document.getElementById("address-input").value = results[0].formatted_address;
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
                document.getElementById("address-input").value = results[0].formatted_address;
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