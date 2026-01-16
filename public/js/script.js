/* =========================================================
   SCRIPT UTAMA - SMART ITINERARY BANJARBAKULA (STABLE VERSION + FIX)
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



// Definisi Icon
var redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
var greyIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [20, 32], iconAnchor: [10, 32], popupAnchor: [1, -28], shadowSize: [32, 32] });
var LeafIcon = L.Icon.extend({ options: { iconSize: [30, 50], iconAnchor: [13, 50], popupAnchor: [0, -10] } });

  var highlightIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [35, 55],
                iconAnchor: [17, 55],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

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

// --- HELPER: FORMAT GAMBAR ---
function getImageUrl(dbImage) {
    if (!dbImage) return 'https://placehold.co/400x300/e2e8f0/64748b?text=No+Image';
    if (dbImage.startsWith('http')) return dbImage;
    return '/storage/' + dbImage; 
}

// --- 2. MODAL & CONFIRM ACTION ---
window.confirmAction = function() {
    if (pendingActionType === "SAMBUNG") {
        executeRedirect(pendingBudget, pendingParentId, pendingChildId);
    } else if (pendingActionType === "REROUTE") {
        // INI YANG SEBELUMNYA ERROR KARENA FUNGSI TIDAK ADA
        executeRerouteLogic(pendingParentId, pendingChildId, pendingBudget);
    } else if (pendingActionType === "HAPUS") {
        var url = new URL(window.location.href);
        var currentIds = url.searchParams.getAll('rute_fix[]');
        if(currentIds.length === 0) currentIds = url.searchParams.getAll('rute_fix');
        
        var newIds = currentIds.filter(id => id.toString() !== pendingChildId.toString());
        
        url.searchParams.delete('rute_fix[]');
        url.searchParams.delete('rute_fix');
        url.searchParams.delete('parent_id'); 
        url.searchParams.delete('prioritas_id'); 
        
        newIds.forEach(id => { url.searchParams.append('rute_fix[]', id); });
        window.location.href = url.toString();
    }
    window.closeCustomModal();
};

window.closeCustomModal = function() {
    var modal = document.getElementById('custom-modal-overlay');
    if(modal) modal.style.display = 'none';
    pendingActionType = ""; 
};

// --- 3. UTILITIES ---
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

function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    var R = 6371; 
    var dLat = deg2rad(lat2-lat1);  
    var dLon = deg2rad(lon2-lon1); 
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
    return R * c;
}
function deg2rad(deg) { return deg * (Math.PI/180); }

// --- 4. GEOLOCATION ---
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
                if(btn) { btn.innerHTML = "📍 Lokasi Terkunci"; btn.classList.add("btn-primary"); }
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

// --- 5. INITIALIZE MAP ---
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

    // --- TAMBAHAN: ARAH MATA ANGIN (COMPASS) ---
    var CompassControl = L.Control.extend({
        options: {
            position: 'topright' // Posisi: Kiri Bawah
        },
        onAdd: function () {
            var div = L.DomUtil.create('div', 'compass-control');
            div.innerHTML = '<img src="/icons/Logo Kompas.png" alt="Arah Mata Angin">'; 
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
    
    var hasSeenInfo = localStorage.getItem('hasSeenInfo');
    if (!hasSeenInfo) { setTimeout(function() { openInfoModal(); }, 1000); localStorage.setItem('hasSeenInfo', 'true'); }
});

function highlightMarker(id) {
    // reset marker lama
    if (activeMarkerId && allMarkers[activeMarkerId]) {
        allMarkers[activeMarkerId].setIcon(originalIcons[activeMarkerId]);
    }

    // set marker baru
    if (allMarkers[id]) {
        allMarkers[id].setIcon(highlightIcon);
        map.flyTo(allMarkers[id].getLatLng(), 16, { animate: true, duration: 1 });
        allMarkers[id].openPopup();
        activeMarkerId = id;
    }
}



// --- 6. RENDER ROUTE & MARKERS ---
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
                        <button 
                            onclick="openDetailPanelById(${w.id})"
                            class="btn-detail-corner"
                            title="Lihat Detail">
                            ↗
                        </button>


                        <img src="${finalImage}" class="popup-image" alt="${w.nama_tempat}">
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

// --- 7. NEARBY PLACES ---
function isUserLocationDetected() {
    return document.getElementById("inputLat").value !== "" &&
           document.getElementById("inputLong").value !== "";
}

function buildPopup(wisata, buttonsHTML) {
    return `
        <div class="popup-card">
            <div class="popup-image-container">
                <img src="${getImageUrl(wisata.gambar)}" class="popup-image">
            </div>
            <div class="popup-info">
                <h3 class="popup-title">${wisata.nama_tempat}</h3>
                <p class="popup-price">
                    ${wisata.harga_tiket == 0 ? 'Gratis' : 'Rp ' + Number(wisata.harga_tiket).toLocaleString('id-ID')}
                </p>
            </div>
            <div class="popup-actions-container">
                ${buttonsHTML}
            </div>
        </div>
    `;
}


// --- 3. NEARBY WISATA ---
window.showNearbyWisata = function(centerLat, centerLng, centerName, parentId) {
    if (alternativeLayer) map.removeLayer(alternativeLayer);
    if (radiusCircle) map.removeLayer(radiusCircle);

    alternativeLayer = L.layerGroup().addTo(map);

    const RADIUS_KM = 10;

    radiusCircle = L.circle([centerLat, centerLng], {
        radius: RADIUS_KM * 1000,
        color: '#f59e0b',
        fillOpacity: 0.1
    }).addTo(map);

    let count = 0;

    wisataLainData.forEach(w => {
        const jarak = getDistanceFromLatLonInKm(
            centerLat,
            centerLng,
            w.latitude,
            w.longitude
        );

        if (jarak <= RADIUS_KM) {
            const marker = L.marker([w.latitude, w.longitude], { icon: greyIcon });

            const finalImage = getImageUrl(w.gambar);

            const popupContent = `
                <div class="popup-card">
                    <div class="popup-image-container">
                        <img src="${getImageUrl(w.gambar)}" class="popup-image">
                    </div>

                    <div class="popup-info">
                        <h3 class="popup-title">${w.nama_tempat}</h3>
                        <p class="popup-price">
                            ${w.harga_tiket == 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID')}
                        </p>
                    </div>

                    <div class="popup-actions-container">
                        <button 
                            onclick="setConnectedRoute(${parentId}, ${w.id}, ${w.harga_tiket})"
                            class="btn-modern btn-green-solid"
                        >
                            Sambung Rute
                        </button>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);
            alternativeLayer.addLayer(marker);
            allMarkers[w.id] = marker;
            count++;
        }
    });

    if (count === 0) {
        showToast(
            "Tidak Ada Wisata Sekitar",
            "Tidak ditemukan wisata dalam radius 10 KM"
        );
    }

    map.fitBounds(radiusCircle.getBounds());
};


// --- 8. GEOJSON AREA ---
function loadGeoJSON() {
    fetch('/data/Batas_Banjarbakula.geojson')
        .then(response => { if (!response.ok) throw new Error("Gagal load GeoJSON"); return response.json(); })
        .then(data => {
            
            // [PERBAIKAN DISINI] Tambahkan 'geoJsonLayer =' agar mouseout bisa reset style
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
                            layer.setStyle({
                                weight: 2, color: '#f59e0b', dashArray: '', fillOpacity: 0.2
                            });
                            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) { layer.bringToFront(); }
                        },
                        mouseout: function(e) { geoJsonLayer.resetStyle(e.target); },
                        
                        // [MODIFIKASI DISINI] Gunakan className custom & matikan tombol close
                        click: function(e) { 
                            L.popup({
                                className: 'region-popup', // Panggil CSS baru tadi
                                closeButton: false,        // Hilangkan tombol X
                                autoPan: false,            // Supaya peta tidak geser kaget
                                offset: [0, -10]           // Sedikit naik ke atas
                            })
                            .setLatLng(e.latlng)
                            .setContent(nama) // Langsung nama saja, bold sudah diatur di CSS
                            .openOn(map);
                        }
                    });
                }
            }).addTo(map);
        })
        .catch(error => console.error('Error memuat peta wilayah:', error));
}

function getWisataRouteStatus(id) {
    const inRuteUtama = Array.isArray(wisataData) && wisataData.some(w => w.id == id);
    const inWisataLain = Array.isArray(wisataLainData) && wisataLainData.some(w => w.id == id);

    if (inRuteUtama) return 'CONNECTED';
    if (inWisataLain) return 'CAN_CONNECT';
    return 'FREE';
}

function getLastRouteId() {
    const url = new URL(window.location.href);
    const ids = url.searchParams.getAll('rute_fix[]');
    return ids.length ? parseInt(ids[ids.length - 1]) : null;
}



// --- 9. REROUTE & CONNECT ROUTE ---
window.rerouteTo = function(newLat, newLng, hargaTiket, idWisata) {
    var currentBudget = parseInt(document.querySelector('input[name="budget"]').value);

    pendingParentId = idWisata;   // ✅ ID, bukan lat
    pendingChildId  = null;
    pendingBudget   = currentBudget;
    pendingActionType = "REROUTE";

    var htmlContent = `
        <div style="text-align:center; margin-bottom:15px;">
            <div style="font-size:3rem;">📍</div><h3>Mulai Dari Sini?</h3>
        </div>
        <p>Lokasi ini akan dijadikan <b>Titik Awal</b> perjalananmu.</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button onclick="window.closeCustomModal()" class="cm-btn cm-btn-cancel">Batal</button>
            <button onclick="window.confirmAction()" class="cm-btn" style="background: #6366f1; color: white;">Ya, Mulai</button>
        </div>`;
    
    var modalContainer = document.querySelector('#custom-modal-overlay > div');
    modalContainer.innerHTML = htmlContent;
    document.getElementById('custom-modal-overlay').style.display = 'flex';
}

// [INI FUNGSI YANG SAYA TAMBAHKAN AGAR TOMBOL MULAI BERFUNGSI]

function executeRerouteLogic(newLat, newLng, pendingBudget) {
    // 1. Update Lokasi di Browser & Storage
    document.getElementById('inputLat').value = newLat;
    document.getElementById('inputLong').value = newLng;
    localStorage.setItem('userLat', newLat);
    localStorage.setItem('userLong', newLng);

    // 2. Manipulasi URL (INI KUNCINYA)
    var url = new URL(window.location.href);

    // Hapus rute lama
    url.searchParams.delete('rute_fix[]');
    url.searchParams.delete('rute_fix');
    url.searchParams.delete('parent_id'); 
    
    // [PENTING] Update Parameter Lat & Long di URL agar Backend tahu lokasi berubah
    url.searchParams.set('lat', newLat); 
    url.searchParams.set('long', newLng);
    
    // Set Budget
    if (Number.isFinite(pendingBudget)) {
        url.searchParams.set('budget', pendingBudget);
    }

    
    // 3. Redirect (Reload dengan koordinat baru)
    window.location.href = url.toString();
}

window.setConnectedRoute = function(parentId, childId, hargaTiket) {
    var totalBudget = parseInt(document.querySelector('input[name="budget"]').value) || 0;
    var currentSisa = (typeof window.realSisaBudget !== 'undefined') ? parseInt(window.realSisaBudget) : totalBudget;
    var sisaJikaDipilih = currentSisa - hargaTiket;

    pendingParentId = parentId; pendingChildId = childId; pendingBudget = totalBudget; 
    pendingActionType = "SAMBUNG";

    var htmlContent = (sisaJikaDipilih < 0) ? `
        <div style="text-align:center; margin-bottom:15px;"><div style="font-size:3rem;">⚠️</div><h3>Budget Kurang!</h3></div>
        <p>Dana kurang <b>Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(sisaJikaDipilih))}</b>. Yakin mau terobos?</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button onclick="window.closeCustomModal()" class="cm-btn cm-btn-cancel">Batal</button>
            <button onclick="window.confirmAction()" class="cm-btn cm-btn-confirm">Terobos Aja</button>
        </div>` : `
        <div style="text-align:center; margin-bottom:15px;"><div style="font-size:3rem;">🔗</div><h3>Sambung Rute?</h3></div>
        <p>Wisata ini akan dimasukkan ke urutan perjalananmu.</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button onclick="window.closeCustomModal()" class="cm-btn cm-btn-cancel">Batal</button>
            <button onclick="window.confirmAction()" class="cm-btn" style="background: #6366f1; color: white;">Ya, Lanjutkan</button>
        </div>`;

    var modalContainer = document.querySelector('#custom-modal-overlay > div');
    modalContainer.innerHTML = htmlContent;
    document.getElementById('custom-modal-overlay').style.display = 'flex';
}

function executeRedirect(budget, parentId, childId) {
    var url = new URL(window.location.href);
    url.searchParams.set('budget', budget); 
    var existingIds = url.searchParams.getAll('rute_fix[]').length ? url.searchParams.getAll('rute_fix[]') : url.searchParams.getAll('rute_fix');
    
    url.searchParams.delete('rute_fix[]'); url.searchParams.delete('rute_fix');
    url.searchParams.delete('parent_id'); url.searchParams.delete('prioritas_id'); 

    var newIdList = new Set();
    existingIds.forEach(id => { if(id && id !== "0") newIdList.add(parseInt(id)); });
    if (parentId && parentId !== 0 && parentId !== "0") newIdList.add(parseInt(parentId));
    if (childId) newIdList.add(parseInt(childId));

    Array.from(newIdList).forEach(id => url.searchParams.append('rute_fix[]', id));
    window.location.href = url.toString();
}

window.removeFromRoute = function(idToRemove) {
    pendingChildId = idToRemove; pendingActionType = "HAPUS";
    var htmlContent = `
        <div style="text-align:center; margin-bottom:15px;"><div style="font-size: 3.5rem;">🗑️</div><h3>Hapus Lokasi?</h3></div>
        <p style="text-align:center;">Lokasi ini akan dihapus dari daftar rute.</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button onclick="closeCustomModal()" class="cm-btn cm-btn-cancel">Batal</button>
            <button onclick="confirmAction()" class="cm-btn cm-btn-confirm">Hapus</button>
        </div>`;
    var modalContainer = document.querySelector('#custom-modal-overlay > div');
    modalContainer.innerHTML = htmlContent;
    document.getElementById('custom-modal-overlay').style.display = 'flex';
}

// --- 10. DIRECTORY & UI LOGIC ---
window.toggleSidebar = function() {
    var sidebar = document.getElementById('mainSidebar');
    var btnShow = document.getElementById('btnShowSidebar');
    if (sidebar.classList.contains('sidebar-closed')) {
        sidebar.classList.remove('sidebar-closed'); btnShow.style.display = 'none';
    } else {
        sidebar.classList.add('sidebar-closed'); btnShow.style.display = 'block';
    }
}

window.openDirectoryModal = function() { 
    document.getElementById('directoryModal').style.display = 'flex'; 
    document.getElementById('dirSearchInput').value = "";
    document.getElementById('dirRegionSelect').value = "";
    applyDirectoryFilter(); 
}
window.closeDirectoryModal = function() { document.getElementById('directoryModal').style.display = 'none'; }

window.applyDirectoryFilter = function () {

    const container = document.getElementById('directoryList');
    container.innerHTML = "";

    const searchText = document.getElementById('dirSearchInput').value.toLowerCase();
    const region     = document.getElementById('dirRegionSelect').value;
    const kategori   = document.getElementById('dirKategoriSelect').value;
    const budgetMax  = document.getElementById('dirBudgetSelect').value;
    const jamFilter  = document.getElementById('dirJamSelect').value;

    let count = 0;

    allWisataData.forEach(w => {

        /* ===== NAMA ===== */
        if (!w.nama_tempat.toLowerCase().includes(searchText)) return;

        /* ===== WILAYAH ===== */
        if (region && !w.alamat?.toLowerCase().includes(region.toLowerCase())) return;

        /* ===== KATEGORI ===== */
        if (kategori && w.kategori !== kategori) return;

        /* ===== BUDGET ===== */
        if (budgetMax !== "") {
            if (parseInt(w.harga_tiket) > parseInt(budgetMax)) return;
        }

        /* ===== JAM OPERASIONAL ===== */
        if (jamFilter && w.jam_buka) {
            const jamBuka = parseInt(w.jam_buka.split(':')[0]);

            if (
                (jamFilter === 'pagi'  && !(jamBuka >= 6  && jamBuka < 11)) ||
                (jamFilter === 'siang' && !(jamBuka >= 11 && jamBuka < 15)) ||
                (jamFilter === 'sore'  && !(jamBuka >= 15 && jamBuka < 18)) ||
                (jamFilter === 'malam' && !(jamBuka >= 18 && jamBuka <= 22))
            ) return;
        }

        /* ===== RENDER CARD ===== */
        const card = document.createElement('div');
        card.className = 'dir-card';
        card.onclick = () => {
            focusOnLocation(w.latitude, w.longitude, w.id);
            closeDirectoryModal();
        };

        card.innerHTML = `
            <div class="dir-card-img-wrapper">
                <img 
                    src="${getImageUrl(w.gambar)}"
                    alt="${w.nama_tempat}"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='https://placehold.co/600x400/e2e8f0/64748b?text=${encodeURIComponent(w.nama_tempat)}';"
                >
            </div>

            <div class="dir-card-body">
                <div class="dir-card-title">
                    ${w.nama_tempat}
                </div>

                <div class="dir-card-footer">
                    <span class="dir-cat">
                        ${w.kategori || 'Umum'}
                    </span>
                    <span class="dir-price">
                        ${w.harga_tiket == 0 
                            ? 'Gratis' 
                            : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID')}
                    </span>
                </div>
            </div>
        `;
        container.appendChild(card);
        count++;
    });

   if (count === 0) {
        container.innerHTML = `
            <div class="directory-empty">
                Tidak ada wisata sesuai filter
            </div>
        `;
    }

};



