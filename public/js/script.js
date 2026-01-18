/* =========================================================
   SCRIPT UTAMA - SMART ITINERARY BANJARBAKULA (FINAL CLEAN)
   ========================================================= */

// --- 1. DEFINISI VARIABEL GLOBAL & IKON ---
var map = null;
var userMarker = null;
var userCircle = null;
var allMarkers = {};       
var alternativeLayer = null; 
var radiusCircle = null;    
var pendingParentId = null;
var pendingChildId = null;
var pendingBudget = null;
var pendingActionType = "";
var activeMarkerId = null;
var originalIcons = {};
var focusLayer = null;
var geoJsonLayer = null; // Tambahkan ini agar tidak error di mouseout GeoJSON

// Definisi Icon Custom
var redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
var greyIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [20, 32], iconAnchor: [10, 32], popupAnchor: [1, -28], shadowSize: [32, 32] });
var highlightIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [35, 55], iconAnchor: [17, 55], popupAnchor: [1, -34], shadowSize: [41, 41] });

var LeafIcon = L.Icon.extend({ options: { iconSize: [30, 50], iconAnchor: [13, 50], popupAnchor: [0, -10] } });
var icons = {
    Alam:    new LeafIcon({ iconUrl: '/icons/alam.png' }),
    Religi:  new LeafIcon({ iconUrl: '/icons/religi.png' }),
    Kuliner: new LeafIcon({ iconUrl: '/icons/kuliner.png' }),
    Belanja: new LeafIcon({ iconUrl: '/icons/belanja.png' }),
    Budaya:  new LeafIcon({ iconUrl: '/icons/budaya.png'}),
    Edukasi: new LeafIcon({ iconUrl: '/icons/edukasi.png'}),
    Agro:    new LeafIcon({ iconUrl: '/icons/agro.png'}),
    Rekreasi: new LeafIcon({ iconUrl: '/icons/rekreasi.png'}),
    Default: new LeafIcon({ iconUrl: '/icons/default.png' }),
};

// --- 2. HELPER GAMBAR (Mendukung Base64 & URL) ---
function getImageUrl(dbImage) {
    if (!dbImage) return 'https://placehold.co/400x300/e2e8f0/64748b?text=No+Image';
    if (dbImage.startsWith('data:') || dbImage.startsWith('http')) return dbImage;
    if (dbImage.startsWith('/')) dbImage = dbImage.substring(1);
    if (dbImage.startsWith('images/')) return '/' + dbImage;
    return '/images/' + dbImage; 
}

// --- 3. UTILITIES UI (Toast, Loading, Validasi) ---
function showLoading() { document.getElementById('loadingOverlay').style.display = 'flex'; }

function showToast(title, message) {
    const toast = document.getElementById('customToast');
    const titleEl = document.getElementById('toastTitle');
    const bodyEl = document.getElementById('toastBody');
    if(toast && titleEl && bodyEl) {
        titleEl.innerText = title; bodyEl.innerText = message;
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 4000);
    } else { alert(title + "\n" + message); }
}

function validateSearch() {
    if (document.getElementById('inputLat').value === "") {
        showToast("Lokasi Belum Ada!", "Mohon klik 'Deteksi Lokasi' dulu ya 📍");
        return false; 
    }
    showLoading();
    return true;
}

// --- 4. GEOLOCATION & MAP INIT ---
function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    var R = 6371; 
    var dLat = deg2rad(lat2-lat1);  
    var dLon = deg2rad(lon2-lon1); 
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
    return R * c;
}
function deg2rad(deg) { return deg * (Math.PI/180); }

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
        .bindPopup("<b>📍 Lokasi Kamu</b><br>Geser untuk ubah posisi!").openPopup();
    
    userMarker.on('dragend', function(e) {
        var pos = userMarker.getLatLng();
        getAddress(pos.lat, pos.lng); 
    });
}

