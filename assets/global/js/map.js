$(document).ready(function () {
    var leaflet = L.map("map");
    var map = leaflet.setView([40.55, -96.41], 6);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    L.control
        .fullscreen({
            position: "topright",
            content: null,
            forceSeparateButton: true,
            forcePseudoFullscreen: true,
            fullscreenElement: false,
        })
        .addTo(map);

    var newMarker = {};

    setMarker('.place_id');

    $('.place_id').on('change input', function () {
        setMarker(this);
    })

    function setMarker(place_id) {
        let lat = $(place_id).find(":selected").data("lat");
        let lang = $(place_id).find(":selected").data("long");
        if (lat && lang) {
            if (newMarker != undefined) {
                map.removeLayer(newMarker);
            }
            map.setView([lat, lang], 6);
            newMarker = new L.marker([lat, lang]).addTo(map);
        }
    }

    // var arcgisOnline = L.esri.Geocoding.arcgisOnlineProvider({countries: $('#address-search').data('code')});
    var arcgisOnline = L.esri.Geocoding.arcgisOnlineProvider();

    var gisDay = L.esri.Geocoding.featureLayerProvider({
        url: "https://services.arcgis.com/uCXeTVveQzP4IIcx/arcgis/rest/services/GIS_Day_Final/FeatureServer/0",
        searchFields: ["Name", "Organization"],
        label: "GIS Day Events",
        formatSuggestion: function (feature) {
            return (feature.properties.Name + " - " + feature.properties.Organization); // format suggestions like this.
        },
    });
    var searchControl = L.esri.BootstrapGeocoder.search({
        inputTag: "address-search",
        providers: [arcgisOnline, gisDay]
    }).addTo(map);

    var results = L.layerGroup().addTo(map);
    searchControl.on("results", function (data) {
        results.clearLayers();

        for (var i = data.results.length - 1; i >= 0; i--) {
            if (newMarker != undefined) {
                map.removeLayer(newMarker);
            }
            document.getElementById("lat").value = data.results[i].latlng.lat;
            document.getElementById("lng").value = data.results[i].latlng.lng;
            document.getElementById("address-search").value = data.text;

            newMarker = new L.marker([data.results[i].latlng.lat, data.results[i].latlng.lng]).addTo(map);
        }

        setTimeout(function () {
            if (data.results.length) {
                map.setView([data.results[0].latlng.lat, data.results[0].latlng.lng], 6);
            }
        }, 100)
    });

    map.on("click", function (e) {
        if (newMarker != undefined) {
            map.removeLayer(newMarker);
        }
        map.setView(e.latlng, 6);
        newMarker = L.marker(e.latlng).addTo(map);

        document.getElementById("lat").value = e.latlng.lat;
        document.getElementById("lng").value = e.latlng.lng;

        L.esri.Geocoding
            .reverseGeocode({
                apikey: 'uCXeTVveQzP4IIcx'
            })
            .latlng(e.latlng)
            .run(function (error, result) {
                if (error) {
                    document.getElementById("address-search").value = "";
                    return;
                }
                document.getElementById("address-search").value = result.address.Match_addr;
            });

    });

    $(".leaflet-control-attribution").remove();
});
