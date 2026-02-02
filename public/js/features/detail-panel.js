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
    const wisata = wisataList[currentIndex];

    document.getElementById("panelNama").innerText = wisata.nama_tempat;
    document.getElementById("panelKategori").innerText =
        wisata.kategori || "Umum";
    document.getElementById("panelAlamat").innerText = wisata.alamat || "-";
    document.getElementById("panelHarga").innerText =
        wisata.harga_tiket == 0
            ? "Gratis"
            : "Rp " + new Intl.NumberFormat("id-ID").format(wisata.harga_tiket);

    // Logic Carousel Image
    let images = [];
    if (wisata.galeri) {
        if (Array.isArray(wisata.galeri)) images = wisata.galeri;
        else if (typeof wisata.galeri === "string") {
            try {
                images = JSON.parse(wisata.galeri);
            } catch (e) {}
        }
    }
    if (images.length === 0 && wisata.gambar) images = [wisata.gambar];
    buildCarousel(images);

    // Action Buttons
    const btnRute = document.getElementById("panelRuteBtn");
    if (btnRute)
        btnRute.onclick = () =>
            rerouteTo(
                wisata.latitude,
                wisata.longitude,
                wisata.harga_tiket,
                wisata.id,
            );
}

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

let carouselImages = [];
let carouselIndex = 0;
function buildCarousel(images) {
    const track = document.getElementById('carouselTrack');
    const dots = document.getElementById('carouselDots');
    if (!track || !dots) return;

    track.innerHTML = ''; dots.innerHTML = ''; carouselIndex = 0; carouselImages = images;
    
    images.forEach((img, i) => {
        const imageEl = document.createElement('img');
        imageEl.src = getImageUrl(img);
        track.appendChild(imageEl);
        const dot = document.createElement('span');
        dot.onclick = () => goToImage(i);
        if (i === 0) dot.classList.add('active');
        dots.appendChild(dot);
    });
    updateCarousel();
}
function updateCarousel() {
    document.getElementById('carouselTrack').style.transform = `translateX(-${carouselIndex * 100}%)`;
    document.querySelectorAll('.carousel-dots span').forEach((d, i) => d.classList.toggle('active', i === carouselIndex));
}