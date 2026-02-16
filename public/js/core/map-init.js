window.getLocation = function() {
    var status = document.getElementById("statusLokasi");
    if (navigator.geolocation) {
        status.innerHTML = "⏳ Sedang mencari satelit...";
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else { status.innerHTML = "❌ Browser tidak support GPS."; }
}

function showPosition(position) {
    var lat = position.coords.latitude;
    var long = position.coords.longitude;
    
    localStorage.setItem('userLat', lat);
    localStorage.setItem('userLong', long);
    getAddress(lat, long); 
    
    if(map) map.flyTo([lat, long], 16, { animate: true, duration: 2 });
    
    // Hapus marker lama jika ada
    if (typeof userMarker !== 'undefined' && userMarker) map.removeLayer(userMarker);
    if (typeof userCircle !== 'undefined' && userCircle) map.removeLayer(userCircle);
    
    // Buat Lingkaran Area
    userCircle = L.circle([lat, long], { 
        color: '#f16363',      // Warna garis ungu modern
        fillColor: '#ff0000',  // Warna isi ungu muda
        fillOpacity: 0.15, 
        weight: 1,
        radius: 100 
    }).addTo(map);

    // --- POPUP KEREN (CUSTOM HTML) ---
    const popupContent = `
        <div class="user-popup-container">
            <div class="user-popup-header">
                <div class="pulse-dot"></div>
                <span class="user-popup-title">Lokasi Kamu Saat Ini</span>
            </div>
            <div class="user-popup-body">
                <p>Titik awal perjalananmu.</p>
                <div class="drag-hint">
                    <i class="ri-drag-move-2-line"></i> Geser pin untuk ubah
                </div>
            </div>
        </div>
    `;

    // Buat Marker
    userMarker = L.marker([lat, long], { icon: redIcon, draggable: true }).addTo(map)
        .bindPopup(popupContent, {
            className: 'custom-user-popup', // Class khusus untuk styling CSS
            closeButton: false,            // Hilangkan tombol X bawaan yang jelek
            autoClose: false,
            closeOnClick: false
        })
        .openPopup();
    
    // Event Drag
    userMarker.on('dragend', function(e) {
        var pos = userMarker.getLatLng();
        // Update posisi lingkaran juga saat marker digeser
        userCircle.setLatLng(pos);
        getAddress(pos.lat, pos.lng); 
    });
}

function getAddress(lat, lng) {
    document.getElementById("inputLat").value = lat;
    document.getElementById("inputLong").value = lng;
    var status = document.getElementById("statusLokasi");
    status.innerHTML = "Ambil alamat...";
    status.style.color = "#d97706"; 
    
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                var shortAddress = data.display_name.split(',').slice(0, 3).join(','); 
                status.innerHTML = "✅ " + shortAddress;
                status.style.color = "green";
                var btn = document.querySelector("button[onclick='getLocation()']");
                if(btn) { 
                    btn.innerHTML = '<span style="font-size:16px;"></span> Lokasi Terkunci'; 
                    btn.classList.add("btn-primary"); 
                }
            } else {
                status.innerHTML = "✅ Lokasi Terkunci";
            }
        })
        .catch(error => {
            console.error(error);
            status.innerHTML = "✅ Koordinat Terkunci";
            status.style.color = "green";
        });
}

function showError(error) { console.log(error); } 
// --- 1. DEKLARASI GLOBAL (Agar bisa diakses fungsi switchBaseMap) ---
var map;
var streetLayer, satelliteLayer, terrainLayer;
var currentBaseLayer; 

document.addEventListener("DOMContentLoaded", function() {
    var startLat = parseFloat(document.getElementById("inputLat").value) || -3.440974;
    var startLong = parseFloat(document.getElementById("inputLong").value) || 114.833500;

    // --- 2. INISIALISASI LAYER (Di dalam DOMContentLoaded) ---
    streetLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '© OpenStreetMap' });
    satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Tiles © Esri' });
    terrainLayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' });

    // Cek LocalStorage
    var savedMap = localStorage.getItem('selectedMap');
    
    // Tentukan layer awal
    if (savedMap === 'satellite') {
        currentBaseLayer = satelliteLayer;
    } else if (savedMap === 'terrain') {
        currentBaseLayer = terrainLayer;
    } else {
        currentBaseLayer = streetLayer; // Default
    }

    // Buat Map
    map = L.map('map', { 
        center: [startLat, startLong], 
        zoom: 12, 
        zoomControl: false, 
        layers: [currentBaseLayer] 
    });

    focusLayer = L.layerGroup().addTo(map);

    // ... (Kode Kompas & Zoom Control kamu tetap sama) ...
    var CompassControl = L.Control.extend({ options: { position: 'topright' }, onAdd: function () { var div = L.DomUtil.create('div', 'compass-control'); div.innerHTML = '<img src="/icons/Logo Kompas.png" alt="Utara" style="width:40px; opacity:0.9;">'; return div; } });
    map.addControl(new CompassControl());
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Pane Polygon
    map.createPane('areaPolygon'); 
    map.getPane('areaPolygon').style.zIndex = 350; 

    // Marker User
    if(document.getElementById("inputLat").value !== "") {
        userMarker = L.marker([startLat, startLong], { icon: redIcon, draggable: true }).addTo(map);
        getAddress(startLat, startLong);
        userMarker.on('dragend', function(e) { getAddress(e.target.getLatLng().lat, e.target.getLatLng().lng); });
    }

    renderRoute(startLat, startLong);
    loadGeoJSON();
    
    // Info Modal
    var hasSeenInfo = localStorage.getItem('hasSeenInfo');
    if (!hasSeenInfo) { setTimeout(function() { openInfoModal(); }, 1000); localStorage.setItem('hasSeenInfo', 'true'); }
});

// --- 3. FUNGSI GANTI BASE MAP (Di Luar DOMContentLoaded) ---
window.switchBaseMap = function(type) {
    // Hapus layer lama
    if (currentBaseLayer) {
        map.removeLayer(currentBaseLayer);
    }

    // Pilih layer baru
    if (type === 'street') {
        currentBaseLayer = streetLayer;
    } else if (type === 'satellite') {
        currentBaseLayer = satelliteLayer;
    } else if (type === 'terrain') {
        currentBaseLayer = terrainLayer;
    }

    // Tambahkan layer baru & Simpan preferensi
    map.addLayer(currentBaseLayer);
    currentBaseLayer.bringToBack(); // Pastikan ada di belakang polygon
    localStorage.setItem('selectedMap', type);
};