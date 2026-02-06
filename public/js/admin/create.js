var map = null;
var marker = null;

// Fungsi Inisialisasi Peta
window.initMap = function () {
    if (map !== null && map !== undefined) {
        map.off();
        map.remove();
    }

    var defaultLat = -3.440974;
    var defaultLng = 114.8335;

    var latVal = document.getElementById("latitude").value;
    var lngVal = document.getElementById("longitude").value;
    if (latVal && lngVal) {
        defaultLat = latVal;
        defaultLng = lngVal;
    }

    map = L.map("map").setView([defaultLat, defaultLng], 13);

    L.tileLayer("https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}", {
        maxZoom: 20,
        subdomains: ["mt0", "mt1", "mt2", "mt3"],
    }).addTo(map);

    marker = L.marker([defaultLat, defaultLng], {
        draggable: true,
    }).addTo(map);

    marker.on('dragend', function (e) {
        var pos = marker.getLatLng();
        updateInputs(pos.lat, pos.lng); // Update Angka Koordinat
        getAddressFromLatLon(pos.lat, pos.lng); // <--- TAMBAHAN: Update Alamat Otomatis
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng); // Update Angka Koordinat
        map.panTo(e.latlng);
        getAddressFromLatLon(e.latlng.lat, e.latlng.lng); // <--- TAMBAHAN: Update Alamat Otomatis
    });

    updateInputs(defaultLat, defaultLng);
};

window.updateMapFromInput = function () {
    var latInput = document.getElementById("latitude").value;
    var lngInput = document.getElementById("longitude").value;

    // Cek apakah input valid (angka)
    if (latInput && lngInput && !isNaN(latInput) && !isNaN(lngInput)) {
        var newLat = parseFloat(latInput);
        var newLng = parseFloat(lngInput);

        // Pindahkan Marker & Peta
        if (marker && map) {
            var newLatLng = new L.LatLng(newLat, newLng);
            marker.setLatLng(newLatLng);
            map.panTo(newLatLng);
        }
    }
};

function updateInputs(lat, lng) {
    document.getElementById("latitude").value = parseFloat(lat).toFixed(7);
    document.getElementById("longitude").value = parseFloat(lng).toFixed(7);
}

// --- LOGIKA PENCARIAN & LOADING ---
var btnCari = document.getElementById("btnCariOtomatis");
if (btnCari) {
    btnCari.addEventListener("click", function () {
        var keyword = document.getElementById("namaTempat").value;
        if (!keyword) keyword = prompt("Masukkan nama lokasi:");

        if (keyword) {
            // 1. UBAH TOMBOL JADI LOADING
            var originalText = btnCari.innerHTML;
            btnCari.innerHTML =
                '<i class="ri-loader-4-line ri-spin"></i> Sedang Mencari Lokasi...';
            btnCari.disabled = true;
            btnCari.style.opacity = "0.7";

            fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${keyword}&limit=1`,
            )
                .then((r) => r.json())
                .then((data) => {
                    if (data.length > 0) {
                        var lat = data[0].lat;
                        var lon = data[0].lon;
                        var alamatLengkap = data[0].display_name; // AMBIL ALAMAT

                        // Pindah Marker
                        marker.setLatLng([lat, lon]);
                        map.setView([lat, lon], 16);
                        updateInputs(lat, lon);

                        // ISI INPUT ALAMAT OTOMATIS
                        var alamatField = document.getElementById("alamatInp");
                        if (alamatField) {
                            alamatField.value = alamatLengkap; // Isi textarea
                            // Update Preview Box Realtime
                            if (window.updatePreview) window.updatePreview();
                        }
                    } else {
                        alert("Lokasi tidak ditemukan! Coba nama lain.");
                    }
                })
                .catch((err) => {
                    console.error(err);
                    alert("Gagal koneksi ke server peta.");
                })
                .finally(() => {
                    // 2. KEMBALIKAN TOMBOL SEPERTI SEMULA
                    btnCari.innerHTML = originalText;
                    btnCari.disabled = false;
                    btnCari.style.opacity = "1";
                });
        }
    });
}

// Fungsi: Ambil Alamat dari Koordinat (Reverse Geocoding)
function getAddressFromLatLon(lat, lng) {
    var alamatField = document.getElementById('alamatInp');
    
    // 1. Kasih feedback loading ke user biar gak bingung
    if(alamatField) {
        alamatField.value = "Sedang mengambil detail alamat...";
        alamatField.style.opacity = "0.7";
    }

    // 2. Request ke API Nominatim
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                // 3. Masukkan hasil ke Textarea
                alamatField.value = data.display_name;
                
                // 4. Update Preview Box (Biar sinkron)
                if(window.updatePreview) window.updatePreview();
            } else {
                alamatField.value = "Alamat tidak ditemukan.";
            }
        })
        .catch(err => {
            console.error("Gagal ambil alamat:", err);
            alamatField.value = ""; // Kosongkan jika error
        })
        .finally(() => {
            // Kembalikan opacity
            alamatField.style.opacity = "1";
        });
}