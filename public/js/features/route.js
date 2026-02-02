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