// Focus Location
window.focusOnLocation = function(lat, lng, id) {

    // 1. Bersihkan marker fokus sebelumnya
    focusLayer.clearLayers();

    // 2. Ambil data wisata
    const targetWisata = allWisataData.find(w => w.id == id);
    if (!targetWisata) return;

    // 3. Ambil icon sesuai kategori
    const iconWisata = icons[targetWisata.kategori] || icons.Default;

    // 4. Buat marker focus dengan icon wisata
    const marker = L.marker(
        [lat, lng],
        { icon: iconWisata }
    ).addTo(focusLayer);

    // 5. Fokus kamera
    map.flyTo([lat, lng], 16, { animate: true });

    const status = getWisataRouteStatus(targetWisata.id);

    let actionButtons = '';

    if (status === 'CONNECTED') {
        actionButtons = `
            <button onclick="showNearbyWisata(${targetWisata.latitude}, ${targetWisata.longitude}, '${targetWisata.nama_tempat}', ${targetWisata.id})"
                class="btn-modern btn-orange-soft">
                Wisata Sekitar
            </button>

            <button 
                onclick="rerouteTo(${targetWisata.latitude}, ${targetWisata.longitude}, ${targetWisata.harga_tiket}, ${targetWisata.id})"
                class="btn-modern btn-indigo-outline">
                Mulai Rute Dari Sini
            </button>
        `;
    }
    else if (status === 'CAN_CONNECT') {
        actionButtons = `
            <button 
                onclick="setConnectedRoute(getLastRouteId(), ${targetWisata.id}, ${targetWisata.harga_tiket})"
                class="btn-modern btn-green-solid">
                Sambung Rute
            </button>

            <button 
                onclick="rerouteTo(${targetWisata.latitude}, ${targetWisata.longitude}, ${targetWisata.harga_tiket}, ${targetWisata.id})"
                class="btn-modern btn-indigo-outline">
                Mulai Rute Dari Sini
            </button>
        `;
    }
    else {
        actionButtons = `
            <button 
                onclick="rerouteTo(${targetWisata.latitude}, ${targetWisata.longitude}, ${targetWisata.harga_tiket}, ${targetWisata.id})"
                class="btn-modern btn-indigo-outline">
                Mulai Rute Dari Sini
            </button>
        `;
    }


    // 6. Popup
    const popupContent = `
        <div class="popup-card">
            <div class="popup-image-container">
                <img 
                    src="${getImageUrl(targetWisata.gambar)}"
                    class="popup-image"
                    alt="${targetWisata.nama_tempat}"
                >
            </div>

            <div class="popup-info">
                <h3 class="popup-title">${targetWisata.nama_tempat}</h3>
                <p class="popup-price">
                    ${targetWisata.harga_tiket == 0 
                        ? 'Gratis' 
                        : 'Rp ' + Number(targetWisata.harga_tiket).toLocaleString('id-ID')}
                </p>
            </div>

            <div class="popup-actions-container">
                ${actionButtons}
            </div>
        </div>
    `;

    marker.bindPopup(popupContent).openPopup();

};

