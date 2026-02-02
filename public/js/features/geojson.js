function loadGeoJSON() {
    fetch("/data/Batas_Banjarbakula.geojson")
        .then((response) => {
            if (!response.ok) throw new Error("Gagal load GeoJSON");
            return response.json();
        })
        .then((data) => {
            geoJsonLayer = L.geoJSON(data, {
                pane: "areaPolygon",
                style: function (feature) {
                    var nama =
                        feature.properties.nama ||
                        feature.properties.NAMOBJ ||
                        "";
                    var warna = "#3388ff";
                    if (nama.toUpperCase().includes("BANJARMASIN"))
                        warna = "#ef4444";
                    else if (nama.toUpperCase().includes("BANJARBARU"))
                        warna = "#22c55e";
                    else if (nama.toUpperCase().includes("BANJAR"))
                        warna = "#3b82f6";
                    return {
                        color: warna,
                        fillColor: warna,
                        fillOpacity: 0.1,
                        weight: 2,
                        dashArray: "5, 5",
                    };
                },
                onEachFeature: function (feature, layer) {
                    var nama =
                        feature.properties.nama ||
                        feature.properties.NAMOBJ ||
                        "Wilayah Banjarbakula";
                    layer.on({
                        mouseover: function (e) {
                            var layer = e.target;
                            layer.setStyle({
                                weight: 2,
                                color: "#f59e0b",
                                dashArray: "",
                                fillOpacity: 0.2,
                            });
                            if (
                                !L.Browser.ie &&
                                !L.Browser.opera &&
                                !L.Browser.edge
                            ) {
                                layer.bringToFront();
                            }
                        },
                        mouseout: function (e) {
                            geoJsonLayer.resetStyle(e.target);
                        },
                        click: function (e) {
                            L.popup({
                                className: "region-popup",
                                closeButton: false,
                                autoPan: false,
                                offset: [0, -10],
                            })
                                .setLatLng(e.latlng)
                                .setContent(nama)
                                .openOn(map);
                        },
                    });
                },
            }).addTo(map);
        })
        .catch((error) => console.error("Error memuat peta wilayah:", error));
}
