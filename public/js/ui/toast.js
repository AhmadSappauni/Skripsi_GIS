function showLoading() { document.getElementById('loadingOverlay').style.display = 'flex'; }

function showToast(title, message) {
    const toast = document.getElementById('customToast');
    const titleEl = document.getElementById('toastTitle');
    const bodyEl = document.getElementById('toastBody');
    if(toast && titleEl && bodyEl) {
        titleEl.innerText = title; bodyEl.innerText = message;
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 4000);
    } else { alert(title + "\n" + message); }
}

function validateSearch() {
    if (document.getElementById('inputLat').value === "") {
        showToast("Lokasi Belum Ada!", "Mohon klik 'Deteksi Lokasi' dulu ya 📍");
        return false; 
    }
    showLoading();
    return true;
}