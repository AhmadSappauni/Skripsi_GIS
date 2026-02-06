function highlightMarker(id) {
    if (activeMarkerId && allMarkers[activeMarkerId]) {
        allMarkers[activeMarkerId].setIcon(originalIcons[activeMarkerId]);
    }
    if (allMarkers[id]) {
        allMarkers[id].setIcon(highlightIcon);
        map.flyTo(allMarkers[id].getLatLng(), 16, {
            animate: true,
            duration: 1,
        });
        allMarkers[id].openPopup();
        activeMarkerId = id;
    }
}

window.openDetailPanelById = function (id) {
    if (!Array.isArray(window.allWisataData)) return;

    window.wisataList = window.allWisataData;
    id = Number(id);

    window.currentIndex = wisataList.findIndex((w) => w.id === id);
    if (currentIndex === -1) return;

    document.getElementById("detailPanel").classList.add("active");

    setTimeout(() => {
        updatePanel();
        highlightMarker(id);
    }, 50);
};

function updatePanel() {
    if (!wisataList || wisataList.length === 0) return;
    
    // [1] DEKLARASI PERTAMA (INI BENAR, PERTAHANKAN)
    const wisata = wisataList[currentIndex]; 

    // 1. Update Info Dasar
    document.getElementById("panelNama").innerText = wisata.nama_tempat;
    document.getElementById("panelKategori").innerText = wisata.kategori || "Umum";
    document.getElementById("panelAlamat").innerText = wisata.alamat || "-";
    document.getElementById("panelHarga").innerText =
        wisata.harga_tiket == 0
            ? "Gratis"
            : "Rp " + new Intl.NumberFormat("id-ID").format(wisata.harga_tiket);

    // 2. LOGIKA FASILITAS
    const panelFasilitas = document.getElementById("panelFasilitas");
    if (panelFasilitas) {
        let listFasilitas = wisata.fasilitas;
        if (typeof listFasilitas === 'string') {
            try { listFasilitas = JSON.parse(listFasilitas); } catch (e) { listFasilitas = []; }
        }
        if (Array.isArray(listFasilitas) && listFasilitas.length > 0) {
            panelFasilitas.style.display = 'block';
            let html = '<h5 style="font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px;">Fasilitas:</h5>';
            html += '<div style="display:flex; flex-wrap:wrap; gap:8px;">';
            listFasilitas.forEach(item => {
                html += `
                    <span style="background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="ri-check-line"></i> ${item}
                    </span>`;
            });
            html += '</div>';
            panelFasilitas.innerHTML = html;
        } else {
            panelFasilitas.style.display = 'none';
        }
    }

    // 3. Logic Carousel Image
    let images = [];
    if (wisata.galeri) {
        if (Array.isArray(wisata.galeri)) images = wisata.galeri;
        else if (typeof wisata.galeri === "string") {
            try { images = JSON.parse(wisata.galeri); } catch (e) {}
        }
    }
    if (images.length === 0 && wisata.gambar) images = [wisata.gambar];
    buildCarousel(images);

    // 4. Action Buttons
    const btnRute = document.getElementById("panelRuteBtn");
    if (btnRute)
        btnRute.onclick = () =>
            rerouteTo(wisata.latitude, wisata.longitude, wisata.harga_tiket, wisata.id);

    // ============================================================
    // ⚠️ HAPUS BARIS DI BAWAH INI (KARENA SUDAH ADA DI ATAS) ⚠️
    // const wisata = wisataList[currentIndex]; 
    // ============================================================

    // 5. UPDATE RATING & REVIEW (Lanjutan pakai variabel 'wisata' yang dari atas)
    
    // A. BINTANG DI HEADER
    const avg = parseFloat(wisata.rata_rata || 0).toFixed(1);
    const count = wisata.jumlah_ulasan || 0;
    
    // Pastikan elemen panelRating ada sebelum di-set innerHTML-nya
    const elRating = document.getElementById("panelRating");
    if(elRating) {
        elRating.innerHTML = `⭐ ${avg} <span style="font-size:11px; color:#94a3b8;">(${count} ulasan)</span>`;
    }

    // B. ISI HIDDEN INPUT ID
    const elInputId = document.getElementById("reviewWisataId");
    if(elInputId) elInputId.value = wisata.id;

    // C. TAMPILKAN DAFTAR ULASAN
    const reviewBox = document.getElementById("reviewContainer");
    if(reviewBox) {
        reviewBox.innerHTML = '';
        if(wisata.reviews && wisata.reviews.length > 0) {
            wisata.reviews.forEach(r => {
                const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
                reviewBox.innerHTML += `
                    <div class="review-item">
                        <div style="display:flex; justify-content:space-between;">
                            <span class="review-user">${r.nama_pengulas}</span>
                            <span class="review-star">${stars}</span>
                        </div>
                        <p class="review-text">${r.komentar || ''}</p>
                    </div>
                `;
            });
        } else {
            reviewBox.innerHTML = '<p style="text-align:center; font-size:12px; color:#cbd5e1;">Belum ada ulasan.</p>';
        }
    }
}

