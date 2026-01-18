// public/js/admin/jam-operasional.js

document.addEventListener("DOMContentLoaded", function() {
    const is24 = document.getElementById('is24Jam');
    const jamRange = document.getElementById('jamRange');
    const jamOperasional = document.getElementById('jamOperasional');
    const jamBuka = document.getElementById('jamBuka');
    const jamTutup = document.getElementById('jamTutup');

    // Fungsi update hidden input & preview
    function updateState() {
        if (is24.checked) {
            // Jika 24 Jam
            jamRange.style.opacity = '0.5';
            jamRange.style.pointerEvents = 'none'; // Matikan klik
            
            // Reset input manual
            jamBuka.value = '';
            jamTutup.value = '';
            
            // Set hidden value untuk preview
            jamOperasional.value = '24 Jam';
        } else {
            // Jika Manual
            jamRange.style.opacity = '1';
            jamRange.style.pointerEvents = 'auto';
            
            if (jamBuka.value && jamTutup.value) {
                jamOperasional.value = `${jamBuka.value} - ${jamTutup.value} WITA`;
            } else {
                jamOperasional.value = '';
            }
        }

        // Panggil update preview jika fungsinya ada
        if (typeof window.updatePreview === 'function') {
            window.updatePreview();
        }
    }

    // Event Listeners
    is24.addEventListener('change', updateState);
    jamBuka.addEventListener('change', updateState);
    jamTutup.addEventListener('change', updateState);
});