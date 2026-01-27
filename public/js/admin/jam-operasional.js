document.addEventListener("DOMContentLoaded", function() {
    // 1. Ambil Elemen
    const is24 = document.getElementById('is24Jam');
    const jamRange = document.getElementById('jamRange');
    const jamOperasional = document.getElementById('jamOperasional');
    const jamBuka = document.getElementById('jamBuka');
    const jamTutup = document.getElementById('jamTutup');

    // [PENTING] Cek Null Safety: Jika elemen tidak ada di halaman ini, BERHENTI.
    if (!is24 || !jamRange) return; 

    // 2. Fungsi update state
    function updateState() {
        if (is24.checked) {
            // Mode 24 Jam
            jamRange.style.opacity = '0.5';
            jamRange.style.pointerEvents = 'none'; 
            
            if(jamBuka) jamBuka.value = '';
            if(jamTutup) jamTutup.value = '';
            
            if(jamOperasional) jamOperasional.value = '24 Jam';
        } else {
            // Mode Manual
            jamRange.style.opacity = '1';
            jamRange.style.pointerEvents = 'auto';
            
            if (jamBuka && jamTutup && jamBuka.value && jamTutup.value) {
                if(jamOperasional) jamOperasional.value = `${jamBuka.value} - ${jamTutup.value} WITA`;
            } else {
                if(jamOperasional) jamOperasional.value = '';
            }
        }

        // Panggil preview jika ada
        if (typeof window.updatePreview === 'function') {
            window.updatePreview();
        }
    }

    // 3. Pasang Event Listeners
    is24.addEventListener('change', updateState);
    if(jamBuka) jamBuka.addEventListener('input', updateState);
    if(jamTutup) jamTutup.addEventListener('input', updateState);
});