function getAddress(lat, lng) {
    document.getElementById("inputLat").value = lat;
    document.getElementById("inputLong").value = lng;
    var status = document.getElementById("statusLokasi");
    status.innerHTML = "🔄 Ambil alamat...";
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
                    btn.innerHTML = '<span style="font-size:16px;">📍</span> Lokasi Terkunci'; 
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


// --- 5. MAP INITIALIZATION (UTAMA) ---
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


// --- 6. RENDER ROUTE (LOGIKA VISUALISASI RUTE) ---
function renderRoute(startLat, startLong) {
    if (typeof wisataData !== 'undefined' && wisataData !== null) {
        var rutePoints = [[startLat, startLong]]; 
        var currentUrlObj = new URL(window.location.href);
        var manualRouteIds = currentUrlObj.searchParams.getAll('rute_fix[]');

        wisataData.forEach(function(w) {
            var selectedIcon = icons[w.kategori] || icons.Default;
            var marker = L.marker([w.latitude, w.longitude], {icon: selectedIcon}).addTo(map);
            allMarkers[w.id] = marker;
            originalIcons[w.id] = selectedIcon;
            
            var hargaText = w.harga_tiket == 0 ? "Gratis" : "Rp " + new Intl.NumberFormat('id-ID').format(w.harga_tiket);
            var distFromStart = getDistanceFromLatLonInKm(startLat, startLong, w.latitude, w.longitude);
            var isManualSelection = manualRouteIds.includes(w.id.toString());
            var isStartPoint = distFromStart < 0.05;

            var btnHapusHTML = (isManualSelection && !isStartPoint) ? 
                `<button onclick="removeFromRoute(${w.id})" class="btn-modern btn-red-solid">Hapus dari Rute</button>` : '';
            
            var btnMulaiHTML = (!isStartPoint) ? 
                `<button onclick="rerouteTo(${w.latitude}, ${w.longitude}, ${w.harga_tiket}, ${w.id})" class="btn-modern btn-indigo-outline">Mulai Rute Dari Sini</button>` : '';

            var finalImage = getImageUrl(w.gambar);

            var popupContent = `
                <div class="popup-card">
                    <div class="popup-image-container">
                        <span class="distance-badge">📍 ${distFromStart.toFixed(1)} KM</span>
                        <button onclick="openDetailPanelById(${w.id})" class="btn-detail-corner" title="Lihat Detail">↗</button>
                        <img src="${finalImage}" class="popup-image" alt="${w.nama_tempat}" onerror="this.src='https://placehold.co/400?text=IMG'">
                    </div>
                    <div class="popup-info">
                        <h3 class="popup-title">${w.nama_tempat}</h3>
                        <span class="popup-price">${hargaText}</span>
                    </div>
                    <div class="popup-actions-container">
                        <button onclick="showNearbyWisata(${w.latitude}, ${w.longitude}, '${w.nama_tempat}', ${w.id})" class="btn-popup-radius btn-modern btn-orange-soft" style="width: 100%;">Wisata Sekitar</button>
                        ${btnHapusHTML} ${btnMulaiHTML}
                    </div>
                </div>`;
            marker.bindPopup(popupContent);
            rutePoints.push([w.latitude, w.longitude]);
        });

        if (rutePoints.length > 1) {
            var polyline = L.polyline(rutePoints, { color: '#6366f1', weight: 4, opacity: 0.8, dashArray: '10, 20' }).addTo(map);
            if (typeof L.polylineDecorator === 'function') {
                L.polylineDecorator(polyline, { patterns: [{ offset: '100%', repeat: 0, symbol: L.Symbol.arrowHead({ pixelSize: 15, polygon: false, pathOptions: { stroke: true, color: '#4f46e5', weight: 3 } }) }, { offset: 25, repeat: 70, symbol: L.Symbol.arrowHead({ pixelSize: 10, polygon: true, pathOptions: { stroke: false, fillOpacity: 1, color: '#6366f1' } }) }] }).addTo(map);
            }
            map.fitBounds(polyline.getBounds(), {padding: [50, 50]});
        }
    }
}

// --- 7. NEARBY WISATA SEARCH (DYNAMIC RADIUS) ---
window.showNearbyWisata = function(centerLat, centerLng, centerName, parentId) {
    if (alternativeLayer) map.removeLayer(alternativeLayer);
    if (radiusCircle) map.removeLayer(radiusCircle);

    alternativeLayer = L.layerGroup().addTo(map);

    // 1. AMBIL NILAI DARI INPUT USER
    var inputRadius = document.getElementById('inputRadius');
    var userRadius = inputRadius ? parseInt(inputRadius.value) : 3; // Default 10 jika input tidak ketemu
    
    // Pastikan minimal 1 KM agar tidak error
    const RADIUS_KM = userRadius > 0 ? userRadius : 3; 

    // Visualisasi Lingkaran
    radiusCircle = L.circle([centerLat, centerLng], {
        radius: RADIUS_KM * 1000, // Konversi KM ke Meter
        color: '#f59e0b', 
        fillOpacity: 0.1
    }).addTo(map);

    let count = 0;
    wisataLainData.forEach(w => {
        const jarak = getDistanceFromLatLonInKm(centerLat, centerLng, w.latitude, w.longitude);
        
        // Cek apakah masuk radius user
        if (jarak <= RADIUS_KM) {
            const marker = L.marker([w.latitude, w.longitude], { icon: greyIcon });
            const finalImage = getImageUrl(w.gambar);
            
            const popupContent = `
                <div class="popup-card">
                    <div class="popup-image-container">
                        <span class="distance-badge" style="background:#f59e0b;">Jarak: ${jarak.toFixed(1)} KM</span>
                        <img src="${finalImage}" class="popup-image" onerror="this.src='https://placehold.co/400?text=IMG'">
                    </div>
                    <div class="popup-info">
                        <h3 class="popup-title">${w.nama_tempat}</h3>
                        <p class="popup-price">${w.harga_tiket == 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID')}</p>
                    </div>
                    <div class="popup-actions-container">
                        <button onclick="setConnectedRoute(${parentId}, ${w.id}, ${w.harga_tiket})" class="btn-modern btn-green-solid">Sambung Rute</button>
                    </div>
                </div>`;
            marker.bindPopup(popupContent);
            alternativeLayer.addLayer(marker);
            allMarkers[w.id] = marker;
            count++;
        }
    });

    if (count === 0) {
        showToast("Info", `Tidak ada wisata dalam radius ${RADIUS_KM} KM`);
    } else {
        showToast("Berhasil", `Ditemukan ${count} wisata dalam radius ${RADIUS_KM} KM`);
    }

    map.fitBounds(radiusCircle.getBounds());
};


// --- 8. GEOJSON AREA (BATAS WILAYAH) ---
function loadGeoJSON() {
    fetch('/data/Batas_Banjarbakula.geojson')
        .then(response => { if (!response.ok) throw new Error("Gagal load GeoJSON"); return response.json(); })
        .then(data => {
            geoJsonLayer = L.geoJSON(data, {
                pane: 'areaPolygon',
                style: function(feature) {
                    var nama = feature.properties.nama || feature.properties.NAMOBJ || ""; 
                    var warna = '#3388ff';
                    if (nama.toUpperCase().includes('BANJARMASIN')) warna = '#ef4444';
                    else if (nama.toUpperCase().includes('BANJARBARU')) warna = '#22c55e';
                    else if (nama.toUpperCase().includes('BANJAR')) warna = '#3b82f6';
                    return { color: warna, fillColor: warna, fillOpacity: 0.1, weight: 2, dashArray: '5, 5' };
                },
                onEachFeature: function(feature, layer) {
                    var nama = feature.properties.nama || feature.properties.NAMOBJ || "Wilayah Banjarbakula";
                    layer.on({
                        mouseover: function(e) {
                            var layer = e.target;
                            layer.setStyle({ weight: 2, color: '#f59e0b', dashArray: '', fillOpacity: 0.2 });
                            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) { layer.bringToFront(); }
                        },
                        mouseout: function(e) { geoJsonLayer.resetStyle(e.target); },
                        click: function(e) { 
                            L.popup({ className: 'region-popup', closeButton: false, autoPan: false, offset: [0, -10] })
                            .setLatLng(e.latlng).setContent(nama).openOn(map);
                        }
                    });
                }
            }).addTo(map);
        })
        .catch(error => console.error('Error memuat peta wilayah:', error));
}


