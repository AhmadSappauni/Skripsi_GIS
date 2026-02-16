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
    const container = document.getElementById('directoryList'); 
    container.innerHTML = "";

    // 1. Ambil Nilai Input
    const searchText = document.getElementById('dirSearchInput').value.toLowerCase();
    const region = document.getElementById('dirRegionSelect').value;
    const kategori = document.getElementById('dirKategoriSelect').value;
    const budgetMax = document.getElementById('dirBudgetSelect').value;
    const minRating = document.getElementById('dirRatingSelect').value; 

    let count = 0;
    
    allWisataData.forEach(w => {
        // --- LOGIKA FILTER ---
        if (!w.nama_tempat.toLowerCase().includes(searchText)) return;
        if (region && !w.alamat?.toLowerCase().includes(region.toLowerCase())) return;
        if (kategori && w.kategori !== kategori) return;
        if (budgetMax !== "" && parseInt(w.harga_tiket) > parseInt(budgetMax)) return;

        const ratingWisata = parseFloat(w.rata_rata || 0);
        if (minRating !== "" && ratingWisata < parseFloat(minRating)) return;

        // ============================================================
        // 📍 LOGIKA BARU: DETEKSI KECAMATAN / AREA SPESIFIK
        // ============================================================
        let displayLokasi = w.alamat ? w.alamat.split(',')[0] : 'Kalsel'; // Default awal
        
        if (w.alamat) {
            const addr = w.alamat;
            
            // 1. Cari "Kecamatan X" atau "Kec. X"
            const matchKec = addr.match(/(?:Kecamatan|Kec\.?)\s+([a-zA-Z\s]+?)(?:,|$)/i);
            
            // 2. Cari Kota + Arah (Misal: Banjarmasin Tengah)
            const matchKotaArah = addr.match(/(?:Banjarmasin|Banjarbaru)\s+(?:Tengah|Barat|Timur|Selatan|Utara|Kota)/i);

            // 3. Cari Nama Daerah Populer (Tanah Laut/Banjar)
            const matchSpesifik = addr.match(/(?:Landasan Ulin|Liang Anggang|Cempaka|Martapura|Astambul|Mataraman|Takisung|Bati-Bati|Pelaihari|Alalak|Mandastana)/i);

            if (matchKec) {
                displayLokasi = "Kec. " + matchKec[1].trim(); 
            } 
            else if (matchKotaArah) {
                displayLokasi = matchKotaArah[0]; 
            }
            else if (matchSpesifik) {
                displayLokasi = "Kec. " + matchSpesifik[0];
            }
            else {
                // Fallback: Ambil bagian kedua setelah koma (biasanya nama daerah)
                const parts = addr.split(',');
                if (parts.length > 1) {
                    displayLokasi = parts[1].trim(); 
                }
            }
        }
        // ============================================================

        // --- RENDER KARTU ---
        const card = document.createElement('div'); 
        card.className = 'dir-card';

        const ratingDisplay = ratingWisata > 0 ? `⭐ ${ratingWisata.toFixed(1)}` : `Belum ada ulasan`;
        const hargaDisplay = w.harga_tiket == 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');

        card.innerHTML = `
            <div class="dir-card-content" onclick="selectDetailFromDirectory(${w.id})">
                <div class="dir-card-img-wrapper">
                    <img src="${getImageUrl(w.gambar)}" loading="lazy" onerror="this.src='https://placehold.co/400?text=IMG'">
                    <span class="dir-badge-top">${w.kategori || 'Umum'}</span>
                </div>
                <div class="dir-card-body">
                    <div class="dir-card-title">${w.nama_tempat}</div>
                    <div style="font-size:12px; color:#f59e0b; margin-bottom:5px; font-weight:600;">
                        ${ratingDisplay}
                    </div>
                    <div class="dir-card-footer">
                        <span class="dir-cat" style="color:#64748b; font-size:12px;">
                            <i class="ri-map-pin-line"></i> ${displayLokasi}
                        </span>
                        <span class="dir-price">${hargaDisplay}</span>
                    </div>
                </div>
            </div>

            <button class="dir-btn-locate" onclick="selectLocateFromDirectory(${w.id})" title="Lihat di Peta">
                <i class="ri-map-pin-2-fill"></i>
            </button>
        `;
            
        container.appendChild(card); 
        count++;
    });

    if (count === 0) container.innerHTML = `<div class="directory-empty">Tidak ada wisata sesuai filter</div>`;
};

// --- FUNGSI PENDUKUNG (Bridge) ---

// 1. Jika User Klik Badan Kartu -> Buka Detail, Tutup Modal, JANGAN Zoom Peta
window.selectDetailFromDirectory = function(id) {
    closeDirectoryModal(); // Tutup modal dulu
    
    // Beri jeda sedikit agar transisi modal selesai baru panel muncul
    setTimeout(() => {
        openDetailOnly(id); // Panggil fungsi split action yang sudah kita buat sebelumnya
    }, 100); 
};

