<!DOCTYPE html>
<html>
<head>
    <title>Google Map - Your Location</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>

    {{-- Google Maps API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>

    <script>
        function initMap() {
            var userLocation = {
                lat: {{ $lat }},
                lng: {{ $lng }}
            };

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: userLocation
            });

            var marker = new google.maps.Marker({
                position: userLocation,
                map: map,
                title: "You are here!"
            });
        }

        window.onload = initMap;
    </script>
</head>
<body>
    <h2>🗺️ Your Location on Google Map</h2>
    <div id="map"></div>
</body>
</html>
