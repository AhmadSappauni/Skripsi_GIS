// public/js/admin/preview.js

window.updatePreview = function () {

    document.getElementById('pvNama').innerText =
        document.getElementById('namaTempat')?.value || '-';

    const kategori = document.querySelector('input[name="kategori"]:checked');
    document.getElementById('pvKategori').innerText =
        kategori ? kategori.value : '-';

    const harga = document.querySelector('input[name="harga_tiket"]')?.value;
    document.getElementById('pvHarga').innerText =
        harga && harga > 0 ? 'Rp ' + harga : 'Gratis';

    document.getElementById('pvJam').innerText =
        document.getElementById('jamOperasional')?.value || '-';

    const lat = document.getElementById('latitude')?.value;
    const lng = document.getElementById('longitude')?.value;
    document.getElementById('pvKoordinat').innerText =
        lat && lng ? `${lat}, ${lng}` : '-';

    document.getElementById('pvAlamat').innerText =
        document.querySelector('textarea[name="alamat"]')?.value || '-';

    const box = document.getElementById('previewBox');
    if (box) box.style.display = 'block';
};
