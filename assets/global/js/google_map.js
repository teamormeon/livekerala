
(function () {
    function initMap() {
        const input = document.getElementById("address-search");
        let latlng = { lat: parseFloat(input.dataset.lat), lng: parseFloat(input.dataset.long) };

        const map = new google.maps.Map(document.getElementById("map"), {
            center: latlng,
            zoom: 13,
        });

        let country_code = input.dataset.code;
        const autocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: country_code },
        });

        autocomplete.bindTo("bounds", map);

        const infowindow = new google.maps.InfoWindow();
        const infowindowContent = document.getElementById("infowindow-content");
        infowindow.setContent(infowindowContent);

        let marker = new google.maps.Marker({
            position: latlng,
            map,
            draggable: false, // Marker is not draggable in this case
        });

        // Reverse geocoding function
        const geocoder = new google.maps.Geocoder();

        // Add a click event listener on the map
        google.maps.event.addListener(map, 'click', function (e) {
            // Remove existing marker if any
            if (marker) {
                marker.setMap(null);
            }

            // Set new marker at the clicked location
            marker = new google.maps.Marker({
                position: e.latLng,
                map: map,
            });

            // Center the map and adjust zoom level
            map.setCenter(e.latLng);
            map.setZoom(13);

            // Update the input fields with the new lat, lng values
            document.getElementById("lat").value = e.latLng.lat();
            document.getElementById("lng").value = e.latLng.lng();

            // Use Geocoder to get the address
            geocoder.geocode({ location: e.latLng }, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        // The formatted address, which is human-readable
                        const formattedAddress = results[0].formatted_address;

                        // Set the human-readable address in the address-search input field
                        document.getElementById("address-search").value = formattedAddress;
                    } else {
                        document.getElementById("address-search").value = "";
                    }
                } else {
                    document.getElementById("address-search").value = "";
                }
            });
        });

        autocomplete.addListener("place_changed", () => {
            infowindow.close();
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) {
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPlace({
                placeId: place.place_id,
                location: place.geometry.location,
            });
            marker.setVisible(true);

            infowindow.setContent(`<div id="infowindow-content">
                                    <p id="place-name" class="title">${place.name}</p>
                                    <span id="place-address">${place.formatted_address}</span>
                                </div>`);

            document.getElementById("lat").value = place.geometry.location.lat();
            document.getElementById("lng").value = place.geometry.location.lng();
            document.getElementById("address-search").value = place.formatted_address;

            // Open the info window when the place is selected from autocomplete
            infowindow.open(map, marker);
        });
    }
    window.initMap = initMap;
})();







