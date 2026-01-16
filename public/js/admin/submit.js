// public/js/admin/submit.js

document.querySelector('form').addEventListener('submit', () => {
    if (!jamOperasional.value) {
        if (is24.checked) {
            jamOperasional.value = '24 Jam';
        } else if (jamBuka.value && jamTutup.value) {
            jamOperasional.value = `${jamBuka.value} - ${jamTutup.value}`;
        }
    }
});
