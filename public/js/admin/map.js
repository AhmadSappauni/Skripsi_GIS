// public/js/admin/map.js

window.map = L.map('map').setView([-2.5489, 118.0149], 5);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let marker = null;

map.on('click', e => setLocation(e.latlng.lat, e.latlng.lng));

const btnCari = document.getElementById('btnCariLokasi');

btnCari?.addEventListener('click', () => {
    const nama = document.getElementById('namaTempat').value;
    if (!nama) return alert('Masukkan nama tempat wisata');

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(nama)}&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (!data.length) return alert('Lokasi tidak ditemukan');
            setLocation(data[0].lat, data[0].lon, true);
        });
});

function setLocation(lat, lng, zoom = false) {
    lat = parseFloat(lat).toFixed(6);
    lng = parseFloat(lng).toFixed(6);

    latitude.value = lat;
    longitude.value = lng;

    if (zoom) map.setView([lat, lng], 15);

    marker
        ? marker.setLatLng([lat, lng])
        : marker = L.marker([lat, lng]).addTo(map);

    reverseGeocode(lat, lng);
}

function reverseGeocode(lat, lng) {
    const alamat = document.querySelector('textarea[name="alamat"]');
    alamat.value = 'Mengambil alamat...';

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => alamat.value = data?.display_name || '');
}
