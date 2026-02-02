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
    if (userMarker) map.removeLayer(userMarker);
    if (userCircle) map.removeLayer(userCircle);
    
    userCircle = L.circle([lat, long], { color: '#dc2626', fillColor: '#ef4444', fillOpacity: 0.2, radius: 100 }).addTo(map);
    userMarker = L.marker([lat, long], { icon: redIcon, draggable: true }).addTo(map)
        .bindPopup("<b> Lokasi Kamu</b><br>Geser untuk ubah posisi!").openPopup();
    
    userMarker.on('dragend', function(e) {
        var pos = userMarker.getLatLng();
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

document.addEventListener("DOMContentLoaded", function() {
    var startLat = parseFloat(document.getElementById("inputLat").value) || -3.440974;
    var startLong = parseFloat(document.getElementById("inputLong").value) || 114.833500;

    var streetLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '© OpenStreetMap' });
    var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Tiles © Esri' });
    var terrainLayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' });

    var savedMap = localStorage.getItem('selectedMap');
    var activeLayer = streetLayer;
    if (savedMap === ' Satelit') activeLayer = satelliteLayer;
    else if (savedMap === ' Terrain') activeLayer = terrainLayer;

    map = L.map('map', { center: [startLat, startLong], zoom: 12, zoomControl: false, layers: [activeLayer] });
    focusLayer = L.layerGroup().addTo(map);

    // Kompas
    var CompassControl = L.Control.extend({
        options: { position: 'topright' },
        onAdd: function () {
            var div = L.DomUtil.create('div', 'compass-control');
            div.innerHTML = '<img src="/icons/Logo Kompas.png" alt="Utara" style="width:40px; opacity:0.9;">'; 
            return div;
        }
    });
    map.addControl(new CompassControl());
    
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    var baseMaps = { " Peta Jalan": streetLayer, " Satelit": satelliteLayer, " Terrain": terrainLayer };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

    map.on('baselayerchange', function(e) { localStorage.setItem('selectedMap', e.name); });
    map.createPane('areaPolygon'); map.getPane('areaPolygon').style.zIndex = 350; 

    if(document.getElementById("inputLat").value !== "") {
        userMarker = L.marker([startLat, startLong], { icon: redIcon, draggable: true }).addTo(map);
        getAddress(startLat, startLong);
        userMarker.on('dragend', function(e) { getAddress(e.target.getLatLng().lat, e.target.getLatLng().lng); });
    }

    renderRoute(startLat, startLong);
    loadGeoJSON();
    
    // Auto show info for first visitor
    var hasSeenInfo = localStorage.getItem('hasSeenInfo');
    if (!hasSeenInfo) { setTimeout(function() { openInfoModal(); }, 1000); localStorage.setItem('hasSeenInfo', 'true'); }
});