// Modal Info & Dropdown
window.openInfoModal = function() { document.getElementById('infoModal').style.display = 'flex'; }
window.closeInfoModal = function() { document.getElementById('infoModal').style.display = 'none'; }
window.toggleFilterMenu = function() { var menu = document.getElementById('menuPilihan'); menu.style.display = (menu.style.display === "none") ? "block" : "none"; }
window.toggleDropdown = function() { document.getElementById('kategoriDropdown').classList.toggle('active'); }
window.updateDisplay = function(text, iconName) {
    document.getElementById('displayText').innerText = text;
    if(document.getElementById('displayIcon')) document.getElementById('displayIcon').src = '/icons/' + iconName;
    document.getElementById('kategoriDropdown').classList.remove('active');
}

let wisataList = [];
let currentIndex = 0;

document.addEventListener("DOMContentLoaded", function () {
    if (Array.isArray(window.wisataData)) {
        wisataList = window.wisataData;
    }
});


function openDetailPanelById(id) {
    if (!Array.isArray(window.wisataData)) return;

    wisataList = window.wisataData;
    currentIndex = wisataList.findIndex(w => w.id === id);

    if (currentIndex === -1) return;

    const panel = document.getElementById('detailPanel');
    panel.classList.add('active');

    setTimeout(() => {
        updatePanel();
        highlightMarker(id);
    }, 50);
}




