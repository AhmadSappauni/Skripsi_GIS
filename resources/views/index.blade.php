<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Itinerary Banjarbakula</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* Style Modal Konfirmasi */
        #custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Latar belakang gelap transparan */
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
            /* Efek blur estetik */
        }

        .cm-btn {
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            flex: 1;
            transition: 0.2s;
        }

        .cm-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cm-btn-cancel {
            background: #f1f5f9;
            color: #64748b;
        }

        .cm-btn-confirm {
            background: #ef4444;
            color: white;
        }

        /* --- CSS TOOLTIP "!" --- */
        .label-with-tooltip {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            /* Jarak ke input di bawahnya */
        }

        .info-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 1px solid #94a3b8;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            margin-left: 6px;
            cursor: help;
            position: relative;
            transition: all 0.2s ease;
        }

        .info-icon:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            background: #eef2ff;
        }

        /* Kotak Penjelasan (Hidden by default) */
        .info-icon::after {
            content: attr(data-tooltip);
            /* Mengambil teks dari HTML */
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            width: 160px;
            background: #1e293b;
            color: white;
            text-align: center;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.4;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        /* Panah Kecil di Bawah Kotak */
        .info-icon::before {
            content: '';
            position: absolute;
            bottom: 115%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            border-width: 5px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 100;
        }

        /* Efek Muncul saat Hover */
        .info-icon:hover::after,
        .info-icon:hover::before {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* --- MODERN INPUT GROUP --- */
        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-modern i {
            position: absolute;
            left: 12px;
            color: #64748b;
            font-size: 14px;
            z-index: 10;
        }

        .input-group-modern .form-input,
        .input-group-modern .form-select {
            padding-left: 35px !important;
            /* Memberi ruang untuk ikon */
            transition: all 0.2s;
        }

        .input-group-modern .form-input:focus,
        .input-group-modern .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background: white;
        }

        /* Section Title Kecil */
        .filter-section-title {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        /* --- DIRECTORY MODAL PREMIUM --- */
        .dir-action-bar {
            padding: 15px 25px;
            background: white;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 12px;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* Search Bar Modern (Pill Shape) */
        .search-pill {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            /* Bulat lonjong */
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .search-pill:focus-within {
            background: white;
            border-color: #6366f1;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        }

        .search-pill input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 14px;
            color: #334155;
        }

        /* Tombol Filter Toggle */
        .btn-filter-toggle {
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 18px;
        }

        .btn-filter-toggle:hover,
        .btn-filter-toggle.active {
            background: #6366f1;
            color: white;
            border-color: #6366f1;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
        }

        /* Panel Filter Tersembunyi */
        .hidden-filter-panel {
            max-height: 0;
            overflow: hidden;
            background: #f8fafc;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid #e2e8f0;
        }

        .hidden-filter-panel.show {
            max-height: 200px;
            /* Cukup untuk menampung dropdown */
        }

        .filter-grid {
            padding: 20px 25px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        /* Kartu Wisata (Grid) */
        .dir-grid {
            padding: 25px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            background: #ffffff;
        }

        /* --- STYLE UNTUK KARTU DIREKTORI (YANG HILANG) --- */

        /* Container Grid */
        #directoryList {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            /* Responsif */
            gap: 20px;
            padding: 25px;
            background: #f8fafc;
        }

        /* Kartu Wisata */
        .dir-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
        }

        .dir-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: #c7d2fe;
        }

        /* Pembungkus Gambar (Agar Ukuran Sama) */
        .dir-card-img-wrapper {
            width: 100%;
            height: 140px;
            /* Tinggi fix agar rapi */
            background: #f1f5f9;
            position: relative;
        }

        .dir-card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Gambar tidak penyok */
        }

        /* Body Kartu */
        .dir-card-body {
            padding: 12px 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .dir-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dir-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            margin-top: 5px;
        }

        .dir-cat {
            background: #eef2ff;
            color: #4f46e5;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        .dir-price {
            color: #d97706;
            /* Warna oranye untuk harga */
            font-weight: 700;
        }

        /* Pesan Jika Kosong */
        .directory-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-size: 14px;
        }
    </style>

</head>

