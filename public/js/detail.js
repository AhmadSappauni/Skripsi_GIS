/**
 * Fungsi untuk kembali ke halaman pencarian sebelumnya
 * Menggunakan localStorage untuk mengingat filter terakhir user
 */
function goBackToSearch() {
    // 1. Ambil URL terakhir dari memori browser
    var lastUrl = localStorage.getItem('lastSearchUrl');

    // 2. Efek visual saat diklik (opsional)
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.3s ease';

    setTimeout(function() {
        // 3. Cek apakah ada URL tersimpan?
        if (lastUrl) {
            window.location.href = lastUrl; 
        } else {
            // Jika user langsung buka link detail (tanpa lewat search),
            // kembalikan ke home/dashboard utama.
            // Ganti '/' dengan route utama app kamu jika berbeda.
            window.location.href = "/"; 
        }
    }, 300);
}