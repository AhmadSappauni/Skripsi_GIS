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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        
        /* --- 1. PERBAIKAN HEADER (MATIKAN STICKY) --- */
        .top-header { 
            position: relative !important; /* Paksa agar tidak sticky */
            top: auto !important; 
            background: transparent; 
            padding: 30px 40px 10px 40px; 
            border: none; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            z-index: 1;
        }
        
        .page-title { font-size: 24px; font-weight: 800; color: #111827; letter-spacing: -0.5px; }
        .back-btn { text-decoration: none; color: #6b7280; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .back-btn:hover { color: #4f46e5; transform: translateX(-3px); }

        /* WIZARD STEPS */
        .wizard-steps { display: flex; justify-content: center; gap: 40px; margin-bottom: 40px; position: relative; margin-top: 20px; }
        .wizard-steps::before {
            content: ''; position: absolute; top: 20px; left: 20%; right: 20%; height: 2px;
            background: #e5e7eb; z-index: 0;
        }
        .step-item { position: relative; z-index: 1; text-align: center; width: 100px; opacity: 0.6; transition: 0.3s; }
        .step-item.active { opacity: 1; }
        .step-item.active .step-icon { background: #4f46e5; color: white; box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.2); border-color: #4f46e5; }
        .step-item.completed .step-icon { background: #10b981; color: white; border-color: #10b981; }
        
        .step-icon {
            width: 40px; height: 40px; background: white; border: 2px solid #d1d5db; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto;
            font-size: 18px; color: #6b7280; transition: 0.3s; font-weight: 700;
        }
        .step-label { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #374151; letter-spacing: 0.5px; }

        /* FORM CARD */
        .form-card {
            background: white; border-radius: 24px; padding: 40px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); border: 1px solid #f3f4f6;
            max-width: 900px; margin: 0 auto 50px auto;
        }

        /* INPUTS */
        .form-group { margin-bottom: 25px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-input {
            width: 100%; padding: 14px 18px; border-radius: 14px; border: 1px solid #e5e7eb;
            font-size: 14px; color: #111827; background: #f9fafb; outline: none; transition: 0.2s; font-weight: 500;
        }
        .form-input:focus { border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* KATEGORI */
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; }
        .cat-input { display: none; }
        .cat-box {
            border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px 10px; text-align: center;
            cursor: pointer; transition: 0.2s; background: white; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .cat-box:hover { border-color: #4f46e5; transform: translateY(-3px); }
        .cat-input:checked + .cat-box {
            border-color: #4f46e5; background: #eef2ff; color: #4f46e5;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.15);
        }
        .cat-icon { width: 40px; height: 40px; object-fit: contain; margin-bottom: 10px; }
        .cat-name { font-size: 12px; font-weight: 700; display: block; }

        /* --- 2. PERBAIKAN TOMBOL HARI BUKA (GRID RAPI) --- */
        .checkbox-group { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); /* Grid otomatis */
            gap: 10px; 
        }
        .check-chip input { display: none; }
        .check-label {
            display: flex; align-items: center; justify-content: center;
            padding: 10px; background: white; border: 1px solid #e5e7eb; border-radius: 12px;
            font-size: 13px; font-weight: 600; color: #6b7280; cursor: pointer; transition: 0.2s;
            text-align: center;
        }
        .check-chip input:checked + .check-label { 
            background: #4f46e5; color: white; border-color: #4f46e5; 
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        /* UPLOAD & MAP */
        .upload-area {
            border: 2px dashed #d1d5db; border-radius: 20px; padding: 40px; text-align: center;
            background: #f9fafb; transition: 0.2s; cursor: pointer;
        }
        .upload-area:hover { border-color: #4f46e5; background: #eef2ff; }
        
        /* Map dengan background abu-abu muda biar gak kaget kalau loading */
        #map { height: 350px; border-radius: 20px; width: 100%; border: 1px solid #e5e7eb; z-index: 1; background: #f3f4f6; }

        /* BUTTONS */
        .wizard-actions { display: flex; justify-content: space-between; margin-top: 40px; border-top: 1px solid #f3f4f6; padding-top: 30px; }
        .btn-prev {
            background: white; border: 1px solid #e5e7eb; color: #6b7280; padding: 12px 24px;
            border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-prev:hover { background: #f9fafb; color: #111827; }
        .btn-next {
            background: #111827; color: white; border: none; padding: 12px 30px;
            border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 8px;
        }
        .btn-next:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -8px rgba(0,0,0,0.3); }

        /* ANIMASI WIZARD */
        .wizard-content { display: none; animation: fadeIn 0.5s ease; }
        .wizard-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* PREVIEW */
        .preview-box { background: #f8fafc; border-radius: 16px; padding: 20px; border: 1px solid #e5e7eb; margin-top: 20px; }
        .prev-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; }
        .prev-label { color: #6b7280; font-weight: 600; }
        .prev-val { color: #111827; font-weight: 700; }

        /* --- PREVIEW BOX PREMIUM --- */
        .preview-box { 
            background: #ffffff; 
            border-radius: 20px; 
            padding: 30px; 
            border: 1px solid #e2e8f0; 
            margin-top: 30px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); /* Shadow lembut & mahal */
            position: relative;
            overflow: hidden;
        }

        /* Garis aksen di kiri biar estetik */
        .preview-box::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 6px;
            background: linear-gradient(to bottom, #4f46e5, #818cf8);
        }

        .prev-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;
        }
        .prev-title { font-size: 15px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Grid Layout untuk Item Preview */
        .prev-grid {
            display: grid; 
            gap: 15px;
        }
        .prev-item {
            display: grid; 
            grid-template-columns: 140px 1fr; /* Kolom Label fix 140px, Sisanya untuk Isi */
            align-items: flex-start; /* Supaya kalau alamat panjang, label tetap di atas */
            font-size: 14px;
        }

        .prev-label { 
            color: #94a3b8; 
            font-weight: 600; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            margin-top: 3px; /* Penyesuaian sejajar mata */
        }

        .prev-val { 
            color: #334155; 
            font-weight: 700; 
            line-height: 1.5; 
        }

        /* Khusus highlight harga & kategori */
        .prev-tag {
            display: inline-block; padding: 4px 10px; border-radius: 6px;
            font-size: 12px; font-weight: 700; background: #f1f5f9; color: #475569;
        }

        /* --- PREMIUM CATEGORY GRID --- */
        .cat-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); /* Kartu lebih lebar */
            gap: 20px; 
        }

        .cat-box {
            position: relative;
            background: #ffffff;
            border: 2px solid #f1f5f9; /* Border abu sangat halus */
            border-radius: 24px; /* Sudut sangat bulat (Modern) */
            padding: 25px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efek pegas (bouncy) */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        /* Hover Effect: Naik & Bayangan Halus */
        .cat-box:hover {
            transform: translateY(-8px);
            border-color: #cbd5e1;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
        }

        /* STATE: SELECTED (SAAT DIPILIH) */
        .cat-input:checked + .cat-box {
            border-color: #6366f1; /* Warna Primary */
            background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%); /* Gradasi tipis */
            box-shadow: 0 15px 35px -5px rgba(99, 102, 241, 0.3); /* GLOW UNGU MAHAL */
            transform: translateY(-5px) scale(1.02);
        }

        /* Icon Gambar */
        .cat-icon {
            width: 52px;
            height: 52px;
            object-fit: contain;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.05)); /* Bayangan ikon */
        }

        /* Icon jadi besar sedikit saat dipilih */
        .cat-input:checked + .cat-box .cat-icon {
            transform: scale(1.15) rotate(-5deg); /* Sedikit miring biar dinamis */
            filter: drop-shadow(0 10px 15px rgba(99, 102, 241, 0.2));
        }

        /* Teks Label */
        .cat-name {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            transition: color 0.2s;
        }

        .cat-input:checked + .cat-box .cat-name {
            color: #4f46e5;
        }

        /* BADGE CENTANG (Lingkaran Centang di Pojok) */
        .cat-check {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 24px;
            height: 24px;
            background: #4f46e5;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 14px;
            opacity: 0; transform: scale(0); /* Tersembunyi defaultnya */
            transition: all 0.3s cubic-bezier(0.5, 1.6, 0.4, 0.7);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.4);
        }

        /* Munculkan Badge saat dipilih */
        .cat-input:checked + .cat-box .cat-check {
            opacity: 1; transform: scale(1);
        }

        /* --- CUSTOM SMART MODAL (GLASSMORPHISM) --- */
        .smart-modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(17, 24, 39, 0.6); /* Gelap transparan */
            backdrop-filter: blur(12px); /* EFEK KACA BURAM (MAHAL) */
            z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: all 0.3s ease;
        }

        .smart-modal-overlay.open { opacity: 1; pointer-events: auto; }

        .smart-modal-card {
            background: white; width: 90%; max-width: 420px;
            border-radius: 28px; padding: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            transform: scale(0.9) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); /* Efek Bouncy/Kenyal */
        }

        .smart-modal-overlay.open .smart-modal-card {
            transform: scale(1) translateY(0);
        }

        .modal-icon-box {
            width: 70px; height: 70px; border-radius: 50%;
            margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center;
            font-size: 32px; position: relative;
        }

        /* Variasi Warna Icon */
        .icon-warning { background: #fef3c7; color: #d97706; }
        .icon-success { background: #d1fae5; color: #059669; }
        .icon-question { background: #e0e7ff; color: #4f46e5; }

        /* Efek Cincin Berdenyut di Icon */
        .modal-icon-box::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 50%; border: 2px solid currentColor; opacity: 0.2;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.5); opacity: 0; } }

        .modal-title { font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .modal-desc { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 25px; }

        .modal-actions { display: flex; gap: 10px; justify-content: center; }

        .btn-modal {
            padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; border: none; transition: 0.2s; flex: 1;
        }
        .btn-primary-modal { background: #4f46e5; color: white; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4); }
        .btn-primary-modal:hover { transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.5); }

        .btn-secondary-modal { background: #f3f4f6; color: #4b5563; }
        .btn-secondary-modal:hover { background: #e5e7eb; }
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
                    
                    <div class="form-group">
                        <label class="form-label">Lokasi di Peta</label>
                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <button type="button" id="btnCariLokasi" class="btn-prev" style="width: 100%; display: flex; justify-content: center; gap: 5px;">
                                <i class="ri-search-line"></i> Cari Lokasi Otomatis
                            </button>
                        </div>
                        <div id="map"></div>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamatInp" class="form-input" rows="2"></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Jam Operasional</label>
                            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; cursor: pointer;">
                                <input type="checkbox" id="is24Jam" name="is_24_jam">
                                <span style="font-size: 13px; font-weight: 600; color: #4b5563;">Buka 24 Jam</span>
                            </label>
                            <div id="jamRange" style="display: flex; align-items: center; gap: 10px;">
                                <input type="time" name="jam_buka" id="jamBuka" class="form-input">
                                <span style="color: #9ca3af;">—</span>
                                <input type="time" name="jam_tutup" id="jamTutup" class="form-input">
                            </div>
                            <input type="hidden" name="jam_operasional" id="jamOperasional">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hari Buka</label>
                            <div class="checkbox-group">
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <label class="check-chip">
                                        <input type="checkbox" name="hari_buka[]" value="{{ $hari }}" checked>
                                        <span class="check-label">{{ $hari }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div id="previewBox" class="preview-box" style="display: none;">
                        <div class="prev-header">
                            <i class="ri-article-line" style="font-size: 20px; color: #4f46e5;"></i>
                            <span class="prev-title">Ringkasan Data Wisata</span>
                        </div>

                        <div class="prev-grid">
                            <div class="prev-item">
                                <span class="prev-label">Nama Tempat</span>
                                <span class="prev-val" id="pvNama" style="font-size: 16px; color: #1e293b;">-</span>
                            </div>

                            <div class="prev-item">
                                <span class="prev-label">Kategori</span>
                                <div><span class="prev-val prev-tag" id="pvKategori">-</span></div>
                            </div>

                            <div class="prev-item">
                                <span class="prev-label">Harga Tiket</span>
                                <span class="prev-val" id="pvHarga" style="color: #059669;">-</span>
                            </div>

                            <div class="prev-item">
                                <span class="prev-label">Jam Operasional</span>
                                <span class="prev-val" id="pvJam">-</span>
                            </div>

                            <div class="prev-item">
                                <span class="prev-label">Alamat Lengkap</span>
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
    <script src="{{ asset('js/admin/map.js') }}"></script>
    <script src="{{ asset('js/admin/jam-operasional.js') }}"></script>
    <script src="{{ asset('js/admin/preview.js') }}"></script>
    <script src="{{ asset('js/admin/wizard.js') }}"></script>
    
    <script>
        function previewFile(input) {
            const name = input.files[0] ? input.files[0].name : '';
            document.getElementById('filePreviewName').innerText = name ? '📁 ' + name : '';
        }
    </script>

</body>
</html>