// --- 9. MODAL & LOGIC AKSI (SAMBUNG / REROUTE / HAPUS) ---
window.confirmAction = function() {
    window.closeCustomModal(); // Tutup dulu
    if (pendingActionType === "SAMBUNG") {
        executeRedirect(pendingBudget, pendingParentId, pendingChildId);
    } else if (pendingActionType === "REROUTE") {
        executeRerouteLogic(pendingParentId, pendingChildId, pendingBudget);
    } else if (pendingActionType === "HAPUS") {
        var url = new URL(window.location.href);
        var currentIds = url.searchParams.getAll('rute_fix[]');
        if(currentIds.length === 0) currentIds = url.searchParams.getAll('rute_fix');
        
        var newIds = currentIds.filter(id => id.toString() !== pendingChildId.toString());
        url.searchParams.delete('rute_fix[]');
        url.searchParams.delete('rute_fix');
        url.searchParams.delete('parent_id'); 
        
        newIds.forEach(id => { url.searchParams.append('rute_fix[]', id); });
        window.location.href = url.toString();
    }
};

window.closeCustomModal = function() {
    var modal = document.getElementById('custom-modal-overlay');
    if(modal) modal.style.display = 'none';
};

// Logic Reroute (Mulai Dari Sini)
window.rerouteTo = function(newLat, newLng, hargaTiket, idWisata) {
    var currentBudget = parseInt(document.querySelector('input[name="budget"]').value);
    pendingParentId = newLat;    // Simpan Lat di sini untuk Reroute
    pendingChildId  = newLng;    // Simpan Lng di sini untuk Reroute
    pendingBudget   = currentBudget;
    pendingActionType = "REROUTE";

    showCustomModal('📍', 'Mulai Dari Sini?', 'Lokasi ini akan dijadikan Titik Awal baru.', 'Ya, Mulai', '#6366f1');
}

