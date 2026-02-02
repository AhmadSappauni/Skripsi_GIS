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
    
    map.flyTo([lat, lng], 16, { animate: true, duration: 1.5 });

    const hargaText = target.harga_tiket == 0 ? "Gratis" : "Rp " + new Intl.NumberFormat('id-ID').format(target.harga_tiket);
    const finalImage = getImageUrl(target.gambar);

    // --- PERBAIKAN DI SINI ---
    const popupContent = `
        <div class="popup-card">
            <div class="popup-image-container">
                <button type="button" 
                    onclick="event.stopPropagation(); openDetailPanelById('${target.id}')" 
                    class="btn-detail-corner" 
                    title="Lihat Detail">
                    ↗
                </button>
                
                <img src="${finalImage}" class="popup-image" alt="${target.nama_tempat}" onerror="this.src='https://placehold.co/400?text=IMG'">
            </div>
            
            <div class="popup-info">
                <h3 class="popup-title">${target.nama_tempat}</h3>
                <p class="popup-price" style="margin-top: 5px; color: #059669; font-weight: 700;">${hargaText}</p>
            </div>

            <div class="popup-actions-container">
                <button onclick="rerouteTo(${target.latitude}, ${target.longitude}, ${target.harga_tiket}, ${target.id})" 
                    class="btn-modern btn-indigo-outline" style="width: 100%;">
                    Mulai Rute Dari Sini
                </button>
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