document.addEventListener("DOMContentLoaded", function () {
    if (window.location.search.includes('rute_fix')) {
        if (typeof showToast === 'function') {
            showToast("Rute Ditemukan! 🚀", "Menampilkan rekomendasi perjalanan terbaik.");
        }
    }
});