function executeRerouteLogic(newLat, newLng, pendingBudget) {
    document.getElementById('inputLat').value = newLat;
    document.getElementById('inputLong').value = newLng;
    localStorage.setItem('userLat', newLat);
    localStorage.setItem('userLong', newLng);

    var url = new URL(window.location.href);
    url.searchParams.delete('rute_fix[]');
    url.searchParams.delete('rute_fix');
    url.searchParams.delete('parent_id'); 
    url.searchParams.set('lat', newLat); 
    url.searchParams.set('long', newLng);
    url.searchParams.set('action', 'cari_rute'); // Trigger otomatis
    if (Number.isFinite(pendingBudget)) url.searchParams.set('budget', pendingBudget);
    window.location.href = url.toString();
}

// Logic Sambung Rute
window.setConnectedRoute = function(parentId, childId, hargaTiket) {
    var totalBudget = parseInt(document.querySelector('input[name="budget"]').value) || 0;
    var currentSisa = (typeof window.realSisaBudget !== 'undefined') ? parseInt(window.realSisaBudget) : totalBudget;
    var sisaJikaDipilih = currentSisa - hargaTiket;

    pendingParentId = parentId; pendingChildId = childId; pendingBudget = totalBudget; 
    pendingActionType = "SAMBUNG";

    if (sisaJikaDipilih < 0) {
        showCustomModal('⚠️', 'Budget Kurang!', `Dana kurang <b>Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(sisaJikaDipilih))}</b>. Yakin mau terobos?`, 'Terobos Aja', '#ef4444');
    } else {
        showCustomModal('🔗', 'Sambung Rute?', 'Wisata ini akan ditambahkan ke rute.', 'Ya, Sambung', '#6366f1');
    }
}

