/* --- LOGIKA CUSTOM RATING DROPDOWN --- */
function toggleRatingMenu() {
    const menu = document.getElementById('ratingMenuList');
    const trigger = document.getElementById('ratingTrigger');
    
    if (menu.style.display === 'block') {
        menu.style.display = 'none';
        trigger.classList.remove('active');
    } else {
        menu.style.display = 'block';
        trigger.classList.add('active');
    }
}

function selectRating(value, text) {
    // 1. Update nilai input hidden (agar bisa disubmit)
    document.getElementById('hiddenRatingInput').value = value;
    
    // 2. Update teks tampilan
    document.getElementById('selectedRatingDisplay').innerText = text;
    
    // 3. Tutup menu
    toggleRatingMenu();
    
    // Opsional: Jika kamu ingin filternya langsung aktif tanpa klik tombol cari,
    // hilangkan komentar di bawah ini:
    // document.querySelector('.filter-form').submit(); 
}

// Tutup dropdown jika klik di luar area
document.addEventListener('click', function(e) {
    const trigger = document.getElementById('ratingTrigger');
    const menu = document.getElementById('ratingMenuList');
    if (trigger && menu && !trigger.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
        trigger.classList.remove('active');
    }
});

// --- FUNGSI TANDAI KUNJUNGAN (JEJAK PETUALANG) ---
window.toggleVisitState = function(id, btnElement) {
    // 1. Cek Login
    if (!window.isLoggedIn) {
        alert("Eits, login dulu biar bisa simpan jejak petualanganmu! 🌍");
        window.location.href = '/login'; // Arahkan ke login
        return;
    }

    // 2. Efek Loading (Biar user tahu sedang proses)
    const originalContent = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> menyimpan...';
    btnElement.style.opacity = '0.7';
    btnElement.disabled = true;

    // 3. Kirim Data ke Laravel
    fetch(`/visit/toggle/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Balikin tombol jadi aktif
        btnElement.disabled = false;
        btnElement.style.opacity = '1';
        
        if (data.status === 'success') {
            if (data.action === 'added') {
                // UBAH JADI HIJAU (Sudah Dikunjungi)
                btnElement.classList.add('active');
                btnElement.innerHTML = '<i class="ri-checkbox-circle-fill"></i> <span>Sudah Dikunjungi</span>';
                window.visitedIds.push(id); // Update memori lokal
            } else {
                // UBAH JADI PUTIH (Batal)
                btnElement.classList.remove('active');
                btnElement.innerHTML = '<i class="ri-checkbox-circle-line"></i> <span>Tandai Dikunjungi</span>';
                // Hapus dari memori lokal
                window.visitedIds = window.visitedIds.filter(vId => vId !== id);
            }
        }
    })
    .catch(err => {
        console.error(err);
        btnElement.innerHTML = originalContent;
        alert("Gagal koneksi. Coba lagi ya.");
    });
};