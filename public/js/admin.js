/* =========================================
   SCRIPT KHUSUS HALAMAN ADMIN
   ========================================= */

document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. FITUR PREVIEW GAMBAR (SAAT UPLOAD) ---
    const fileInput = document.querySelector('input[name="gambar_file"]');
    const previewContainer = document.querySelector('.upload-box');

    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Cari apakah sudah ada gambar preview sebelumnya
                    let imgPreview = previewContainer.querySelector('.img-preview-live');
                    
                    // Jika belum ada, buat elemen img baru
                    if (!imgPreview) {
                        imgPreview = document.createElement('img');
                        imgPreview.className = 'img-preview-live';
                        
                        // Style agar tampilannya rapi
                        imgPreview.style.maxHeight = '200px';
                        imgPreview.style.width = 'auto';
                        imgPreview.style.marginTop = '15px';
                        imgPreview.style.borderRadius = '12px';
                        imgPreview.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                        imgPreview.style.border = '2px solid #e2e8f0';
                        
                        // Masukkan ke dalam kotak upload (di bawah input)
                        previewContainer.appendChild(imgPreview);
                    }
                    
                    // Isi source gambar dengan hasil bacaan file
                    imgPreview.src = e.target.result;
                }
                
                reader.readAsDataURL(file);
            }
        });
    }

    // --- 2. KONFIRMASI HAPUS (DOUBLE CHECK) ---
    // Kode ini akan menangkap semua tombol delete dan memastikan user yakin
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Jika di HTML sudah ada onclick="return confirm...", kode ini jadi pelapis tambahan
            // Tapi sebaiknya onclick di HTML dihapus jika pakai ini.
            // Untuk sekarang, kita biarkan onclick di HTML yang menangani logic-nya.
        });
    });

    // --- 3. AUTO HIDE ALERT (Pesan Sukses Hilang Sendiri) ---
    const alertBox = document.querySelector('.alert-success');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500); // Hapus dari DOM setelah fade out
        }, 3000); // Hilang setelah 3 detik
    }

});