<body>

    <div id="map"></div>

    <div id="loadingOverlay">
        <div class="spinner"></div>
        <h3 style="color: var(--primary); margin-top: 20px; font-weight: 800;">Sistem Bekerja...</h3>
        <p style="color: var(--text-light); font-size: 14px;">Mencari rute terbaik untuk liburanmu 🤖</p>
    </div>

    <button id="btnShowSidebar" onclick="toggleSidebar()"
        style="display: none; position: absolute; top: 20px; left: 20px; z-index: 1000; background: white; border: none; padding: 12px 18px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer; font-weight: 700; color: #1e293b; align-items: center; gap: 8px;">
        <span style="font-size: 18px;">☰</span> Menu
    </button>

    <div class="floating-sidebar" id="mainSidebar">

        <div class="sidebar-header"
            style="flex-shrink: 0; background: white; z-index: 20; border-bottom: 1px solid #f1f5f9; padding: 25px 25px 20px 25px; display: flex; justify-content: space-between; align-items: center;">
            <div onclick="openInfoModal()" style="cursor: pointer; transition: 0.2s;"
                onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'" title="Tentang Aplikasi">
                <h2 style="margin: 0; line-height: 1.1; font-size: 22px; color: #1e293b;">Smart <span
                        style="color: #4f46e5;">Itinerary</span></h2>
                <p style="font-size: 11px; color: #64748b; margin-top: 4px; font-weight: 500;">Banjarbakula Region</p>
            </div>

            <div style="display: flex; gap: 8px;">
                <button onclick="toggleFilterMenu()" class="btn-info-header" title="Filter Pencarian"
                    style="background: #eef2ff; color: #4f46e5;">
                    ⚙️
                </button>
                <button onclick="openDirectoryModal()" class="btn-info-header" title="Direktori Wisata"
                    style="background: #f1f5f9; color: #334155;">
                    📂
                </button>
                <button onclick="toggleSidebar()" class="btn-info-header" title="Tutup"
                    style="background: #fef2f2; color: #ef4444;">
                    ✕
                </button>
            </div>
        </div>

        <div class="sidebar-scroll-area" style="flex-grow: 1; overflow-y: auto; padding: 25px;">

            <form action="{{ route('app.peta') }}" method="GET" onsubmit="return validateSearch()"
                style="margin-bottom: 25px;">
                <input type="hidden" name="action" value="cari_rute">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="form-label"
                        style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Titik
                        Keberangkatan</label>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button type="button" onclick="getLocation()" class="btn btn-outline"
                            style="flex: 1; padding: 12px; border: 1px dashed #4f46e5; color: #4f46e5; background: #eef2ff; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <span style="font-size: 16px;">📍</span> Deteksi Lokasi Saya
                        </button>
                    </div>

                    <p id="statusLokasi"
                        style="font-size: 11px; margin-top: 8px; text-align: center; font-weight: 600; color: {{ request('lat') ? '#10b981' : '#ef4444' }}">
                        {{ request('lat') ? '✅ Lokasi Berhasil Terkunci' : '⚠️ Lokasi belum dideteksi' }}
                    </p>
                    <input type="hidden" name="lat" id="inputLat" value="{{ request('lat') }}">
                    <input type="hidden" name="long" id="inputLong" value="{{ request('long') }}">
                </div>

                <div id="menuPilihan"
                    style="display: none; animation: slideDown 0.3s ease-out; margin-bottom: 15px; background: white; border: 1px solid #e2e8f0; padding: 20px; border-radius: 16px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);">

                    <div
                        style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">
                        <div
                            style="font-size: 12px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">
                            Filter Cerdas
                        </div>
                        <div
                            style="font-size: 10px; color: #64748b; background:#f1f5f9; padding:2px 8px; border-radius:4px;">
                            Greedy Config
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <div class="label-with-tooltip">
                            <label class="form-label" style="margin-bottom: 0;">Kategori Wisata</label>
                            <span class="info-icon" data-tooltip="Pilih jenis wisata (Alam, Religi, dll)">!</span>
                        </div>
                        <div class="custom-dropdown" id="kategoriDropdown">
                            <div class="dropdown-selected" onclick="toggleDropdown()">
                                <div class="selected-content">
                                    @php
                                        $icons = [
                                            'Alam' => 'alam.png',
                                            'Religi' => 'religi.png',
                                            'Kuliner' => 'kuliner.png',
                                            'Belanja' => 'belanja.png',
                                            'Budaya' => 'budaya.png',
                                            'Rekreasi' => 'rekreasi.png',
                                            'Agro' => 'agro.png',
                                            'Edukasi' => 'edukasi.png',
                                        ];
                                        $reqKat = request('kategori');
                                        $currentIcon = $icons[$reqKat] ?? 'default.png';
                                        $currentLabel = $reqKat ?: 'Semua Kategori';
                                    @endphp
                                    <img src="{{ asset('icons/' . $currentIcon) }}" id="displayIcon"
                                        style="width: 20px; height: 20px; object-fit: contain;">
                                    <span id="displayText" style="font-weight: 600;">{{ $currentLabel }}</span>
                                </div>
                                <span class="dropdown-arrow">▼</span>
                            </div>
                            <div class="dropdown-options">
                                <label class="dropdown-item" onclick="updateDisplay('Semua Kategori', 'default.png')">
                                    <input type="radio" name="kategori" value=""
                                        {{ request('kategori') == '' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/default.png') }}"> <span>Semua Kategori</span>
                                </label>
                                @foreach ($icons as $key => $ico)
                                    <label class="dropdown-item"
                                        onclick="updateDisplay('{{ $key }}', '{{ $ico }}')">
                                        <input type="radio" name="kategori" value="{{ $key }}"
                                            {{ request('kategori') == $key ? 'checked' : '' }}>
                                        <img src="{{ asset('icons/' . $ico) }}"> <span>{{ $key }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <div class="label-with-tooltip">
                                <label class="form-label" style="margin-bottom: 0;">Wilayah</label>
                            </div>
                            <div class="input-group-modern">
                                <i class="ri-map-pin-2-line"></i>
                                <select name="wilayah" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach (['Banjarmasin', 'Banjarbaru', 'Martapura', 'Barito Kuala', 'Tanah Laut'] as $w)
                                        <option value="{{ $w }}"
                                            {{ request('wilayah') == $w ? 'selected' : '' }}>
                                            {{ $w == 'Martapura' ? 'Kab. Banjar' : $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="label-with-tooltip">
                                <label class="form-label" style="margin-bottom: 0;">Hari</label>
                            </div>
                            <div class="input-group-modern">
                                <i class="ri-calendar-event-line"></i>
                                <select name="hari" class="form-select">
                                    <option value="">Bebas</option>
                                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                                        <option value="{{ $h }}"
                                            {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section-title">Parameter Algoritma</div>

                    <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #f1f5f9;">

                        <div style="margin-bottom: 12px;">
                            <div class="label-with-tooltip">
                                <label class="form-label"
                                    style="margin-bottom: 0; color: #4f46e5; font-weight: 700;">Maksimal Budget</label>
                                <span class="info-icon"
                                    data-tooltip="Algoritma akan mencari kombinasi wisata termurah sesuai batas ini">!</span>
                            </div>
                            <div class="input-group-modern">
                                <i class="ri-money-dollar-circle-line" style="color: #4f46e5;"></i>
                                <input type="number" name="budget" class="form-input"
                                    value="{{ request('budget', 100000) }}" placeholder="Rp"
                                    style="background: white; border-color: #c7d2fe; font-weight: 600; color: #1e293b;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">

                            <div>
                                <div class="label-with-tooltip">
                                    <label class="form-label" style="margin-bottom: 0;">Radius (KM)</label>
                                    <span class="info-icon"
                                        data-tooltip="Jarak maksimal pencarian wisata alternatif">!</span>
                                </div>
                                <div class="input-group-modern">
                                    <i class="ri-radar-line"></i>
                                    <input type="number" id="inputRadius" class="form-input" value="10"
                                        min="1" max="100" placeholder="KM">
                                </div>
                            </div>

                            <div>
                                <div class="label-with-tooltip">
                                    <label class="form-label" style="margin-bottom: 0;">Max Tujuan</label>
                                    <span class="info-icon"
                                        data-tooltip="Berapa banyak tempat wisata dalam satu rute?">!</span>
                                </div>
                                <div class="input-group-modern">
                                    <i class="ri-list-check"></i>
                                    <select name="limit" class="form-select">
                                        <option value="100">Auto</option>
                                        <option value="2" {{ request('limit') == '2' ? 'selected' : '' }}>2
                                        </option>
                                        <option value="3" {{ request('limit') == '3' ? 'selected' : '' }}>3
                                        </option>
                                        <option value="4" {{ request('limit') == '4' ? 'selected' : '' }}>4
                                        </option>
                                        <option value="5" {{ request('limit') == '5' ? 'selected' : '' }}>5
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width: 100%; padding: 14px; font-weight: 700; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);">
                    🚀 Cari Rute Cerdas
                </button>
            </form>

            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 25px 0;">

            @if (isset($hasil) && count($hasil) > 0)
                <script>
                    localStorage.setItem('lastSearchUrl', window.location.href);
                </script>

                <div class="result-summary"
                    style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 20px; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; box-shadow: 0 10px 25px -5px rgba(30, 41, 59, 0.3);">
                    <div>
                        <div
                            style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                            Total Estimasi</div>
                        <div style="font-size: 20px; font-weight: 800;">Rp
                            {{ number_format($total_biaya, 0, ',', '.') }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; font-weight: 600;">Sisa
                            Budget</div>
                        <div style="font-size: 14px; font-weight: 700; color: #34d399;">Rp
                            {{ number_format($sisa_budget, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div
                    style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">
                    Rute Rekomendasi</div>

                <ul class="wisata-list" style="padding: 0; list-style: none;">
                    @foreach ($hasil as $index => $wisata)
                        <li class="wisata-item"
                            style="background: white; border: 1px solid #f1f5f9; border-radius: 16px; padding: 15px; margin-bottom: 15px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);"
                            onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#cbd5e1';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#f1f5f9';"
                            onclick="focusOnLocation({{ $wisata->latitude }}, {{ $wisata->longitude }}, {{ $wisata->id }}); openDetailPanelById({{ $wisata->id }});">

                            <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                                <span
                                    style="background: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">#{{ $index + 1 }}</span>
                                @if ($index == 0)
                                    <span class="badge-clean primary" style="background: #eef2ff; color: #4f46e5;">📍
                                        Terdekat</span>
                                @endif
                                <span class="badge-clean success" style="background: #ecfdf5; color: #059669;">💰 Pas
                                    Budget</span>
                            </div>

                            <div style="display: flex; gap: 15px; align-items: center;">
                                <img src="{{ Str::startsWith($wisata->gambar, ['http', 'data:']) ? $wisata->gambar : asset( $wisata->gambar) }}"
                                    class="wisata-img"
                                    style="width: 60px; height: 60px; border-radius: 12px; object-fit: cover; flex-shrink: 0; background: #f1f5f9;"
                                    onerror="this.onerror=null; this.src='https://placehold.co/400x400/e2e8f0/64748b?text=IMG';">

                                <div class="wisata-info" style="flex: 1; min-width: 0;">
                                    <h4
                                        style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $wisata->nama_tempat }}
                                    </h4>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #64748b;">
                                        <span style="display: flex; align-items: center; gap: 4px;"><i
                                                class="ri-pin-distance-line"></i> {{ $wisata->jarak_km }} KM</span>
                                        <span style="font-weight: 700; color: #d97706;">
                                            {{ $wisata->harga_tiket == 0 ? 'Gratis' : 'Rp ' . number_format($wisata->harga_tiket, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div style="color: #cbd5e1; font-size: 18px;">➝</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @elseif(isset($hasil) && count($hasil) == 0)
                <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                    <div style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;">🤔</div>
                    <h4 style="font-size: 16px; margin-bottom: 8px; color: #334155; font-weight: 700;">Rute Tidak
                        Ditemukan</h4>
                    <p style="font-size: 12px; line-height: 1.5;">Maaf, tidak ada wisata yang cocok dengan filter dan
                        budget kamu. Coba naikkan budget atau ubah kategori.</p>
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                    <img src="https://cdn-icons-png.flaticon.com/512/854/854878.png" width="80"
                        style="opacity: 0.8; margin-bottom: 20px; filter: grayscale(100%);">
                    <h4 style="font-size: 16px; margin-bottom: 8px; color: #334155; font-weight: 700;">Mulai
                        Petualanganmu</h4>
                    <p style="font-size: 12px; line-height: 1.5;">Klik tombol <b>"Deteksi Lokasi"</b> di atas, lalu
                        atur budget liburanmu untuk mendapatkan rute terbaik.</p>
                </div>
            @endif

            <div
                style="text-align: center; margin-top: 40px; margin-bottom: 20px; font-size: 10px; color: #cbd5e1; font-weight: 500;">
                Smart Itinerary Banjarbakula &copy; {{ date('Y') }}
            </div>
        </div>
    </div>

    <div id="directoryModal" class="modal-overlay" style="display: none; z-index: 9999;">
        <div class="modal-content"
            style="background: white; width: 95%; max-width: 900px; height: 90vh; border-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">

            <div
                style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: white;">
                <div>
                    <h3 style="margin: 0; font-size: 20px; color: #1e293b; font-weight: 800;">Jelajahi Wisata</h3>
                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">Temukan destinasi terbaik di
                        Banjarbakula</p>
                </div>
                <button onclick="window.closeDirectoryModal()"
                    style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e2e8f0; background: white; color: #64748b; cursor: pointer; display:flex; align-items:center; justify-content:center; transition:0.2s;">✕</button>
            </div>

            <div class="dir-action-bar">
                <div class="search-pill">
                    <i class="ri-search-line" style="color: #94a3b8;"></i>
                    <input type="text" id="dirSearchInput" placeholder="Ketik nama tempat wisata..."
                        onkeyup="applyDirectoryFilter()">
                </div>
                <button id="btnDirFilter" class="btn-filter-toggle" onclick="toggleDirectoryFilters()"
                    title="Filter Lanjutan">
                    <i class="ri-equalizer-line"></i>
                </button>
            </div>

            <div id="dirFilterPanel" class="hidden-filter-panel">
                <div class="filter-grid">
                    <div>
                        <label
                            style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">WILAYAH</label>
                        <select id="dirRegionSelect" class="form-select" onchange="applyDirectoryFilter()">
                            <option value="">Semua Wilayah</option>
                            <option value="Banjarmasin">Banjarmasin</option>
                            <option value="Banjarbaru">Banjarbaru</option>
                            <option value="Martapura">Kab. Banjar</option>
                            <option value="Barito Kuala">Barito Kuala</option>
                            <option value="Tanah Laut">Tanah Laut</option>
                        </select>
                    </div>
                    <div>
                        <label
                            style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">KATEGORI</label>
                        <select id="dirKategoriSelect" class="form-select" onchange="applyDirectoryFilter()">
                            <option value="">Semua Kategori</option>
                            <option value="Alam">Alam</option>
                            <option value="Religi">Religi</option>
                            <option value="Kuliner">Kuliner</option>
                            <option value="Belanja">Belanja</option>
                            <option value="Budaya">Budaya</option>
                            <option value="Rekreasi">Rekreasi</option>
                        </select>
                    </div>

                    <div>
                        <label
                            style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">BUDGET
                            MAX</label>
                        <select id="dirBudgetSelect" class="form-select" onchange="applyDirectoryFilter()">
                            <option value="">Semua Budget</option>
                            <option value="0">Gratis</option>
                            <option value="25000">
                                < 25rb</option>
                            <option value="50000">
                                < 50rb</option>
                            <option value="100000">
                                < 100rb</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="directoryList" style="flex: 1; overflow-y: auto;">
            </div>
        </div>
    </div>

    <div id="detailPanel" class="detail-panel">
        <div class="panel-image-wrapper">
            <div class="carousel-wrapper">
                <div id="carouselTrack" class="carousel-track"></div>
                <button class="carousel-btn left" onclick="prevImage()">‹</button>
                <button class="carousel-btn right" onclick="nextImage()">›</button>
                <div id="carouselDots" class="carousel-dots"></div>
            </div>
            <div class="panel-overlay-gradient"></div>
            <button class="panel-close" onclick="closeDetailPanel()">×</button>
            <span id="panelKategori" class="panel-badge">Kategori</span>
        </div>

        <div class="panel-body">
            <div class="panel-header">
                <h2 id="panelNama" class="panel-title">Nama Tempat</h2>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                    <p id="panelHarga" class="panel-price">Rp 0</p>
                    <div class="panel-rating">⭐ 4.8</div>
                </div>
                <p id="panelAlamat" class="panel-address">Alamat lengkap...</p>
            </div>

            <div class="panel-nav">
                <button onclick="prevWisata()" class="nav-btn">←</button>
                <span class="panel-nav-text">Navigasi Rute</span>
                <button onclick="nextWisata()" class="nav-btn">→</button>
            </div>

            <div class="panel-divider"></div>

            <div class="panel-actions">
                <button id="panelRuteBtn" class="btn-rute-hero"
                    onclick="alert('Fitur navigasi Google Maps akan dibuka...')">
                    <i class="ri-direction-fill"></i> Buka Google Maps
                </button>
            </div>
        </div>
    </div>

    <div id="infoModal" class="modal-overlay" style="display: none;">
        <div class="modal-content"
            style="background: white; width: 90%; max-width: 400px; border-radius: 24px; padding: 0; overflow: hidden;">
            <div style="background: #eef2ff; padding: 30px 20px; text-align: center;">
                <div
                    style="width: 80px; height: 80px; background: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 40px; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);">
                    🎓
                </div>
                <h3 style="margin: 15px 0 5px 0; color: #1e293b; font-weight: 800;">Smart Itinerary</h3>
                <p style="margin: 0; font-size: 12px; color: #6366f1; font-weight: 600;">Skripsi - Universitas Lambung
                    Mangkurat</p>
            </div>
            <div style="padding: 25px;">
                <h4 style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 10px;">Pengembang</h4>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 5px;"><b>Ahmad Sappauni</b></p>
                <p style="font-size: 12px; color: #94a3b8;">NIM. 2210131210010</p>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 10px;">Metode</h4>
                    <p style="font-size: 12px; color: #64748b; line-height: 1.6;">
                        Aplikasi ini menggunakan <b>Algoritma Greedy</b> untuk optimalisasi rute dan <b>Haversine
                            Formula</b> untuk perhitungan jarak akurat.
                    </p>
                </div>
                <button onclick="closeInfoModal()" class="btn btn-primary"
                    style="width: 100%; margin-top: 25px; padding: 12px; border-radius: 12px;">Tutup</button>
            </div>
        </div>
    </div>

    <div id="customToast" class="toast-notification">
        <div class="toast-icon">✨</div>
        <div class="toast-message">
            <h4 id="toastTitle">Berhasil!</h4>
            <p id="toastBody">Pesan notifikasi.</p>
        </div>
    </div>
    <div id="custom-modal-overlay" class="modal-overlay" style="display: none; z-index: 10000;">
        <div class="modal-content"
            style="background: white; width: 90%; max-width: 320px; border-radius: 20px; padding: 30px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        </div>
    </div>

    <!-- LEAFLET -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polylinedecorator@1.6.0/dist/leaflet.polylineDecorator.js"></script>

    <!-- DATA DARI BACKEND -->
    <script>
        window.wisataData = @json(isset($hasil) ? $hasil : null);
        window.wisataLainData = @json(isset($wisata_lain) ? $wisata_lain : null);
        window.allWisataData = @json($semua_wisata ?? []);
        window.realSisaBudget = @json(isset($sisa_budget) ? $sisa_budget : request('budget') ?? 0);
    </script>

    <!-- CORE & FEATURES -->
    <script src="{{ asset('js/core/state.js') }}"></script>
    <script src="{{ asset('js/core/utils.js') }}"></script>
    <script src="{{ asset('js/core/map-init.js') }}"></script>
    <script src="{{ asset('js/features/route.js') }}"></script>
    <script src="{{ asset('js/features/nearby.js') }}"></script>
    <script src="{{ asset('js/features/geojson.js') }}"></script>
    <script src="{{ asset('js/features/detail-panel.js') }}"></script>

    <!-- UI -->
    <script src="{{ asset('js/ui/toast.js') }}"></script>
    <script src="{{ asset('js/ui/modal.js') }}"></script>
    <script src="{{ asset('js/ui/directory-ui.js') }}"></script>

    <!-- OPTIONAL PAGE INIT -->
    <script src="{{ asset('js/ui/page-init.js') }}"></script>

</body>

</html>
