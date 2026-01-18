window.updatePreview = function () {
    
    // 1. AMBIL DATA
    const nama = document.getElementById('namaTempat')?.value.trim();
    const hargaVal = document.getElementById('hargaTiket')?.value;
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
    previewBox.style.animation = 'fadeInUp 0.5s ease-out';

    // Format Harga
    let hargaText = 'Gratis';
    if (hargaVal && parseInt(hargaVal) > 0) {
        hargaText = 'Rp ' + parseInt(hargaVal).toLocaleString('id-ID');
    }

    // Helper Function
    function safeSetText(id, value) {
        const el = document.getElementById(id);
        if (el) el.innerHTML = value; // Pakai innerHTML biar bisa kasih warna/bold jika perlu
    }

    // Isi Data
    safeSetText('pvNama', nama);
    safeSetText('pvKategori', kategori);
    safeSetText('pvHarga', hargaText);
    safeSetText('pvJam', jamText); // Data Baru
    safeSetText('pvAlamat', alamat);
};