function updatePanel() {
    if (!wisataList || wisataList.length === 0) return;

    const wisata = wisataList[currentIndex];
    if (!wisata) return;

    const elNama     = document.getElementById('panelNama');
    const elKategori = document.getElementById('panelKategori');
    const elAlamat   = document.getElementById('panelAlamat');
    const elHarga    = document.getElementById('panelHarga');
    const btnRute    = document.getElementById('panelRuteBtn');
    const btnDetail  = document.getElementById('panelDetailBtn');
    const carouselTrack = document.getElementById('carouselTrack');

    if (!elNama || !elKategori || !elAlamat || !elHarga || !carouselTrack) {
        console.warn('Detail panel element belum lengkap di DOM');
        return;
    }

    

    // Text
    elNama.innerText     = wisata.nama_tempat;
    elKategori.innerText = wisata.kategori || 'Umum';
    elAlamat.innerText   = wisata.alamat || '-';
    elHarga.innerText    =
        wisata.harga_tiket == 0
            ? 'Gratis'
            : 'Rp ' + new Intl.NumberFormat('id-ID').format(wisata.harga_tiket);

    // Image
    let images = [];

    if (wisata.galeri) {    
        if (Array.isArray(wisata.galeri)) {
            images = wisata.galeri;
        } else if (typeof wisata.galeri === 'string') {
            try {
                images = JSON.parse(wisata.galeri);
            } catch (e) {
                console.warn('Galeri bukan JSON valid');
            }
        }
    }

    if (images.length === 0 && wisata.gambar) {
        images = [wisata.gambar];
    }

    buildCarousel(images);

    // Tombol aksi
    if (btnRute) {
        btnRute.onclick = () =>
            rerouteTo(wisata.latitude, wisata.longitude, wisata.harga_tiket, wisata.id);
    }

    if (btnDetail) {
        btnDetail.href = `/wisata/${wisata.id}`;
        btnDetail.target = '_blank';
    }
}




