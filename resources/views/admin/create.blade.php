<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - SmartAdmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body class="admin-layout">

    @include('admin.components_admin.sidebar')

    <main class="main-content">

        <header class="top-header">
            <a href="{{ route('admin.index') }}" class="back-btn"><i class="ri-arrow-left-line"></i> Batal & Kembali</a>
            <div class="nav-profile">
                <div class="avatar-circle">A</div>
            </div>
        </header>

        <div class="content-wrapper">

            <div style="text-align: center; margin-bottom: 40px;">
                <h1 style="font-size: 28px; font-weight: 800; color: #111827; margin-bottom: 8px;">Tambah Destinasi Baru
                </h1>
                <p style="color: #6b7280; font-size: 15px;">Lengkapi informasi wisata secara bertahap</p>
            </div>

            <div class="wizard-steps">
                <div class="step-item active" id="ind-0">
                    <div class="step-icon"><i class="ri-file-list-3-line"></i></div>
                    <span class="step-label">Informasi</span>
                </div>
                <div class="step-item" id="ind-1">
                    <div class="step-icon"><i class="ri-image-line"></i></div>
                    <span class="step-label">Visual</span>
                </div>
                <div class="step-item" id="ind-2">
                    <div class="step-icon"><i class="ri-map-pin-line"></i></div>
                    <span class="step-label">Lokasi</span>
                </div>
            </div>

            <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
                @csrf

                <div class="wizard-content active">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Tempat Wisata</label>
                            <input type="text" name="nama_tempat" id="namaTempat" class="form-input"
                                placeholder="Contoh: Menara Pandang" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Tiket (Rp)</label>
                            <input type="text" id="hargaDisplay" class="form-input" placeholder="0 jika gratis"
                                onkeyup="formatRupiah(this)"
                                value="{{ old('harga_tiket') ? number_format(old('harga_tiket'), 0, ',', '.') : '' }}">
                            <input type="hidden" name="harga_tiket" id="hargaReal" value="{{ old('harga_tiket') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori Utama</label>
                        <div class="cat-grid">
                            @php
                                $listKategori = [
                                    'Alam' => 'alam.png',
                                    'Religi' => 'religi.png',
                                    'Kuliner' => 'kuliner.png',
                                    'Belanja' => 'belanja.png',
                                    'Budaya' => 'budaya.png',
                                    'Edukasi' => 'edukasi.png',
                                    'Rekreasi' => 'rekreasi.png',
                                    'Agrowisata' => 'agro.png',
                                ];
                            @endphp
                            @foreach ($listKategori as $nama => $icon)
                                <label>
                                    <input type="radio" name="kategori" value="{{ $nama }}" class="cat-input"
                                        required>
                                    <div class="cat-box">
                                        <div class="cat-check"><i class="ri-check-line"></i></div>

                                        <img src="{{ asset('icons/' . $icon) }}" class="cat-icon">
                                        <span class="cat-name">{{ $nama }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label">Fasilitas Tersedia</label>
                        <div class="fasilitas-grid">
                            @php
                                $fasilitasUmum = [
                                    'Area Parkir', 'Toilet Umum', 'Mushola', 'Spot Foto', 
                                    'Warung Makan', 'Gazebo', 'Wifi', 'Toko Oleh-oleh', 
                                    'Camping Ground', 'Playground'
                                ];
                            @endphp

                            @foreach($fasilitasUmum as $f)
                                <label class="fasilitas-check">
                                    <input type="checkbox" name="fasilitas[]" value="{{ $f }}" 
                                        {{ (is_array(old('fasilitas')) && in_array($f, old('fasilitas'))) ? 'checked' : '' }}>
                                    <span class="fasilitas-label">
                                        <i class="ri-checkbox-circle-line"></i> {{ $f }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-input" rows="3" placeholder="Jelaskan daya tarik tempat ini..."></textarea>
                    </div>
                    
                </div>

                <div class="wizard-content">
                    <div class="upload-area" onclick="document.getElementById('fileInp').click()">
                        <i class="ri-upload-cloud-2-line upload-icon"></i>
                        <h4 style="font-weight: 700; color: #374151; margin-bottom: 5px;">Klik untuk Upload Foto</h4>
                        <p style="font-size: 13px; color: #9ca3af;">Bisa pilih BANYAK foto sekaligus (Max 5 foto)</p>
                        
                        <input type="file" id="fileInp" name="gambar_file[]" accept="image/*" multiple style="display: none;" onchange="previewFile(this)">
                    </div>

                    <div id="filePreviewContainer" style="text-align: center; margin-top: 10px; font-size: 13px; font-weight: 600; color: #4f46e5;"></div>

                    <div style="text-align: center; margin: 20px 0; position: relative;">
                        <span
                            style="background: white; padding: 0 15px; color: #9ca3af; font-size: 12px; font-weight: 700; position: relative; z-index: 1;">ATAU
                            GUNAKAN LINK</span>
                        <div
                            style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e5e7eb; z-index: 0;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">URL Gambar Eksternal</label>
                        <input type="url" name="gambar_url" class="form-input" placeholder="https://...">
                    </div>
                </div>

                <div class="wizard-content">

                    <div style="margin-bottom: 25px;">
                        <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Titik & Detail Lokasi
                        </h3>
                        <p style="font-size: 13px; color: #64748b; margin-top: 4px;">Pastikan titik peta sesuai agar
                            pengunjung tidak tersesat.</p>
                    </div>
                    <button type="button" id="btnCariOtomatis" class="btn-search-location mt-3">
                        <i class="ri-map-pin-search-line"></i> Cari Lokasi Berdasarkan Nama Tempat
                    </button>

                    <div class="form-group mb-4">
                        <div
                            style="position: relative; width: 100%; height: 380px; border-radius: 20px; overflow: hidden; border: 4px solid white; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);">
                            <div id="map" style="width: 100%; height: 100%; z-index: 1;"></div>
                        </div>
                    </div>

                    <div class="row-nowrap mb-4">
                        <div class="col-half">
                            <label class="form-label">Latitude</label>
                            <div class="input-group-modern">
                                <i class="ri-latitude-view"></i>
                                <input type="text" name="latitude" id="latitude" placeholder="-3.440..."
                                    required oninput="updateMapFromInput()">
                            </div>
                        </div>

                        <div class="col-half">
                            <label class="form-label">Longitude</label>
                            <div class="input-group-modern" style="margin-bottom: 20px;">
                                <i class="ri-longitude-view"></i>
                                <input type="text" name="longitude" id="longitude" placeholder="114.833..."
                                    required oninput="updateMapFromInput()">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamatInp" class="form-input" rows="3"
                            placeholder="Jalan, Kelurahan, Kecamatan..." style="resize: none; line-height: 1.6;" required
                            oninput="window.updatePreview && window.updatePreview()"></textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 30px 0;">

                    <div class="hours-card mb-4"
                        style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 20px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <label class="form-label" style="margin: 0; font-size: 14px;">Jam Operasional</label>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is24Jam" name="is_24_jam"
                                    style="cursor: pointer;">
                                <label class="form-check-label" for="is24Jam"
                                    style="font-size: 12px; font-weight: 600; color: #4f46e5; margin-left: 8px;">Buka
                                    24 Jam</label>
                            </div>
                        </div>

                        <div id="jamRange" class="jam-container">
                            <div class="jam-box">
                                <div class="time-input-wrapper">
                                    <input type="time" name="jam_buka" id="jamBuka">
                                </div>
                                <div
                                    style="text-align: center; font-size: 10px; color: #94a3b8; margin-top: 5px; font-weight: 700;">
                                    BUKA</div>
                            </div>

                            <div class="jam-separator">
                                <i class="ri-arrow-right-line"></i>
                            </div>

                            <div class="jam-box">
                                <div class="time-input-wrapper">
                                    <input type="time" name="jam_tutup" id="jamTutup">
                                </div>
                                <div
                                    style="text-align: center; font-size: 10px; color: #94a3b8; margin-top: 5px; font-weight: 700;">
                                    TUTUP</div>
                            </div>
                        </div>

                        <input type="hidden" name="jam_operasional" id="jamOperasional">
                    </div>

                    <div class="form-group">
                        <label class="form-label mb-3">Hari Buka</label>
                        <div class="checkbox-group">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                <label class="check-chip">
                                    <input type="checkbox" name="hari_buka[]" value="{{ $hari }}" checked>
                                    <span class="check-label">{{ $hari }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="previewBox" class="preview-box" style="display: none;">
                        <div class="prev-header">
                            <i class="ri-file-list-3-line" style="font-size: 20px; color: #4f46e5;"></i>
                            <span class="prev-title">Review Data</span>
                        </div>

                        <div class="prev-grid">
                            <div class="prev-item">
                                <span class="prev-label">Tempat</span>
                                <span class="prev-val" id="pvNama">-</span>
                            </div>
                            <div class="prev-item">
                                <span class="prev-label">Kategori</span>
                                <div><span class="prev-val prev-tag" id="pvKategori">-</span></div>
                            </div>
                            <div class="prev-item">
                                <span class="prev-label">Harga</span>
                                <span class="prev-val" id="pvHarga" style="color: #059669;">-</span>
                            </div>
                            <div class="prev-item">
                                <span class="prev-label">Jam</span>
                                <span class="prev-val" id="pvJam">-</span>
                            </div>
                            <div class="prev-item" style="grid-column: span 2;">
                                <span class="prev-label">Alamat</span>
                                <span class="prev-val" id="pvAlamat"
                                    style="color: #64748b; font-weight: 500;">-</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="wizard-actions">
                    <button type="button" class="btn-prev" id="prevBtn" style="display: none;">Kembali</button>
                    <button type="button" class="btn-next" id="nextBtn">Lanjut <i
                            class="ri-arrow-right-line"></i></button>
                    <button type="submit" class="btn-next" id="submitBtn"
                        style="display: none; background: #10b981;">Simpan Data <i class="ri-check-line"></i></button>
                </div>

            </form>
        </div>
    </main>

    <div class="smart-modal-overlay" id="customModal">
        <div class="smart-modal-card">
            <div class="modal-icon-box" id="modalIcon">
                <i class="ri-alert-line"></i>
            </div>
            <h3 class="modal-title" id="modalTitle">Judul Pesan</h3>
            <p class="modal-desc" id="modalDesc">Deskripsi pesan akan muncul di sini.</p>

            <div class="modal-actions" id="modalActions">
            </div>
        </div>
    </div>
    <script>
        function formatRupiah(input) {
            // 1. Bersihkan karakter selain angka
            let angka = input.value.replace(/\D/g, '');

            // 2. Simpan angka murni ke input hidden (untuk dikirim ke database)
            document.getElementById('hargaReal').value = angka;

            // 3. Format tampilan dengan titik (untuk user)
            if (angka === "") {
                input.value = "";
            } else {
                input.value = new Intl.NumberFormat('id-ID').format(angka);
            }

            // (Opsional) Update Preview Box Realtime
            if (window.updatePreview) window.updatePreview();
        }
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- <script src="{{ asset('js/admin/map.js') }}"></script> --}}
    <script src="{{ asset('js/admin/jam-operasional.js') }}"></script>
    <script src="{{ asset('js/admin/preview.js') }}"></script>
    <script src="{{ asset('js/admin/wizard.js') }}"></script>
    <script src="{{ asset('js/admin/create.js') }}"></script>

    <script>
        // Variabel global untuk menampung file
        let dt = new DataTransfer(); 

        function previewFile(input) {
            const container = document.getElementById('filePreviewContainer');
            const inputElement = document.getElementById('fileInp'); // Pastikan ID input kamu 'fileInp'

            // 1. Masukkan file baru ke penampungan (dt)
            for (let file of input.files) {
                dt.items.add(file);
            }

            // 2. Update input file dengan daftar terbaru (agar form bisa kirim semua data)
            inputElement.files = dt.files;

            // 3. Render ulang tampilan
            renderPreview(container);
        }

        function renderPreview(container) {
            container.innerHTML = ''; // Bersihkan dulu
            const files = dt.files;

            if (files.length > 0) {
                // Tampilkan jumlah
                const countInfo = document.createElement('div');
                countInfo.style.marginBottom = '10px';
                countInfo.innerHTML = `<strong>${files.length} Foto terpilih:</strong>`;
                container.appendChild(countInfo);

                // Container Grid
                const grid = document.createElement('div');
                grid.style.display = 'flex';
                grid.style.gap = '10px';
                grid.style.flexWrap = 'wrap';
                grid.style.justifyContent = 'center';
                container.appendChild(grid);

                // Loop setiap file untuk dibuatkan thumbnail + tombol X
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Wrapper
                        const wrapper = document.createElement('div');
                        wrapper.className = 'preview-item';

                        // Gambar
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        
                        // Tombol Hapus
                        const btn = document.createElement('button');
                        btn.className = 'btn-remove-img';
                        btn.innerHTML = '✕';
                        btn.type = 'button'; // PENTING: Supaya tidak submit form
                        btn.onclick = function() { removeFile(index); }; // Panggil fungsi hapus

                        wrapper.appendChild(img);
                        wrapper.appendChild(btn);
                        grid.appendChild(wrapper);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                container.innerText = "";
            }
        }

        function removeFile(index) {
            const inputElement = document.getElementById('fileInp');
            const container = document.getElementById('filePreviewContainer');

            // 1. Buat DataTransfer baru (temp)
            const newDt = new DataTransfer();

            // 2. Salin semua file KECUALI yang index-nya mau dihapus
            Array.from(dt.files).forEach((file, i) => {
                if (i !== index) {
                    newDt.items.add(file);
                }
            });

            // 3. Update variabel global & input HTML
            dt = newDt;
            inputElement.files = dt.files;

            // 4. Render ulang
            renderPreview(container);
        }
    </script>
</body>

</html>
