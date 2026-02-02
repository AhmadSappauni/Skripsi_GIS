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
        showToast("Info", `Tidak ada wisata alternatif dalam radius ${RADIUS_KM} KM`);
    } else {
        showToast("Berhasil", `Ditemukan ${count} wisata dalam radius ${RADIUS_KM} KM`);
    }

    map.fitBounds(radiusCircle.getBounds());
};