function executeRedirect(budget, parentId, childId) {
    var url = new URL(window.location.href);
    url.searchParams.set('budget', budget); 
    var existingIds = url.searchParams.getAll('rute_fix[]').length ? url.searchParams.getAll('rute_fix[]') : url.searchParams.getAll('rute_fix');
    
    url.searchParams.delete('rute_fix[]'); url.searchParams.delete('rute_fix');
    url.searchParams.delete('parent_id'); url.searchParams.delete('prioritas_id'); 

    var newIdList = new Set();
    existingIds.forEach(id => { if(id && id !== "0") newIdList.add(parseInt(id)); });
    if (parentId && parentId !== 0) newIdList.add(parseInt(parentId));
    if (childId) newIdList.add(parseInt(childId));

    Array.from(newIdList).forEach(id => url.searchParams.append('rute_fix[]', id));
    url.searchParams.set('action', 'cari_rute');
    window.location.href = url.toString();
}

window.removeFromRoute = function(idToRemove) {
    pendingChildId = idToRemove; pendingActionType = "HAPUS";
    showCustomModal('🗑️', 'Hapus Lokasi?', 'Lokasi ini akan dihapus dari daftar.', 'Hapus', '#ef4444');
}

// Helper Tampilkan Modal
function showCustomModal(icon, title, desc, btnText, btnColor) {
    var htmlContent = `
        <div style="text-align:center; margin-bottom:15px;">
            <div style="font-size:3rem;">${icon}</div><h3>${title}</h3>
        </div>
        <p>${desc}</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button onclick="window.closeCustomModal()" class="cm-btn cm-btn-cancel">Batal</button>
            <button onclick="window.confirmAction()" class="cm-btn" style="background: ${btnColor}; color: white;">${btnText}</button>
        </div>`;
    var container = document.querySelector('#custom-modal-overlay > div');
    if(container) {
        container.innerHTML = htmlContent;
        document.getElementById('custom-modal-overlay').style.display = 'flex';
    } else {
        console.error("Modal Container Missing in HTML");
    }
}


// --- 10. DETAIL PANEL & CAROUSEL ---
function highlightMarker(id) {
    if (activeMarkerId && allMarkers[activeMarkerId]) {
        allMarkers[activeMarkerId].setIcon(originalIcons[activeMarkerId]);
    }
    if (allMarkers[id]) {
        allMarkers[id].setIcon(highlightIcon);
        map.flyTo(allMarkers[id].getLatLng(), 16, { animate: true, duration: 1 });
        allMarkers[id].openPopup();
        activeMarkerId = id;
    }
}

// Global List untuk Navigasi Panel
let wisataList = [];
let currentIndex = 0;
document.addEventListener("DOMContentLoaded", function () {
    if (Array.isArray(window.wisataData)) wisataList = window.wisataData;
});

window.openDetailPanelById = function(id) {
    if (!Array.isArray(window.wisataData)) return;
    wisataList = window.wisataData;
    currentIndex = wisataList.findIndex(w => w.id === id);
    if (currentIndex === -1) return;

    const panel = document.getElementById('detailPanel');
    panel.classList.add('active');
    setTimeout(() => { updatePanel(); highlightMarker(id); }, 50);
}

function updatePanel() {
    if (!wisataList || wisataList.length === 0) return;
    const wisata = wisataList[currentIndex];
    
    document.getElementById('panelNama').innerText = wisata.nama_tempat;
    document.getElementById('panelKategori').innerText = wisata.kategori || 'Umum';
    document.getElementById('panelAlamat').innerText = wisata.alamat || '-';
    document.getElementById('panelHarga').innerText = wisata.harga_tiket == 0 ? 'Gratis' : 'Rp ' + new Intl.NumberFormat('id-ID').format(wisata.harga_tiket);

    // Logic Carousel Image
    let images = [];
    if (wisata.galeri) {    
        if (Array.isArray(wisata.galeri)) images = wisata.galeri;
        else if (typeof wisata.galeri === 'string') {
            try { images = JSON.parse(wisata.galeri); } catch (e) {}
        }
    }
    if (images.length === 0 && wisata.gambar) images = [wisata.gambar];
    buildCarousel(images);

    // Action Buttons
    const btnRute = document.getElementById('panelRuteBtn');
    if(btnRute) btnRute.onclick = () => rerouteTo(wisata.latitude, wisata.longitude, wisata.harga_tiket, wisata.id);
}

