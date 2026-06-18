window.initMap = function () {
    let map;
    let directionsService = new google.maps.DirectionsService();
    let directionsRenderer = new google.maps.DirectionsRenderer();

    // Define a single InfoWindow instance globally
    let activeInfoWindow = null;

    let mapId = document.getElementById('google_map_id').value;

    // Initialize the Google map
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 48.8566, lng: 2.3522 }, // Default map center (example: Paris)
        zoom: 13,
        mapId: mapId ?? "DEMO_MAP_ID"
    });

    // Initialize the DirectionsRenderer and link it to the map
    directionsRenderer.setMap(map);

    // Loop through all the listing items and add markers to the map
    $(".listing-map-box").each(function (index, selector) {
        setMapMarker(selector, false);
    });

    // Set marker on click of any listing
    $(document).on("click", ".listing-map-box", function () {
        setMapMarker(this, true);
    });

    // Function to set markers on the map
    function setMapMarker(selector, isNew = false) {
        let lat = $(selector).data("lat");
        let lng = $(selector).data("long");
        let title = $(selector).data("title");
        let location = $(selector).data("location");
        let image = $(selector).data("image");
        let route = $(selector).data("route");

        // Set the map's center to the new marker position
        map.setCenter({ lat: lat, lng: lng });
        map.setZoom(13);

        // Create a marker
        let marker = new google.maps.marker.AdvancedMarkerElement({
            position: { lat: lat, lng: lng },
            map: map,
            title: title
        });

        // Create content for the InfoWindow
        let content = `<div style="" class="map-body">
                        <img src="${image}" alt="${title}" style="width: 100%; height: 100px; margin-bottom: 5px"/>
                        <div>
                            <a href="${route}" target="_blank" style="font-size: 16px; outline: none !important; border: none !important;">${title}</a>
                            <p style="margin-top: 10px"><i class="fas fa-map-marker-alt fa-fw text-dark"></i> ${location}</p>
                            <a href="javascript:void(0)" class="get-directions" data-lat="${lat}" data-lng="${lng}"><i class="fas fa-diamond-turn-right"></i> Get Direction</a>
                        </div>
                    </div>`;

        // Add click listener to the marker
        marker.addListener('click', function () {
            // Close the currently open InfoWindow, if any
            if (activeInfoWindow) {
                activeInfoWindow.close();
            }

            // Create a new InfoWindow or reuse the existing instance
            if (!activeInfoWindow) {
                activeInfoWindow = new google.maps.InfoWindow();
            }

            // Set content and open the InfoWindow
            activeInfoWindow.setContent(content);
            activeInfoWindow.open(map, marker);
        });

        // Open the InfoWindow by default for a new marker
        if (isNew) {
            if (activeInfoWindow) {
                activeInfoWindow.close();
            }
            if (!activeInfoWindow) {
                activeInfoWindow = new google.maps.InfoWindow();
            }
            activeInfoWindow.setContent(content);
            activeInfoWindow.open(map, marker);
        }
    }

    // Function to get directions from user's current location to the marker location
    function getDirections(destinationLat, destinationLng) {
        navigator.geolocation.getCurrentPosition(function (position) {
            let origin = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            let destination = {
                lat: destinationLat,
                lng: destinationLng
            };

            let request = {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING // or WALKING, BICYCLING, etc.
            };

            // Request directions
            directionsService.route(request, function (result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    // Display the route on the map
                    directionsRenderer.setDirections(result);
                } else {
                    alert("Could not get directions: " + status);
                }
            });
        });
    }

    // Attach click event handler using jQuery
    $(document).on('click', '.get-directions', function () {
        let lat = $(this).data('lat');
        let lng = $(this).data('lng');
        getDirections(lat, lng);
    });
};