function nextWisata() {
    if (!wisataList || wisataList.length === 0) return;

    currentIndex = (currentIndex < wisataList.length - 1)
        ? currentIndex + 1
        : 0;

    updatePanel();
    highlightMarker(wisataList[currentIndex].id);
}


function prevWisata() {
    if (!wisataList || wisataList.length === 0) return;

    currentIndex = (currentIndex > 0)
        ? currentIndex - 1
        : wisataList.length - 1;

    updatePanel();
    highlightMarker(wisataList[currentIndex].id);
}


function closeDetailPanel() {
    document.getElementById('detailPanel').classList.remove('active');

    if (activeMarkerId && allMarkers[activeMarkerId]) {
        allMarkers[activeMarkerId].setIcon(originalIcons[activeMarkerId]);
    }

    activeMarkerId = null;
}


// courosel image
let carouselImages = [];
let carouselIndex = 0;

function buildCarousel(images) {
    const track = document.getElementById('carouselTrack');
    const dots = document.getElementById('carouselDots');

    if (!track || !dots) return;

    track.innerHTML = '';
    dots.innerHTML = '';
    carouselIndex = 0;
    carouselImages = images;

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
    const track = document.getElementById('carouselTrack');
    const dots = document.querySelectorAll('.carousel-dots span');

    track.style.transform = `translateX(-${carouselIndex * 100}%)`;

    dots.forEach((d, i) => {
        d.classList.toggle('active', i === carouselIndex);
    });
}

function nextImage() {
    carouselIndex = (carouselIndex + 1) % carouselImages.length;
    updateCarousel();
}

function prevImage() {
    carouselIndex =
        carouselIndex === 0
            ? carouselImages.length - 1
            : carouselIndex - 1;
    updateCarousel();
}

function goToImage(index) {
    carouselIndex = index;
    updateCarousel();
}

/* --- FUNGSI TOGGLE FILTER --- */
window.toggleAdvancedFilters = function() {
    var filterPanel = document.getElementById('advancedFilters');
    var btn = document.getElementById('btnFilterToggle');
    
    // Toggle class 'show' untuk animasi CSS
    if (filterPanel.classList.contains('show')) {
        filterPanel.classList.remove('show');
        btn.classList.remove('active');
        btn.innerHTML = '<span class="icon">⚙️</span> Filter'; // Ikon Petir (Quick)
    } else {
        filterPanel.classList.add('show');
        btn.classList.add('active');
        btn.innerHTML = '<span class="icon">✖</span> Tutup'; // Ikon Close
    }
}