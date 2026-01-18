<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wisata - SmartAdmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        
        /* HEADER (STATIS/TIDAK STICKY) */
        .top-header { 
            position: relative !important; top: auto !important; 
            background: transparent; padding: 30px 40px 10px 40px; border: none; 
            display: flex; justify-content: space-between; align-items: center; z-index: 1;
        }
        .page-title { font-size: 24px; font-weight: 800; color: #111827; letter-spacing: -0.5px; margin: 0; }
        .back-btn { text-decoration: none; color: #6b7280; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .back-btn:hover { color: #4f46e5; transform: translateX(-3px); }

        /* LAYOUT GRID */
        .edit-container { 
            max-width: 1100px; margin: 0 auto 50px auto; 
            display: grid; grid-template-columns: 2fr 1fr; gap: 30px; 
        }
        @media (max-width: 1024px) { .edit-container { grid-template-columns: 1fr; } }

        /* CARD STYLE */
        .card { 
            background: white; border-radius: 24px; padding: 35px; 
            border: 1px solid #f1f5f9; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.03); 
            margin-bottom: 30px;
        }
        .card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f8fafc; }
        .card-title { font-size: 16px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-icon { font-size: 20px; color: #4f46e5; }

        /* INPUTS */
        .form-group { margin-bottom: 25px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .form-input {
            width: 100%; padding: 14px 18px; border-radius: 14px; border: 1px solid #e2e8f0;
            font-size: 14px; color: #1e293b; background: #fcfcfc; outline: none; transition: 0.2s; font-weight: 500;
        }
        .form-input:focus { border-color: #f97316; background: white; box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1); }

        /* CATEGORY GRID */
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 12px; }
        .cat-box {
            position: relative; background: #ffffff; border: 2px solid #f1f5f9; border-radius: 16px;
            padding: 15px 5px; text-align: center; cursor: pointer; transition: 0.3s;
            display: flex; flex-direction: column; align-items: center; height: 100%;
        }
        .cat-input { display: none; }
        .cat-box:hover { transform: translateY(-5px); border-color: #fed7aa; }
        .cat-input:checked + .cat-box {
            border-color: #f97316; background: linear-gradient(145deg, #fff7ed 0%, #ffffff 100%);
            box-shadow: 0 10px 25px -5px rgba(249, 115, 22, 0.2); transform: scale(1.02);
        }
        .cat-icon { width: 36px; height: 36px; object-fit: contain; margin-bottom: 8px; }
        .cat-name { font-size: 11px; font-weight: 700; color: #64748b; }
        .cat-input:checked + .cat-box .cat-name { color: #c2410c; }
        
        .cat-check {
            position: absolute; top: 6px; right: 6px; width: 18px; height: 18px;
            background: #f97316; border-radius: 50%; color: white; font-size: 10px;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transform: scale(0); transition: 0.3s;
        }
        .cat-input:checked + .cat-box .cat-check { opacity: 1; transform: scale(1); }

        /* IMAGE PREVIEW */
        .img-container { position: relative; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .current-img { width: 100%; height: 180px; object-fit: cover; display: block; }
        .img-badge { 
            position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.7); 
            color: white; padding: 5px 12px; border-radius: 50px; font-size: 11px; font-weight: 600; 
            backdrop-filter: blur(4px);
        }

        /* MAP */
        #map { height: 350px; border-radius: 20px; width: 100%; border: 1px solid #e2e8f0; background: #f1f5f9; }

        /* DAYS GRID */
        .checkbox-group { display: grid; grid-template-columns: repeat(auto-fill, minmax(65px, 1fr)); gap: 8px; }
        .check-chip input { display: none; }
        .check-label {
            display: flex; align-items: center; justify-content: center; padding: 10px 5px;
            background: white; border: 1px solid #e2e8f0; border-radius: 12px;
            font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer; transition: 0.2s;
        }
        .check-chip input:checked + .check-label { 
            background: #f97316; color: white; border-color: #f97316; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3); 
        }

        /* BUTTONS (STATIC) */
        .btn-save {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; border: none; 
            padding: 16px; border-radius: 16px; font-weight: 700; width: 100%; cursor: pointer; 
            transition: 0.2s; box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.4); font-size: 15px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -8px rgba(234, 88, 12, 0.5); }
        
        .btn-cancel {
            background: white; border: 1px solid #e2e8f0; color: #64748b; padding: 14px; 
            border-radius: 16px; font-weight: 600; width: 100%; cursor: pointer; transition: 0.2s; 
            text-align: center; text-decoration: none; display: block; margin-top: 15px;
        }
        .btn-cancel:hover { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }

    </style>
</head>

<body class="admin-layout">

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo">Smart<span>Admin</span></div>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-label">Menu Utama</li>
            <li><a href="{{ route('admin.index') }}" class="menu-link"><i class="ri-dashboard-3-line icon"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.create') }}" class="menu-link"><i class="ri-add-circle-line icon"></i> Tambah Wisata</a></li>
            <li class="menu-label">Pengaturan</li>
            <li><a href="/" target="_blank" class="menu-link"><i class="ri-global-line icon"></i> Lihat Website</a></li>
        </ul>
    </aside>

    <main class="main-content">
        
        <header class="top-header">
            <div>
                <a href="{{ route('admin.index') }}" class="back-btn"><i class="ri-arrow-left-line"></i> Kembali ke Dashboard</a>
                <h1 style="font-size: 24px; font-weight: 800; color: #111827; margin-top: 5px;">Edit Destinasi</h1>
            </div>
            <div class="nav-profile"><div class="avatar-circle" style="background: #fff7ed; color: #ea580c;">✎</div></div>
        </header>

        <div class="content-wrapper">
            <form action="{{ route('admin.update', $wisata->id) }}" method="POST" enctype="multipart/form-data" class="edit-container">
                @csrf
                @method('PUT')

                <div style="display: flex; flex-direction: column;">
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="ri-file-edit-line card-icon"></i>
                            <span class="card-title">Informasi Dasar</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Nama Tempat</label>
                                <input type="text" name="nama_tempat" class="form-input" value="{{ $wisata->nama_tempat }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Harga Tiket (Rp)</label>
                                <input type="number" name="harga_tiket" class="form-input" value="{{ $wisata->harga_tiket }}" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-input" rows="4">{{ $wisata->deskripsi }}</textarea>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="ri-map-pin-2-line card-icon"></i>
                            <span class="card-title">Lokasi & Peta</span>
                        </div>
                        <div id="map"></div>
                        <p style="font-size: 12px; color: #9ca3af; margin: 10px 0 20px 0; display: flex; align-items: center; gap: 5px;">
                            <i class="ri-information-line"></i> Geser <b>Pin Biru</b> untuk memperbarui koordinat.
                        </p>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-input" value="{{ $wisata->latitude }}" readonly style="background: #f1f5f9; color: #64748b;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-input" value="{{ $wisata->longitude }}" readonly style="background: #f1f5f9; color: #64748b;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-input" rows="2">{{ $wisata->alamat }}</textarea>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="ri-time-line card-icon"></i>
                            <span class="card-title">Jadwal Operasional</span>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px; cursor: pointer; background: #fff7ed; padding: 10px; border-radius: 10px; border: 1px solid #ffedd5;">
                                <input type="checkbox" id="is24Jam" name="is_24_jam" {{ $wisata->jam_buka == '24 Jam' ? 'checked' : '' }}>
                                <span style="font-size: 13px; font-weight: 700; color: #ea580c;">Buka 24 Jam Penuh</span>
                            </label>
                            
                            @php
                                $jamRaw = $wisata->jam_buka;
                                $jb = ''; $jt = '';
                                if($jamRaw != '24 Jam' && str_contains($jamRaw, '-')) {
                                    $parts = explode('-', str_replace(' WITA', '', $jamRaw));
                                    $jb = trim($parts[0] ?? '');
                                    $jt = trim($parts[1] ?? '');
                                }
                            @endphp

                            <div id="jamRange" style="display: flex; align-items: center; gap: 10px; {{ $wisata->jam_buka == '24 Jam' ? 'display:none;' : '' }}">
                                <input type="time" name="jam_buka" id="jamBuka" class="form-input" value="{{ $jb }}">
                                <span style="font-weight: bold; color: #cbd5e1;">—</span>
                                <input type="time" name="jam_tutup" id="jamTutup" class="form-input" value="{{ $jt }}">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Hari Buka</label>
                            <div class="checkbox-group">
                                @php $hariDB = explode(',', $wisata->hari_buka); @endphp
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <label class="check-chip">
                                        <input type="checkbox" name="hari_buka[]" value="{{ $hari }}" {{ in_array($hari, $hariDB) ? 'checked' : '' }}>
                                        <span class="check-label">{{ $hari }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                <div style="display: flex; flex-direction: column;">
                    
                    <div class="card" style="padding: 25px;">
                        <button type="submit" class="btn-save">
                            <i class="ri-save-3-line"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.index') }}" class="btn-cancel">Batal</a>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="ri-image-2-line card-icon"></i>
                            <span class="card-title">Visual</span>
                        </div>
                        @if($wisata->gambar)
                            <div class="img-container">
                                <img src="{{ Str::startsWith($wisata->gambar, ['http', 'data:']) ? $wisata->gambar : asset('images/' . $wisata->gambar) }}" class="current-img">
                                <div class="img-badge">Terpasang</div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label">Ganti Foto</label>
                            <input type="file" name="gambar_file" class="form-input" accept="image/*" style="background: white;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Atau URL Link</label>
                            <input type="url" name="gambar_url" class="form-input" placeholder="https://..." value="{{ Str::startsWith($wisata->gambar, 'http') ? $wisata->gambar : '' }}" style="background: white;">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="ri-layout-grid-line card-icon"></i>
                            <span class="card-title">Kategori</span>
                        </div>
                        <div class="cat-grid">
                            @php $listKategori = ['Alam'=>'alam.png', 'Religi'=>'religi.png', 'Kuliner'=>'kuliner.png', 'Belanja'=>'belanja.png', 'Budaya'=>'budaya.png', 'Edukasi'=>'edukasi.png', 'Rekreasi'=>'rekreasi.png', 'Agrowisata'=>'agro.png']; @endphp
                            @foreach ($listKategori as $nama => $icon)
                                <label>
                                    <input type="radio" name="kategori" value="{{ $nama }}" class="cat-input" {{ $wisata->kategori == $nama ? 'checked' : '' }}>
                                    <div class="cat-box">
                                        <div class="cat-check"><i class="ri-check-line"></i></div>
                                        <img src="{{ asset('icons/' . $icon) }}" class="cat-icon">
                                        <span class="cat-name">{{ $nama }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // MAP SETUP
            const dbLat = {{ $wisata->latitude }};
            const dbLng = {{ $wisata->longitude }};
            const map = L.map('map').setView([dbLat, dbLng], 15);
            L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map);
            
            const marker = L.marker([dbLat, dbLng], {draggable: true}).addTo(map);
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                latInput.value = pos.lat.toFixed(6);
                lngInput.value = pos.lng.toFixed(6);
            });

            // JAM 24 JAM TOGGLE
            const is24 = document.getElementById('is24Jam');
            const jamRange = document.getElementById('jamRange');
            const jamBuka = document.getElementById('jamBuka');
            const jamTutup = document.getElementById('jamTutup');

            is24.addEventListener('change', function() {
                if(this.checked) {
                    jamRange.style.display = 'none';
                    jamBuka.value = ''; jamTutup.value = '';
                } else {
                    jamRange.style.display = 'flex';
                }
            });
        });
    </script>

</body>
</html>