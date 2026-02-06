window.updatePreview = function () {
    
    // 1. AMBIL DATA
    const nama = document.getElementById('namaTempat')?.value.trim();
    
    // --- PERUBAHAN DI SINI ---
    // Mengambil value dari ID 'hargaReal' (input hidden), bukan 'hargaTiket' lagi
    // Jika hargaReal tidak ketemu (misal belum ketik), default ke '0'
    const hargaVal = document.getElementById('hargaReal')?.value || '0'; 
    // -------------------------

    const alamat = document.getElementById('alamatInp')?.value.trim();
    const kategoriEl = document.querySelector('input[name="kategori"]:checked');
    const kategori = kategoriEl ? kategoriEl.value : '';

    // Ambil Data Jam
    const is24Jam = document.getElementById('is24Jam')?.checked;
    const jamBuka = document.getElementById('jamBuka')?.value;
    const jamTutup = document.getElementById('jamTutup')?.value;
    
    // Logic Format Jam
    let jamText = '-';
    if (is24Jam) {
        jamText = 'Buka 24 Jam';
    } else if (jamBuka && jamTutup) {
        jamText = `${jamBuka} - ${jamTutup} WITA`;
    }

    // 2. CEK KELENGKAPAN (Nama, Kategori, Alamat wajib ada)
    const isDataComplete = (nama && kategori && alamat && alamat !== '-');
    const previewBox = document.getElementById('previewBox');
    
    if (!previewBox) return;

    if (!isDataComplete) {
        previewBox.style.display = 'none';
        return; 
    }

    // 3. TAMPILKAN
    previewBox.style.display = 'block';
    
    // Reset animasi biar nge-blink kalau ada update
    previewBox.style.animation = 'none';
    previewBox.offsetHeight; /* trigger reflow */
    previewBox.style.animation = 'fadeInUp 0.5s ease-out';

    // Format Harga
    let hargaText = 'Gratis';
    // Kita parse int dari string murni (misal "10000"), lalu format jadi "10.000"
    if (hargaVal && parseInt(hargaVal) > 0) {
        hargaText = 'Rp ' + parseInt(hargaVal).toLocaleString('id-ID');
    }

    // Helper Function
    function safeSetText(id, value) {
        const el = document.getElementById(id);
        if (el) el.innerHTML = value; 
    }

    // Isi Data ke Preview Box
    safeSetText('pvNama', nama);
    safeSetText('pvKategori', kategori);
    safeSetText('pvHarga', hargaText);
    safeSetText('pvJam', jamText);
    safeSetText('pvAlamat', alamat);
};