window.submitReview = function(e) {
    e.preventDefault(); // Cegah reload halaman

    const form = document.getElementById('formReview');
    const formData = new FormData(form);

    // Kirim ke Backend (Laravel)
    fetch('/review/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert("Terima kasih! Ulasan terkirim.");
            form.reset(); // Kosongkan form
            // Opsional: Refresh halaman atau update data lokal biar langsung muncul
            location.reload(); 
        } else {
            alert("Gagal mengirim ulasan.");
        }
    })
    .catch(err => console.error(err));
};

window.nextWisata = function () {
    currentIndex = currentIndex < wisataList.length - 1 ? currentIndex + 1 : 0;
    updatePanel();
    highlightMarker(wisataList[currentIndex].id);
};
window.prevWisata = function () {
    currentIndex = currentIndex > 0 ? currentIndex - 1 : wisataList.length - 1;
    updatePanel();
    highlightMarker(wisataList[currentIndex].id);
};
window.closeDetailPanel = function () {
    document.getElementById("detailPanel").classList.remove("active");
    if (activeMarkerId && allMarkers[activeMarkerId])
        allMarkers[activeMarkerId].setIcon(originalIcons[activeMarkerId]);
    activeMarkerId = null;
};

/* =========================================
   LOGIKA CAROUSEL (DIPERBAIKI)
   ========================================= */

let carouselImages = [];
let carouselIndex = 0;

function buildCarousel(images) {
    const track = document.getElementById('carouselTrack');
    const dots = document.getElementById('carouselDots');
    
    // Ambil tombol navigasi (sesuai class di HTML kamu)
    const btnLeft = document.querySelector('.carousel-btn.left');
    const btnRight = document.querySelector('.carousel-btn.right');

    if (!track || !dots) return;

    // Reset
    track.innerHTML = ''; 
    dots.innerHTML = ''; 
    carouselIndex = 0; 
    carouselImages = images;
    
    // LOGIKA 1: Sembunyikan tombol jika gambar cuma 1 (atau 0)
    if (images.length <= 1) {
        if(btnLeft) btnLeft.style.display = 'none';
        if(btnRight) btnRight.style.display = 'none';
        if(dots) dots.style.display = 'none';
    } else {
        if(btnLeft) btnLeft.style.display = 'block';
        if(btnRight) btnRight.style.display = 'block';
        if(dots) dots.style.display = 'flex';
    }

    // Generate Gambar & Dots
    images.forEach((img, i) => {
        const imageEl = document.createElement('img');
        imageEl.src = getImageUrl(img); // Pastikan fungsi getImageUrl ada di script.js / helper
        track.appendChild(imageEl);

        const dot = document.createElement('span');
        // Gunakan window.goToImage agar bisa dipanggil
        dot.onclick = () => window.goToImage(i); 
        if (i === 0) dot.classList.add('active');
        dots.appendChild(dot);
    });

    updateCarousel();
}

function updateCarousel() {
    const track = document.getElementById('carouselTrack');
    if(track) {
        track.style.transform = `translateX(-${carouselIndex * 100}%)`;
    }
    document.querySelectorAll('.carousel-dots span').forEach((d, i) => {
        d.classList.toggle('active', i === carouselIndex);
    });
}

// LOGIKA 2: Fungsi Navigasi (Wajib pakai window. karena dipanggil dari onclick HTML)
window.nextImage = function() {
    if (carouselImages.length <= 1) return;
    carouselIndex = (carouselIndex + 1) % carouselImages.length;
    updateCarousel();
};

window.prevImage = function() {
    if (carouselImages.length <= 1) return;
    carouselIndex = (carouselIndex === 0) ? carouselImages.length - 1 : carouselIndex - 1;
    updateCarousel();
};

window.goToImage = function(index) {
    carouselIndex = index;
    updateCarousel();
};

/* LOGIKA GANTI TAB */
window.switchTab = function(tabName) {
    // 1. Sembunyikan semua konten
    document.getElementById('tabInfo').style.display = 'none';
    document.getElementById('tabUlasan').style.display = 'none';
    
    // 2. Reset tombol aktif
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    // 3. Munculkan yang dipilih
    if (tabName === 'info') {
        document.getElementById('tabInfo').style.display = 'block';
        buttons[0].classList.add('active'); // Tombol pertama
    } else {
        document.getElementById('tabUlasan').style.display = 'block';
        buttons[1].classList.add('active'); // Tombol kedua
    }
};