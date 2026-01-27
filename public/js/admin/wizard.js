document.addEventListener("DOMContentLoaded", function() {
    
    let currentStep = 0;
    const contents = document.querySelectorAll('.wizard-content');
    const indicators = document.querySelectorAll('.step-item'); 
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    const namaInput = document.getElementById('namaTempat');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    // --- LOGIKA CUSTOM MODAL (FUTURISTIK) ---
    const modal = document.getElementById('customModal');
    const mIcon = document.getElementById('modalIcon');
    const mTitle = document.getElementById('modalTitle');
    const mDesc = document.getElementById('modalDesc');
    const mActions = document.getElementById('modalActions');

    function showModal(type, title, message, onConfirm = null) {
        // 1. Setup Icon & Warna
        mIcon.className = 'modal-icon-box'; // Reset class
        if (type === 'error') {
            mIcon.classList.add('icon-warning');
            mIcon.innerHTML = '<i class="ri-error-warning-line"></i>';
        } else if (type === 'confirm') {
            mIcon.classList.add('icon-question');
            mIcon.innerHTML = '<i class="ri-question-line"></i>';
        }

        // 2. Isi Teks
        mTitle.innerText = title;
        mDesc.innerText = message;

        // 3. Setup Tombol
        mActions.innerHTML = ''; // Kosongkan tombol lama

        if (type === 'error') {
            // Tombol Tunggal (Oke)
            const btn = document.createElement('button');
            btn.className = 'btn-modal btn-primary-modal';
            btn.innerText = 'Oke, Saya Perbaiki';
            btn.onclick = closeModal;
            mActions.appendChild(btn);
        } else if (type === 'confirm') {
            // Tombol Ganda (Batal & Ya)
            const btnCancel = document.createElement('button');
            btnCancel.className = 'btn-modal btn-secondary-modal';
            btnCancel.innerText = 'Batal';
            btnCancel.onclick = closeModal;

            const btnConfirm = document.createElement('button');
            btnConfirm.className = 'btn-modal btn-primary-modal';
            btnConfirm.innerText = 'Ya, Simpan!';
            btnConfirm.onclick = () => {
                closeModal();
                if (onConfirm) onConfirm();
            };

            mActions.appendChild(btnCancel);
            mActions.appendChild(btnConfirm);
        }

        // 4. Tampilkan dengan Animasi
        modal.classList.add('open');
    }

    function closeModal() {
        modal.classList.remove('open');
    }

    // Tutup modal jika klik di luar area kartu
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // --- WIZARD LOGIC ---
    // --- MODIFIKASI FUNGSI updateWizard (DI DALAM WIZARD.JS) ---

function updateWizard() {
    // 1. Update Tampilan Step & Indikator (Sama seperti sebelumnya)
    contents.forEach((content, index) => {
        if (index === currentStep) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });

    indicators.forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index === currentStep) {
            step.classList.add('active');
        } else if (index < currentStep) {
            step.classList.add('completed');
        }
    });

    // 2. Atur Tombol Prev/Next (Sama)
    if (currentStep === 0) {
        prevBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'inline-flex';
    }

    if (currentStep === contents.length - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
        // Panggil preview hanya di langkah terakhir
        if (typeof window.updatePreview === 'function') window.updatePreview();
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }

    // --- [PERBAIKAN UTAMA] LOGIKA KHUSUS LANGKAH 2 (PETA) ---
    // Pastikan initMap dipanggil saat masuk ke langkah index 2 (Lokasi)
    if (currentStep === 2) {
        // Cek apakah fungsi initMap sudah tersedia di global scope?
        if (typeof window.initMap === 'function') {
            // Beri jeda sedikit agar div #map muncul (display block) dulu
            setTimeout(() => {
                window.initMap(); 
            }, 300);
        } else {
            console.warn("Fungsi initMap tidak ditemukan. Pastikan script inline peta ada.");
        }
    }
}

    function isStepValid() {
        if (currentStep === 0) {
            const nama = namaInput.value.trim();
            const kategori = document.querySelector('input[name="kategori"]:checked');
            
            if (!nama) {
                // PANGGIL MODAL CUSTOM KITA
                showModal('error', 'Nama Masih Kosong', 'Mohon isi nama tempat wisata terlebih dahulu sebelum melanjutkan.');
                return false;
            }
            if (!kategori) {
                showModal('error', 'Kategori Belum Dipilih', 'Pilihlah salah satu kategori yang paling sesuai dengan wisata ini.');
                return false;
            }
        }
        
        if (currentStep === 2) {
            if(!latInput.value || !lngInput.value) {
                showModal('error', 'Lokasi Belum Ditandai', 'Klik pada peta atau gunakan tombol cari untuk menandai lokasi wisata.');
                return false;
            }
        }
        return true;
    }

    nextBtn.addEventListener('click', function() {
        if (isStepValid()) {
            if (currentStep < contents.length - 1) {
                currentStep++;
                updateWizard();
            }
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 0) {
            currentStep--;
            updateWizard();
        }
    });

    // KONFIRMASI SIMPAN
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showModal('confirm', 'Simpan Data?', 'Pastikan semua informasi sudah benar sebelum disimpan ke database.', () => {
            document.querySelector('form').submit();
        });
    });

    updateWizard();
});