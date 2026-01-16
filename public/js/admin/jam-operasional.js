// public/js/admin/jam-operasional.js

const is24 = document.getElementById('is24Jam');
const jamRange = document.getElementById('jamRange');
const jamOperasional = document.getElementById('jamOperasional');
const jamBuka = document.getElementById('jamBuka');
const jamTutup = document.getElementById('jamTutup');

is24.addEventListener('change', () => {
    if (is24.checked) {
        jamRange.style.display = 'none';
        jamOperasional.value = '24 Jam';
        jamBuka.value = jamTutup.value = '';
    } else {
        jamRange.style.display = 'grid';
        jamOperasional.value = '';
    }
    updatePreview();
});

[jamBuka, jamTutup].forEach(el => {
    el.addEventListener('change', () => {
        if (jamBuka.value && jamTutup.value) {
            jamOperasional.value = `${jamBuka.value} - ${jamTutup.value}`;
        }
        updatePreview();
    });
});
