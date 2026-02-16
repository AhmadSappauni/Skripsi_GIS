// 1. Variabel Global
var geoJsonData = null; // Menyimpan data mentah GeoJSON
var activeGeoJsonLayers = {}; // Menyimpan layer yang sedang aktif di peta (per wilayah)

// 2. Fungsi Load GeoJSON (Hanya fetch data sekali)
function loadGeoJSON() {
    fetch("/data/Batas_Banjarbakula.geojson")
        .then((response) => {
            if (!response.ok) throw new Error("Gagal load GeoJSON");
            return response.json();
        })
        .then((data) => {
            geoJsonData = data; // Simpan data ke variabel global
            
            // Inisialisasi: Tampilkan semua wilayah secara default
            // Pastikan ID checkbox di HTML sesuai (misal: chk-Banjarmasin)
            toggleSpecificLayer('Banjarmasin', true);
            toggleSpecificLayer('Banjarbaru', true);
            toggleSpecificLayer('Banjar', true);
            toggleSpecificLayer('TanahLaut', true);
            toggleSpecificLayer('Batola', true);
        })
        .catch((error) => console.error("Error memuat peta wilayah:", error));
}

// 3. Fungsi Toggle (Filter & Render Layer)
window.toggleSpecificLayer = function(regionKey, isChecked) {
    if (!geoJsonData) return; // Tunggu data selesai dimuat

    // Jika layer sudah ada, hapus dulu
    if (activeGeoJsonLayers[regionKey]) {
        map.removeLayer(activeGeoJsonLayers[regionKey]);
        delete activeGeoJsonLayers[regionKey];
    }

    // Jika dicentang, buat layer baru khusus untuk wilayah tersebut
    if (isChecked) {
        var filteredLayer = L.geoJSON(geoJsonData, {
            pane: "areaPolygon",
            filter: function(feature) {
                // Logika Filter Berdasarkan Nama di Properties
                var nama = (feature.properties.nama || feature.properties.NAMOBJ || "").toUpperCase();
                
                // Sesuaikan keyword pencarian dengan Key tombolmu
                if (regionKey === 'Banjarmasin') return nama.includes("BANJARMASIN");
                if (regionKey === 'Banjarbaru') return nama.includes("BANJARBARU");
                if (regionKey === 'Banjar') return nama.includes("KAB. BANJAR") || (nama.includes("BANJAR") && !nama.includes("BARU") && !nama.includes("MASIN"));
                if (regionKey === 'TanahLaut') return nama.includes("TANAH LAUT") || nama.includes("PELAIHARI"); // Sesuaikan dengan data
                if (regionKey === 'Batola') return nama.includes("BARITO KUALA") || nama.includes("MARABAHAN"); // Sesuaikan dengan data
                
                return false;
            },
            style: function(feature) {
                // Style (Sama seperti kodemu sebelumnya)
                var nama = (feature.properties.nama || feature.properties.NAMOBJ || "").toUpperCase();
                var warna = "#3388ff"; 
                if (nama.includes("BANJARMASIN")) warna = "#ef4444";
                else if (nama.includes("BANJARBARU")) warna = "#22c55e";
                else if (nama.includes("BANJAR")) warna = "#3b82f6";
                else if (nama.includes("TANAH LAUT")) warna = "#eab308";
                else if (nama.includes("BARITO KUALA")) warna = "#a855f7";

                return {
                    color: warna,
                    fillColor: warna,
                    fillOpacity: 0.1,
                    weight: 2,
                    dashArray: "5, 5",
                };
            },
            onEachFeature: function(feature, layer) {
                 // Interaksi (Sama seperti kodemu)
                 var nama = feature.properties.nama || feature.properties.NAMOBJ || "Wilayah";
                 layer.on({
                    mouseover: function (e) {
                        e.target.setStyle({ weight: 3, fillOpacity: 0.3, dashArray: "" });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            e.target.bringToFront();
                        }
                    },
                    mouseout: function (e) {
                        activeGeoJsonLayers[regionKey].resetStyle(e.target);
                    },
                    click: function (e) {
                         L.popup({ className: "region-popup", closeButton: false, offset: [0, -10] })
                            .setLatLng(e.latlng)
                            .setContent(nama)
                            .openOn(map);
                    }
                });
            }
        }).addTo(map);

        // Simpan referensi layer agar bisa dihapus nanti
        activeGeoJsonLayers[regionKey] = filteredLayer;
    }
}

// 4. Fungsi Panel & Reset (Sama seperti sebelumnya)
window.toggleLayerPanel = function() {
    document.getElementById('layerPanel').classList.toggle('show');
    document.querySelector('.btn-layer-toggle').classList.toggle('active');
}

window.resetLayers = function(show) {
    const checkboxes = document.querySelectorAll('.layer-list input[type="checkbox"]');
    checkboxes.forEach(chk => {
        chk.checked = show;
        // Ambil key region dari ID (contoh: chk-Banjarmasin -> Banjarmasin)
        var regionKey = chk.id.replace('chk-', '');
        toggleSpecificLayer(regionKey, show);
    });
}