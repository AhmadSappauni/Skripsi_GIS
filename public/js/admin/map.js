document.addEventListener("DOMContentLoaded", function() {
    
    // 1. DEFINISI VARIABEL INPUT (PENTING AGAR TIDAK ERROR)
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const alamatInp = document.getElementById('alamatInp'); // Pastikan ID ini ada di blade, atau sesuaikan

    // 2. INISIALISASI PETA
    // Gunakan window.map agar bisa diakses oleh wizard.js
    window.map = L.map('map').setView([-3.4406, 114.8394], 11);

    // Gunakan Google Maps Layer (Lebih cepat & detail)
    L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(window.map);

    let marker = null;

    // Event Klik Peta
    window.map.on('click', function(e) {
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    // Tombol Cari Lokasi
    const btnCari = document.getElementById('btnCariLokasi');
    const namaTempatInput = document.getElementById('namaTempat');

    if (btnCari) {
        btnCari.addEventListener('click', () => {
            const nama = namaTempatInput.value;
            if (!nama) return alert('Masukkan nama tempat wisata di form sebelumnya!');

            // Ubah teks tombol jadi loading
            const originalText = btnCari.innerHTML;
            btnCari.innerHTML = 'Mencari...';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(nama + ' Kalimantan Selatan')}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    btnCari.innerHTML = originalText;
                    if (!data.length) return alert('Lokasi tidak ditemukan. Coba cari manual di peta.');
                    
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    setLocation(lat, lon, true);
                })
                .catch(err => {
                    btnCari.innerHTML = originalText;
                    alert('Gagal mencari lokasi.');
                });
        });
    }

    // Fungsi Set Lokasi & Marker
    function setLocation(lat, lng, zoom = false) {
        lat = parseFloat(lat).toFixed(6);
        lng = parseFloat(lng).toFixed(6);

        // Isi ke Input Hidden
        if(latInput) latInput.value = lat;
        if(lngInput) lngInput.value = lng;

        if (zoom) window.map.setView([lat, lng], 16);

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(window.map);
            
            // Update saat didrag
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                if(latInput) latInput.value = pos.lat.toFixed(6);
                if(lngInput) lngInput.value = pos.lng.toFixed(6);
                reverseGeocode(pos.lat, pos.lng);
            });
        }

        reverseGeocode(lat, lng);
    }

    // Fungsi Ambil Alamat Otomatis
    function reverseGeocode(lat, lng) {
        // Cek apakah input alamat ada (namanya 'alamat' di HTML form)
        const alamatField = document.querySelector('textarea[name="alamat"]');
        if(!alamatField) return;

        alamatField.value = 'Sedang mengambil alamat...';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                alamatField.value = data?.display_name || '';
                // Update preview jika fungsi ada
                if(typeof window.updatePreview === 'function') window.updatePreview();
            });
    }
});