// 2. Jika User Klik Tombol Pin -> Zoom Peta + Buka Popup (Seperti dulu)
window.selectLocateFromDirectory = function(id) {
    closeDirectoryModal(); // Tutup modal dulu
    
    // Cari data koordinat berdasarkan ID
    const target = window.allWisataData.find(w => w.id == id);

    if (target) {
        setTimeout(() => {
            // Fungsi ini yang bikin Marker Merah + Popup Card + Zoom
            focusOnLocation(target.latitude, target.longitude, target.id); 
        }, 100);
    }
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

    // --- [BARU] LOGIKA TOMBOL VISITED ---
    // Pastikan window.visitedIds sudah ada (dari blade)
    const visitedList = window.visitedIds || [];
    const isVisited = visitedList.includes(target.id);
    
    const btnClass = isVisited ? 'btn-visited active' : 'btn-visited';
    const btnIcon  = isVisited ? 'ri-checkbox-circle-fill' : 'ri-checkbox-circle-line';
    const btnText  = isVisited ? 'Sudah Dikunjungi' : 'Tandai Dikunjungi';
    // ------------------------------------

    // --- RENDER POPUP ---
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
                
                <button class="${btnClass}" onclick="toggleVisitState(${target.id}, this)">
                    <i class="${btnIcon}"></i> <span>${btnText}</span>
                </button>
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

// --- FUNGSI PENDUKUNG SPLIT ACTION (Taruh di file JS yang sama) ---

// 1. FUNGSI HANYA BUKA DETAIL (Tanpa Zoom)
window.openDetailOnly = function(id) {
    // Pastikan data tersedia
    if (!window.allWisataData) return;
    
    // Cari index wisata berdasarkan ID
    window.wisataList = window.allWisataData;
    id = Number(id);
    window.currentIndex = wisataList.findIndex((w) => w.id === id);
    
    if (currentIndex === -1) return;

    // Buka Panel Detail
    const panel = document.getElementById("detailPanel");
    if(panel) {
        panel.classList.add("active");
        
        // Update Isi Panel (Tanpa FlyTo Peta)
        if(typeof updatePanel === 'function') {
            updatePanel(); 
        } else {
            console.warn("Fungsi updatePanel() tidak ditemukan.");
        }
    }
};

// 2. FUNGSI KHUSUS ZOOM KE LOKASI (Menggunakan Leaflet)
// Fungsi ini dipanggil oleh tombol Pin di Sidebar & Directory
window.locateOnMap = function(id) {
    // Cari data wisata berdasarkan ID
    const target = window.allWisataData.find(w => w.id == id);
    
    if (target) {
        // Panggil fungsi 'focusOnLocation' agar konsisten (Zoom + Popup Card)
        focusOnLocation(target.latitude, target.longitude, target.id);
    }
};

// --- FUNGSI TANDAI KUNJUNGAN (JEJAK PETUALANG) ---
window.toggleVisitState = function(id, btnElement) {
    if (!window.isLoggedIn) {
        alert("Login dulu ya untuk menyimpan jejak petualanganmu! 🌍");
        window.location.href = '/login'; 
        return;
    }

    const originalContent = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> menyimpan...';
    btnElement.style.opacity = '0.7';
    btnElement.disabled = true;

    fetch(`/visit/toggle/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        btnElement.disabled = false;
        btnElement.style.opacity = '1';
        
        if (data.status === 'success') {
            if (data.action === 'added') {
                btnElement.classList.add('active');
                btnElement.innerHTML = '<i class="ri-checkbox-circle-fill"></i> <span>Sudah Dikunjungi</span>';
                if(window.visitedIds) window.visitedIds.push(id);
            } else {
                btnElement.classList.remove('active');
                btnElement.innerHTML = '<i class="ri-checkbox-circle-line"></i> <span>Tandai Dikunjungi</span>';
                if(window.visitedIds) window.visitedIds = window.visitedIds.filter(vId => vId !== id);
            }
        }
    })
    .catch(err => {
        console.error(err);
        btnElement.innerHTML = originalContent;
        alert("Gagal koneksi server.");
    });
};

// --- LOGIKA MENU KOLEKSIKU ---

// 1. Buka Modal & Render Data
// Variable Global untuk simpan catatan sementara
window.userNotes = {}; 

// 1. Buka Modal & Ambil Data Catatan Terbaru
window.openVisitedModal = function() {
    document.getElementById('visitedModal').style.display = 'flex';
    
    // Tampilkan Loading dulu
    document.getElementById('visitedListContainer').innerHTML = '<div style="text-align:center; padding:20px; color:#64748b;">Mengambil jurnal...</div>';

    // Fetch Data Catatan dari Server
    fetch('/visit/get-data')
        .then(res => res.json())
        .then(data => {
            window.userNotes = data; // Simpan ke variable global
            renderVisitedList();     // Baru render tampilan
        })
        .catch(err => {
            console.error(err);
            renderVisitedList(); // Tetap render meski error (catatan kosong)
        });
};

// 2. Tutup Modal
window.closeVisitedModal = function() {
    document.getElementById('visitedModal').style.display = 'none';
};

// 2. Render List dengan Textarea
// Render Versi Timeline Premium
window.renderVisitedList = function() {
    const container = document.getElementById('visitedListContainer');
    container.innerHTML = ""; 

    const visitedIds = window.visitedIds || [];
    if (!window.allWisataData) return;
    
    const myPlaces = window.allWisataData.filter(w => visitedIds.includes(w.id));

    if (myPlaces.length === 0) {
        container.innerHTML = `
            <div style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                <div style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;">🧭</div>
                <p>Belum ada jejak tersimpan.</p>
            </div>`;
        return;
    }

    // Buat Wrapper Timeline
    const wrapper = document.createElement('div');
    wrapper.className = 'timeline-wrapper';

    myPlaces.forEach((w, index) => {
        const item = document.createElement('div');
        item.className = 'timeline-item';
        
        // Ambil catatan
        const note = window.userNotes[w.id] || '';
        const hasNote = note.trim().length > 0;
        const imgSrc = w.gambar.startsWith('http') ? w.gambar : '/'+w.gambar;

        // Tampilan Preview (Jika catatan panjang, potong)
        const previewText = hasNote 
            ? (note.length > 60 ? note.substring(0, 60) + '...' : note) 
            : 'Belum ada catatan. Ketuk untuk menulis...';
        
        const previewClass = hasNote ? '' : 'empty-note';

        item.innerHTML = `
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <div class="t-header" onclick="closeVisitedModal(); setTimeout(() => focusOnLocation(${w.latitude}, ${w.longitude}, ${w.id}), 200);">
                    <img src="${imgSrc}" class="t-img" onerror="this.src='https://placehold.co/50'">
                    <div class="t-info">
                        <div class="t-title">${w.nama_tempat}</div>
                        <div class="t-loc"><i class="ri-map-pin-2-line"></i> ${w.alamat ? w.alamat.split(',')[0] : 'Lokasi'}</div>
                    </div>
                    <div style="font-size: 18px; color: #cbd5e1;">›</div>
                </div>

                <div class="note-preview ${previewClass}" onclick="toggleNoteEditor(${w.id})">
                    <i class="ri-pencil-line" style="margin-right:4px;"></i> 
                    <span id="preview-text-${w.id}">${previewText}</span>
                </div>

                <div id="editor-${w.id}" class="note-editor-area">
                    <textarea id="input-${w.id}" class="premium-textarea" placeholder="Ceritakan pengalamanmu di sini...">${note}</textarea>
                    <div class="editor-actions">
                        <span id="status-${w.id}" style="font-size:10px; color:#64748b;">Tekan simpan selesai</span>
                        <button class="btn-save-note" onclick="saveNoteAndClose(${w.id})">Simpan</button>
                    </div>
                </div>
            </div>
        `;

        wrapper.appendChild(item);
    });

    container.appendChild(wrapper);
};

// 1. Buka/Tutup Editor
window.toggleNoteEditor = function(id) {
    // Tutup semua editor lain dulu biar rapi (Accordion effect)
    document.querySelectorAll('.note-editor-area').forEach(el => el.classList.remove('active'));
    
    // Buka yang diklik
    const editor = document.getElementById(`editor-${id}`);
    if (editor) editor.classList.add('active');
};

// 2. Simpan & Update Tampilan
window.saveNoteAndClose = function(id) {
    const input = document.getElementById(`input-${id}`);
    const val = input.value;
    const statusEl = document.getElementById(`status-${id}`);
    const previewEl = document.getElementById(`preview-text-${id}`);

    statusEl.innerText = 'Menyimpan...';

    // Panggil fungsi save API yang sudah ada sebelumnya
    window.saveNoteToServer(id, val); // Asumsi fungsi ini mengembalikan Promise atau kita modif sedikit

    // Karena saveNoteToServer di kode sebelumnya pakai fetch background, 
    // kita update UI manual di sini biar responsif:
    
    setTimeout(() => {
        // Update Preview Text
        if (val.trim().length > 0) {
            previewEl.innerText = val.length > 60 ? val.substring(0, 60) + '...' : val;
            previewEl.parentElement.classList.remove('empty-note');
        } else {
            previewEl.innerText = 'Belum ada catatan. Ketuk untuk menulis...';
            previewEl.parentElement.classList.add('empty-note');
        }

        // Tutup Editor
        document.getElementById(`editor-${id}`).classList.remove('active');
        statusEl.innerText = 'Tekan simpan selesai'; // Reset text
    }, 800); // Delay dikit biar user lihat proses
};

// --- FUNGSI PENGHUBUNG SERVER (YANG HILANG) ---
window.saveNoteToServer = function(id, content) {
    // 1. Ambil Token Keamanan
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.getAttribute('content') : (window.csrfToken || '');

    // 2. Kirim ke Laravel
    fetch(`/visit/save-note/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ note: content })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Berhasil disimpan:', data);
        // Update memori lokal browser biar gak perlu refresh
        if(!window.userNotes) window.userNotes = {};
        window.userNotes[id] = content;
    })
    .catch(err => {
        console.error('Gagal menyimpan:', err);
        alert("Gagal menyimpan catatan. Cek koneksi internetmu.");
    });
};