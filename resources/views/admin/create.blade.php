<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body class="admin-wrapper page-create">

    <nav class="admin-navbar">
        <div class="brand-logo">Smart<span>Admin</span></div>
        <div class="nav-profile">
            <span style="font-size: 13px; color: #64748b;">Mode: Tambah Data</span>
            <div class="avatar-circle" style="background:#dcfce7; color:#10b981;">+</div>
        </div>
    </nav>

    <div class="admin-container">

        <a href="{{ route('admin.index') }}" style="text-decoration:none;color:#64748b;font-weight:600;">⬅ Kembali</a>

        <div class="admin-card">

            <h2 style="color:var(--primary); margin-bottom:5px;">Tambah Destinasi</h2>
            <p style="margin-bottom:30px;">Isi data wisata secara bertahap</p>

            {{-- STEP INDICATOR --}}
            <div class="wizard-steps" style="display:flex; gap:10px; margin-bottom:30px;">
                <div class="step active">1. Info</div>
                <div class="step">2. Foto</div>
                <div class="step">3. Lokasi</div>
            </div>
            <div class="progress-wrapper" style="margin-bottom:30px;">
                <div class="progress-bar-bg" style="height:8px; background:#e5e7eb; border-radius:10px;">
                    <div id="progressBar"
                        style="height:8px; width:33%; background:var(--primary); border-radius:10px; transition:width .3s;">
                    </div>
                </div>
            </div>


            <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ================= STEP 1 ================= --}}
                <div class="wizard-content active">

                    <div class="form-section-title">Informasi Utama</div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Tempat Wisata</label>
                            <input type="text" name="nama_tempat" id="namaTempat" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Tiket</label>
                            <input type="number" name="harga_tiket" class="form-input" placeholder="0 jika gratis">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori</label>
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
                                        <img src="{{ asset('icons/' . $icon) }}" class="cat-icon">
                                        <span class="cat-label">{{ $nama }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-input" rows="3"></textarea>
                    </div>

                </div>

                {{-- ================= STEP 2 ================= --}}
                <div class="wizard-content">

                    <div class="form-section-title">Foto Wisata</div>

                    <div class="form-group upload-box">
                        <input type="file" name="gambar_file" class="form-input" accept="image/*">
                        <div class="divider-text">ATAU</div>
                        <input type="url" name="gambar_url" class="form-input" placeholder="URL gambar">
                    </div>

                </div>

                {{-- ================= STEP 3 ================= --}}
                <div class="wizard-content">

                    <div class="form-section-title">Lokasi & Jadwal</div>

                    <div class="form-group">
                        <label class="form-label">Pilih Lokasi di Peta</label>

                        <button type="button" id="btnCariLokasi" class="btn btn-outline" style="margin-bottom:10px;">
                            🔍 Cari lokasi dari nama tempat
                        </button>

                        <div id="map" style="height: 320px; border-radius: 16px; margin-bottom: 15px;">
                        </div>

                        <!-- input tersembunyi -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        <small style="color:#64748b;">
                            Klik peta untuk menentukan lokasi wisata
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-input"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jam Operasional</label>

                        <label style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                            <input type="checkbox" id="is24Jam" name="is_24_jam">
                            <strong>Buka 24 Jam</strong>
                        </label>

                        <div class="form-grid" id="jamRange">
                            <div class="form-group">
                                <label class="form-label">Jam Buka</label>
                                <input type="time" name="jam_buka" id="jamBuka" class="form-input">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jam Tutup</label>
                                <input type="time" name="jam_tutup" id="jamTutup" class="form-input">
                            </div>
                        </div>

                        <input type="hidden" name="jam_operasional" id="jamOperasional">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hari Buka</label>
                        <div class="checkbox-group">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                <label>
                                    <input type="checkbox" name="hari_buka[]" value="{{ $hari }}" checked>
                                    <span class="checkbox-label">{{ $hari }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="previewBox" class="preview-card" style="display:none;">
                        <div class="preview-title">Preview Data Wisata</div>

                        <div class="preview-grid">
                            <div class="preview-item">
                                <strong>Nama</strong>
                                <span id="pvNama"></span>
                            </div>

                            <div class="preview-item">
                                <strong>Kategori</strong>
                                <span id="pvKategori"></span>
                            </div>

                            <div class="preview-item">
                                <strong>Harga</strong>
                                <span id="pvHarga"></span>
                            </div>
                            <div class="preview-item">
                                <strong>Jam Operasional</strong>
                                <span id="pvJam"></span>
                            </div>
                            <div class="preview-item">
                                <strong>Koordinat</strong>
                                <span id="pvKoordinat"></span>
                            </div>

                            <div class="preview-highlight">
                                📍 <span id="pvAlamat"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NAVIGATION --}}
                <div class="wizard-actions" style="display:flex; gap:15px; margin-top:30px;">
                    <button type="button" class="btn btn-outline" id="prevBtn">
                        Kembali
                    </button>

                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Lanjut
                    </button>

                    <button type="submit" class="btn btn-primary " id="submitBtn" style="display:none;">
                        Simpan Data
                    </button>

                </div>
            </form>

        </div>
    </div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="{{ asset('js/admin/map.js') }}"></script>
<script src="{{ asset('js/admin/jam-operasional.js') }}"></script>
<script src="{{ asset('js/admin/preview.js') }}"></script>
<script src="{{ asset('js/admin/wizard.js') }}"></script>
<script src="{{ asset('js/admin/submit.js') }}"></script>


</body>

</html>
