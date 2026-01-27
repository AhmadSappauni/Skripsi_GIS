<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - SmartAdmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
    /* --- 1. GLOBAL & RESET --- */
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; }
    
    /* Header Fix */
    .top-header { 
        position: relative !important; 
        top: auto !important; 
        background: transparent; 
        padding: 30px 40px 10px 40px; 
        border: none; 
        display: flex; justify-content: space-between; align-items: center; z-index: 1;
    }
    
    .back-btn { text-decoration: none; color: #6b7280; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
    .back-btn:hover { color: #4f46e5; transform: translateX(-3px); }

    /* --- 2. WIZARD STEPS (HEADER PROGRESS) --- */
    .wizard-steps { display: flex; justify-content: center; gap: 40px; margin: 20px 0 40px 0; position: relative; }
    .wizard-steps::before {
        content: ''; position: absolute; top: 20px; left: 20%; right: 20%; height: 2px;
        background: #e5e7eb; z-index: 0;
    }
    .step-item { position: relative; z-index: 1; text-align: center; width: 100px; opacity: 0.6; transition: 0.3s; }
    .step-item.active { opacity: 1; }
    
    .step-icon {
        width: 40px; height: 40px; background: white; border: 2px solid #d1d5db; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto;
        font-size: 18px; color: #6b7280; transition: 0.3s; font-weight: 700;
    }
    .step-item.active .step-icon { background: #4f46e5; color: white; border-color: #4f46e5; box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.15); }
    .step-item.completed .step-icon { background: #10b981; color: white; border-color: #10b981; }
    .step-label { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #374151; letter-spacing: 0.5px; }

    /* --- 3. FORM CARD CONTAINER --- */
    .form-card {
        background: white; border-radius: 24px; padding: 40px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); border: 1px solid #f3f4f6;
        max-width: 900px; margin: 0 auto 50px auto;
    }

    /* --- 4. FORM ELEMENTS (INPUTS, LABELS) --- */
    .form-group { margin-bottom: 25px; }
    .form-label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Standard Input */
    .form-input {
        width: 100%; padding: 14px 18px; border-radius: 14px; border: 1px solid #e5e7eb;
        font-size: 14px; color: #111827; background: #f9fafb; outline: none; transition: 0.2s; font-weight: 500;
    }
    .form-input:focus { border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    /* --- 5. MODERN INPUTS (UNTUK LAT/LONG) --- */
    .input-group-modern {
        display: flex; align-items: center; background: #ffffff;
        border: 1px solid #e2e8f0; border-radius: 12px; padding: 5px 15px; transition: all 0.3s ease;
    }
    .input-group-modern:focus-within { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    .input-group-modern i { color: #94a3b8; font-size: 18px; margin-right: 12px; }
    .input-group-modern input { border: none; outline: none; width: 100%; font-size: 14px; font-weight: 600; color: #334155; background: transparent; }

    /* --- 6. LAYOUT HELPERS (SIDE-BY-SIDE) --- */
    .row-nowrap { display: flex; gap: 20px; align-items: flex-start; width: 100%; }
    .col-half { flex: 1; width: 50%; }
    @media (max-width: 600px) { .row-nowrap { flex-direction: column; gap: 15px; } .col-half { width: 100%; } }

    /* --- 7. KATEGORI SELECTION --- */
    .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; }
    .cat-input { display: none; }
    .cat-box {
        position: relative; background: #ffffff; border: 2px solid #f1f5f9; border-radius: 20px;
        padding: 20px 10px; text-align: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;
    }
    .cat-box:hover { transform: translateY(-5px); border-color: #cbd5e1; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05); }
    .cat-input:checked + .cat-box {
        border-color: #6366f1; background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.25); transform: translateY(-3px) scale(1.02);
    }
    .cat-icon { width: 48px; height: 48px; object-fit: contain; margin-bottom: 12px; transition: 0.3s; }
    .cat-input:checked + .cat-box .cat-icon { transform: scale(1.1); filter: drop-shadow(0 5px 10px rgba(99,102,241,0.2)); }
    .cat-name { font-size: 13px; font-weight: 700; color: #64748b; }
    .cat-input:checked + .cat-box .cat-name { color: #4f46e5; }
    
    /* Centang Badge */
    .cat-check {
        position: absolute; top: 10px; right: 10px; width: 22px; height: 22px; background: #4f46e5;
        border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px;
        opacity: 0; transform: scale(0); transition: 0.3s cubic-bezier(0.5, 1.6, 0.4, 0.7);
    }
    .cat-input:checked + .cat-box .cat-check { opacity: 1; transform: scale(1); }

    /* --- 8. HARI BUKA & JAM OPERASIONAL --- */
    .checkbox-group { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
    .check-chip input { display: none; }
    .check-label {
        display: flex; align-items: center; justify-content: center; padding: 10px; background: white;
        border: 1px solid #e5e7eb; border-radius: 12px; font-size: 13px; font-weight: 600; color: #6b7280;
        cursor: pointer; transition: 0.2s; text-align: center;
    }
    .check-chip input:checked + .check-label { background: #4f46e5; color: white; border-color: #4f46e5; }

    /* Jam Operasional Container */
    .hours-card { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 20px; }
    .jam-container { display: flex; align-items: center; gap: 15px; width: 100%; }
    .jam-box { flex: 1; }
    .jam-separator { color: #cbd5e1; font-size: 20px; padding-bottom: 20px; }
    
    .time-input-wrapper {
        background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; display: flex; justify-content: center;
    }
    .time-input-wrapper input { border: none; outline: none; font-weight: 700; color: #334155; font-family: monospace; width: 100%; text-align: center; }

    /* --- 9. MAP & BUTTONS --- */
    .upload-area { border: 2px dashed #d1d5db; border-radius: 20px; padding: 40px; text-align: center; background: #f9fafb; cursor: pointer; transition: 0.2s; }
    .upload-area:hover { border-color: #4f46e5; background: #eef2ff; }
    
    #map { height: 380px; border-radius: 20px; width: 100%; border: 4px solid white; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); z-index: 1; background: #f3f4f6; }

    .btn-search-location {
        width: 100%; padding: 12px; border-radius: 12px; background: #eef2ff; color: #4f46e5;
        border: 1px dashed #c7d2fe; font-weight: 700; font-size: 14px; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; margin-top: 15px;
    }
    .btn-search-location:hover { background: #4f46e5; color: white; border-style: solid; }

    /* --- 10. PREVIEW BOX (ELEGANT STYLE) --- */
    .preview-box { 
        background: #ffffff; border-radius: 20px; padding: 30px; border: 1px solid #e2e8f0; 
        margin-top: 30px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); position: relative; overflow: hidden;
    }
    .preview-box::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 6px; background: linear-gradient(to bottom, #4f46e5, #818cf8); }
    
    .prev-header { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .prev-title { font-size: 15px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .prev-grid { display: grid; gap: 15px; }
    .prev-item { display: grid; grid-template-columns: 140px 1fr; align-items: flex-start; font-size: 14px; }
    .prev-label { color: #94a3b8; font-weight: 600; font-size: 12px; text-transform: uppercase; margin-top: 3px; }
    .prev-val { color: #334155; font-weight: 700; line-height: 1.5; }
    .prev-tag { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #f1f5f9; color: #475569; }

    /* --- 11. NAVIGATION & MODALS --- */
    .wizard-actions { display: flex; justify-content: space-between; margin-top: 40px; border-top: 1px solid #f3f4f6; padding-top: 30px; }
    .btn-prev { background: white; border: 1px solid #e5e7eb; color: #6b7280; padding: 12px 24px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-prev:hover { background: #f9fafb; color: #111827; }
    
    .btn-next { background: #111827; color: white; border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 8px; }
    .btn-next:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -8px rgba(0,0,0,0.3); }

    /* Wizard Content Animation */
    .wizard-content { display: none; animation: fadeIn 0.4s ease; }
    .wizard-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Custom Modal */
    .smart-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(12px); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s ease; }
    .smart-modal-overlay.open { opacity: 1; pointer-events: auto; }
    .smart-modal-card { background: white; width: 90%; max-width: 420px; border-radius: 28px; padding: 30px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); text-align: center; transform: scale(0.9) translateY(20px); transition: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .smart-modal-overlay.open .smart-modal-card { transform: scale(1) translateY(0); }
    
    .modal-icon-box { width: 70px; height: 70px; border-radius: 50%; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center; font-size: 32px; position: relative; }
    .icon-warning { background: #fef3c7; color: #d97706; }
    .icon-question { background: #e0e7ff; color: #4f46e5; }
    
    .modal-title { font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 8px; }
    .modal-desc { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 25px; }
    .modal-actions { display: flex; gap: 10px; }
    .btn-modal { padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; border: none; flex: 1; }
    .btn-primary-modal { background: #4f46e5; color: white; }
    .btn-secondary-modal { background: #f3f4f6; color: #4b5563; }
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
            <li><a href="{{ route('admin.create') }}" class="menu-link active"><i class="ri-add-circle-line icon"></i> Tambah Wisata</a></li>
            <li class="menu-label">Pengaturan</li>
            <li><a href="/" target="_blank" class="menu-link"><i class="ri-global-line icon"></i> Lihat Website</a></li>
        </ul>
    </aside>

    <main class="main-content">
        
        <header class="top-header">
            <a href="{{ route('admin.index') }}" class="back-btn"><i class="ri-arrow-left-line"></i> Batal & Kembali</a>
            <div class="nav-profile"><div class="avatar-circle">A</div></div>
        </header>

        <div class="content-wrapper">
            
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 style="font-size: 28px; font-weight: 800; color: #111827; margin-bottom: 8px;">Tambah Destinasi Baru</h1>
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
                            <input type="text" name="nama_tempat" id="namaTempat" class="form-input" placeholder="Contoh: Menara Pandang" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Tiket (Rp)</label>
                            <input type="number" name="harga_tiket" id="hargaTiket" class="form-input" placeholder="0 jika gratis">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori Utama</label>
                        <div class="cat-grid">
                            @php 
                                $listKategori = ['Alam'=>'alam.png', 'Religi'=>'religi.png', 'Kuliner'=>'kuliner.png', 'Belanja'=>'belanja.png', 'Budaya'=>'budaya.png', 'Edukasi'=>'edukasi.png', 'Rekreasi'=>'rekreasi.png', 'Agrowisata'=>'agro.png']; 
                            @endphp
                            @foreach ($listKategori as $nama => $icon)
                                <label>
                                    <input type="radio" name="kategori" value="{{ $nama }}" class="cat-input" required>
                                    <div class="cat-box">
                                        <div class="cat-check"><i class="ri-check-line"></i></div>
                                        
                                        <img src="{{ asset('icons/' . $icon) }}" class="cat-icon">
                                        <span class="cat-name">{{ $nama }}</span>
                                    </div>
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
                        <p style="font-size: 13px; color: #9ca3af;">Format: JPG, PNG, WEBP (Max 2MB)</p>
                        <input type="file" id="fileInp" name="gambar_file" accept="image/*" style="display: none;" onchange="previewFile(this)">
                    </div>
                    <div id="filePreviewName" style="text-align: center; margin-top: 10px; font-size: 13px; font-weight: 600; color: #4f46e5;"></div>

                    <div style="text-align: center; margin: 20px 0; position: relative;">
                        <span style="background: white; padding: 0 15px; color: #9ca3af; font-size: 12px; font-weight: 700; position: relative; z-index: 1;">ATAU GUNAKAN LINK</span>
                        <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e5e7eb; z-index: 0;"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">URL Gambar Eksternal</label>
                        <input type="url" name="gambar_url" class="form-input" placeholder="https://...">
                    </div>
                </div>

                <div class="wizard-content">
    
    <div style="margin-bottom: 25px;">
        <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Titik & Detail Lokasi</h3>
        <p style="font-size: 13px; color: #64748b; margin-top: 4px;">Pastikan titik peta sesuai agar pengunjung tidak tersesat.</p>
    </div>
    <button type="button" id="btnCariOtomatis" class="btn-search-location mt-3">
            <i class="ri-map-pin-search-line"></i> Cari Lokasi Berdasarkan Nama Tempat
        </button>

    <div class="form-group mb-4">
        <div style="position: relative; width: 100%; height: 380px; border-radius: 20px; overflow: hidden; border: 4px solid white; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);">
            <div id="map" style="width: 100%; height: 100%; z-index: 1;"></div>
        </div>
    </div>

    <div class="row-nowrap mb-4">
        <div class="col-half">
            <label class="form-label">Latitude</label>
            <div class="input-group-modern">
                <i class="ri-latitude-view"></i>
                <input type="text" name="latitude" id="latitude" placeholder="-3.440..." required oninput="updateMapFromInput()">
            </div>
        </div>
        
        <div class="col-half">
            <label class="form-label">Longitude</label>
            <div class="input-group-modern" style="margin-bottom: 20px;">
                <i class="ri-longitude-view"></i>
                <input type="text" name="longitude" id="longitude" placeholder="114.833..." required oninput="updateMapFromInput()">
            </div>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label">Alamat Lengkap</label>
        <textarea name="alamat" id="alamatInp" class="form-input" rows="3" 
            placeholder="Jalan, Kelurahan, Kecamatan..." 
            style="resize: none; line-height: 1.6;" 
            required 
            oninput="window.updatePreview && window.updatePreview()"></textarea>
    </div>

    <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 30px 0;">

    <div class="hours-card mb-4" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <label class="form-label" style="margin: 0; font-size: 14px;">Jam Operasional</label>
            
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is24Jam" name="is_24_jam" style="cursor: pointer;">
                <label class="form-check-label" for="is24Jam" style="font-size: 12px; font-weight: 600; color: #4f46e5; margin-left: 8px;">Buka 24 Jam</label>
            </div>
        </div>

        <div id="jamRange" class="jam-container">
            <div class="jam-box">
                <div class="time-input-wrapper">
                    <input type="time" name="jam_buka" id="jamBuka">
                </div>
                <div style="text-align: center; font-size: 10px; color: #94a3b8; margin-top: 5px; font-weight: 700;">BUKA</div>
            </div>

            <div class="jam-separator">
                <i class="ri-arrow-right-line"></i>
            </div>

            <div class="jam-box">
                <div class="time-input-wrapper">
                    <input type="time" name="jam_tutup" id="jamTutup">
                </div>
                <div style="text-align: center; font-size: 10px; color: #94a3b8; margin-top: 5px; font-weight: 700;">TUTUP</div>
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
                <span class="prev-val" id="pvAlamat" style="color: #64748b; font-weight: 500;">-</span>
            </div>
        </div>
    </div>

</div>

                <div class="wizard-actions">
                    <button type="button" class="btn-prev" id="prevBtn" style="display: none;">Kembali</button>
                    <button type="button" class="btn-next" id="nextBtn">Lanjut <i class="ri-arrow-right-line"></i></button>
                    <button type="submit" class="btn-next" id="submitBtn" style="display: none; background: #10b981;">Simpan Data <i class="ri-check-line"></i></button>
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- <script src="{{ asset('js/admin/map.js') }}"></script> --}}
    <script src="{{ asset('js/admin/jam-operasional.js') }}"></script>
    <script src="{{ asset('js/admin/preview.js') }}"></script>
    <script src="{{ asset('js/admin/wizard.js') }}"></script>
    
    <script>
        function previewFile(input) {
            const name = input.files[0] ? input.files[0].name : '';
            document.getElementById('filePreviewName').innerText = name ? '📁 ' + name : '';
        }
    </script>
    <script>
    // Variabel Global
    var map = null;
    var marker = null;

    // Fungsi Inisialisasi Peta
    window.initMap = function() {
        if (map !== null && map !== undefined) {
            map.off();
            map.remove();
        }

        var defaultLat = -3.440974;
        var defaultLng = 114.833500;

        var latVal = document.getElementById('latitude').value;
        var lngVal = document.getElementById('longitude').value;
        if(latVal && lngVal) {
            defaultLat = latVal;
            defaultLng = lngVal;
        }

        map = L.map('map').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);

        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        marker.on('dragend', function (e) {
            var pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            map.panTo(e.latlng);
        });
        
        updateInputs(defaultLat, defaultLng);
    }

    window.updateMapFromInput = function() {
        var latInput = document.getElementById('latitude').value;
        var lngInput = document.getElementById('longitude').value;

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
        document.getElementById('latitude').value = parseFloat(lat).toFixed(7);
        document.getElementById('longitude').value = parseFloat(lng).toFixed(7);
    }

    // --- LOGIKA PENCARIAN & LOADING ---
    var btnCari = document.getElementById('btnCariOtomatis');
    if(btnCari){
        btnCari.addEventListener('click', function() {
            var keyword = document.getElementById('namaTempat').value;
            if (!keyword) keyword = prompt("Masukkan nama lokasi:");

            if (keyword) {
                // 1. UBAH TOMBOL JADI LOADING
                var originalText = btnCari.innerHTML;
                btnCari.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Sedang Mencari Lokasi...';
                btnCari.disabled = true;
                btnCari.style.opacity = "0.7";

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${keyword}&limit=1`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.length > 0) {
                            var lat = data[0].lat;
                            var lon = data[0].lon;
                            var alamatLengkap = data[0].display_name; // AMBIL ALAMAT

                            // Pindah Marker
                            marker.setLatLng([lat, lon]);
                            map.setView([lat, lon], 16);
                            updateInputs(lat, lon);

                            // ISI INPUT ALAMAT OTOMATIS
                            var alamatField = document.getElementById('alamatInp');
                            if(alamatField) {
                                alamatField.value = alamatLengkap; // Isi textarea
                                // Update Preview Box Realtime
                                if(window.updatePreview) window.updatePreview();
                            }

                        } else {
                            alert("Lokasi tidak ditemukan! Coba nama lain.");
                        }
                    })
                    .catch(err => {
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
</script>

</body>
</html>