window.nextWisata = function() {
    currentIndex = (currentIndex < wisataList.length - 1) ? currentIndex + 1 : 0;
    updatePanel(); highlightMarker(wisataList[currentIndex].id);
}
window.prevWisata = function() {
    currentIndex = (currentIndex > 0) ? currentIndex - 1 : wisataList.length - 1;
    updatePanel(); highlightMarker(wisataList[currentIndex].id);
}
window.closeDetailPanel = function() {
    document.getElementById('detailPanel').classList.remove('active');
    if (activeMarkerId && allMarkers[activeMarkerId]) allMarkers[activeMarkerId].setIcon(originalIcons[activeMarkerId]);
    activeMarkerId = null;
}

// Carousel Logic
let carouselImages = [];
let carouselIndex = 0;
function buildCarousel(images) {
    const track = document.getElementById('carouselTrack');
    const dots = document.getElementById('carouselDots');
    if (!track || !dots) return;

    track.innerHTML = ''; dots.innerHTML = ''; carouselIndex = 0; carouselImages = images;
    
    images.forEach((img, i) => {
        const imageEl = document.createElement('img');
        imageEl.src = getImageUrl(img);
        track.appendChild(imageEl);
        const dot = document.createElement('span');
        dot.onclick = () => goToImage(i);
        if (i === 0) dot.classList.add('active');
        dots.appendChild(dot);
    });
    updateCarousel();
}
function updateCarousel() {
    document.getElementById('carouselTrack').style.transform = `translateX(-${carouselIndex * 100}%)`;
    document.querySelectorAll('.carousel-dots span').forEach((d, i) => d.classList.toggle('active', i === carouselIndex));
}
window.nextImage = function() { carouselIndex = (carouselIndex + 1) % carouselImages.length; updateCarousel(); }
window.prevImage = function() { carouselIndex = (carouselIndex === 0) ? carouselImages.length - 1 : carouselIndex - 1; updateCarousel(); }
function goToImage(index) { carouselIndex = index; updateCarousel(); }


// --- 11. SIDEBAR & DIRECTORY UI ---
window.toggleSidebar = function() {
    document.getElementById('mainSidebar').classList.toggle('sidebar-closed');
    var btn = document.getElementById('btnShowSidebar');
    btn.style.display = (btn.style.display === 'none') ? 'block' : 'none';
}

window.toggleFilterMenu = function() { 
    var menu = document.getElementById('menuPilihan'); 
    menu.style.display = (menu.style.display === "none") ? "block" : "none"; 
}

window.openDirectoryModal = function() { 
    document.getElementById('directoryModal').style.display = 'flex'; 
    document.getElementById('dirSearchInput').value = "";
    document.getElementById('dirRegionSelect').value = "";
    applyDirectoryFilter(); 
}
window.closeDirectoryModal = function() { document.getElementById('directoryModal').style.display = 'none'; }

