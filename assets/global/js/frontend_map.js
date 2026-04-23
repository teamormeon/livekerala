$(document).ready(function () {
    let googleMapAppKey = $('#googleMapAppKey').val();

    const leaflet = L.map("map");
    const map = leaflet.setView([48.8566, 2.3522], 13);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    L.control
        .fullscreen({
            position: "topright",
            content: null,
            forceSeparateButton: true,
            forcePseudoFullscreen: true,
            fullscreenElement: false,
        })
        .addTo(map);


    let routingControl;

    let currentPopup = null;

    $(".listing-map-box").each(function (index, selector) {
        setMapMarker(selector, false);
    });

    $(document).on("click", ".listing-map-box", function () {
        setMapMarker(this, true);
    });

    function getDirections(destinationLat, destinationLng) {
        if (routingControl) {
            routingControl.remove();
        }

        navigator.geolocation.getCurrentPosition(function (position) {
            const origin = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            const destination = new google.maps.LatLng(destinationLat, destinationLng);

            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();


            const googleMap = new google.maps.Map(document.createElement("div"));
            directionsRenderer.setMap(googleMap);


            const request = {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.WALKING, // You can change the travel mode
            };

            directionsService.route(request, function (result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    // Extract the route from the Google API result
                    const route = result.routes[0];
                    const path = route.overview_path; // Path to draw on the map

                    // Convert Google Maps path to Leaflet polyline coordinates
                    const latLngs = path.map(function (latLng) {
                        return L.latLng(latLng.lat(), latLng.lng());
                    });

                    // Add the route as a polyline on the Leaflet map
                    routingControl = L.polyline(latLngs, {color: 'blue', weight: 4}).addTo(map);

                    // Optionally, zoom the map to fit the route's bounds
                    map.fitBounds(routingControl.getBounds());
                } else {
                    alert("Directions request failed due to " + status);
                }
            });
        }, function (error) {
            alert("Geolocation failed: " + error.message); // Error handling for geolocation
        });
    }

    function setMapMarker(selector, isNew = false) {
        let lat = $(selector).data("lat");
        let long = $(selector).data("long");
        let title = $(selector).data("title");
        let location = $(selector).data("location");
        let image = $(selector).data("image");
        let route = $(selector).data("route");

        leaflet.setView([lat, long], 14);

        let markerDesign = `<div class="map-body">
                <img src="${image}" alt="${title}" style="width: 100%; height: 100px; margin-bottom: 5px"/>
                <div>
                    <a href="${route}" target="_blank">${title}</a>
                    <p style="margin: 10px 0"><i class="fas fa-map-marker-alt fa-fw text-dark"></i> ${location}</p>`

        if (googleMapAppKey) {
            markerDesign += `<a href="javascript:void(0)" class="get-directions" data-lat="${lat}" data-lng="${long}">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                </div>
            </div>`;
        } else {
            markerDesign += `</div>
            </div>`;
        }

        let marker = L.marker([lat, long]).addTo(map).bindPopup(markerDesign);

        if (currentPopup) {
            currentPopup.remove();
        }

        currentPopup = marker.getPopup();

        if (isNew) {
            marker.openPopup();
        }
    }

    $(document).on('click', '.get-directions', function () {
        let destinationLat = $(this).data('lat');
        let destinationLng = $(this).data('lng');
        getDirections(destinationLat, destinationLng);
    });
});