window.applyDirectoryFilter = function () {
    const container = document.getElementById('directoryList'); container.innerHTML = "";
    const searchText = document.getElementById('dirSearchInput').value.toLowerCase();
    const region = document.getElementById('dirRegionSelect').value;
    const kategori = document.getElementById('dirKategoriSelect').value;
    const budgetMax = document.getElementById('dirBudgetSelect').value;

    let count = 0;
    allWisataData.forEach(w => {
        if (!w.nama_tempat.toLowerCase().includes(searchText)) return;
        if (region && !w.alamat?.toLowerCase().includes(region.toLowerCase())) return;
        if (kategori && w.kategori !== kategori) return;
        if (budgetMax !== "" && parseInt(w.harga_tiket) > parseInt(budgetMax)) return;

        const card = document.createElement('div'); card.className = 'dir-card';
        card.onclick = () => { focusOnLocation(w.latitude, w.longitude, w.id); closeDirectoryModal(); };
        card.innerHTML = `
            <div class="dir-card-img-wrapper">
                <img src="${getImageUrl(w.gambar)}" loading="lazy" onerror="this.src='https://placehold.co/400?text=IMG'">
            </div>
            <div class="dir-card-body">
                <div class="dir-card-title">${w.nama_tempat}</div>
                <div class="dir-card-footer">
                    <span class="dir-cat">${w.kategori || 'Umum'}</span>
                    <span class="dir-price">${w.harga_tiket == 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID')}</span>
                </div>
            </div>`;
        container.appendChild(card); count++;
    });

    if (count === 0) container.innerHTML = `<div class="directory-empty">Tidak ada wisata sesuai filter</div>`;
};

window.focusOnLocation = function(lat, lng, id) {
    focusLayer.clearLayers();
    const target = allWisataData.find(w => w.id == id);
    if (!target) return;

    const iconWisata = icons[target.kategori] || icons.Default;
    const marker = L.marker([lat, lng], { icon: iconWisata }).addTo(focusLayer);
    map.flyTo([lat, lng], 16, { animate: true });

    // Show popup immediately with minimal actions
    const popupContent = `
        <div class="popup-card">
            <div class="popup-image-container">
                <img src="${getImageUrl(target.gambar)}" class="popup-image">
            </div>
            <div class="popup-info"><h3 class="popup-title">${target.nama_tempat}</h3></div>
            <div class="popup-actions-container">
                 <button onclick="rerouteTo(${target.latitude}, ${target.longitude}, ${target.harga_tiket}, ${target.id})" class="btn-modern btn-indigo-outline">Mulai Rute Dari Sini</button>
            </div>
        </div>`;
    marker.bindPopup(popupContent).openPopup();
};

window.openInfoModal = function() { document.getElementById('infoModal').style.display = 'flex'; }
window.closeInfoModal = function() { document.getElementById('infoModal').style.display = 'none'; }
window.toggleDropdown = function() { document.getElementById('kategoriDropdown').classList.toggle('active'); }
window.updateDisplay = function(text, iconName) {
    document.getElementById('displayText').innerText = text;
    if(document.getElementById('displayIcon')) document.getElementById('displayIcon').src = '/icons/' + iconName;
    document.getElementById('kategoriDropdown').classList.remove('active');
}

/* --- LOGIKA TOGGLE FILTER DIREKTORI --- */
window.toggleDirectoryFilters = function() {
    var panel = document.getElementById('dirFilterPanel');
    var btn = document.getElementById('btnDirFilter');
    
    // Toggle Class 'show' untuk animasi slide
    if (panel.classList.contains('show')) {
        panel.classList.remove('show');
        btn.classList.remove('active');
        btn.innerHTML = '<i class="ri-equalizer-line"></i>'; // Ikon Filter Normal
    } else {
        panel.classList.add('show');
        btn.classList.add('active');
        btn.innerHTML = '<i class="ri-close-line"></i>'; // Ikon Close (X)
    }
}

// Reset filter saat modal dibuka (Opsional, agar rapi setiap dibuka)
var originalOpenDir = window.openDirectoryModal;
window.openDirectoryModal = function() {
    // Reset UI Panel Filter (Tutup dulu)
    document.getElementById('dirFilterPanel').classList.remove('show');
    document.getElementById('btnDirFilter').classList.remove('active');
    
    // Jalankan fungsi asli pembuka modal
    document.getElementById('directoryModal').style.display = 'flex'; 
    document.getElementById('dirSearchInput').value = "";
    document.getElementById('dirRegionSelect').value = "";
    applyDirectoryFilter(); 
};