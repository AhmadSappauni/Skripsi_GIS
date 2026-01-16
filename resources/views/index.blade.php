<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Itinerary Banjarbakula</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
</head>
<body>

    <div id="map"></div>

    <div id="loadingOverlay">
        <div class="spinner"></div> 
        <h3 style="color: var(--primary); margin-top: 20px;">Sistem bekerja...</h3>
        <p style="color: var(--text-light);">Mencari rute terbaik untuk liburanmu 🤖</p>
    </div>

    <button id="btnShowSidebar" onclick="toggleSidebar()" 
        style="display: none; position: absolute; top: 20px; left: 20px; z-index: 1000; background: white; border: none; padding: 10px 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer; font-weight: bold; color: #334155;">
        ☰ Buka Menu
    </button>

    <div class="floating-sidebar" id="mainSidebar">
    
        <div class="sidebar-header" style="flex-shrink: 0; background: white; z-index: 20; border-bottom: 1px solid #f1f5f9; padding: 20px 20px 15px 20px; display: flex; justify-content: space-between; align-items: flex-start;">
    
            <div onclick="openInfoModal()" 
                style="cursor: pointer; transition: opacity 0.2s;" 
                onmouseover="this.style.opacity='0.7'" 
                onmouseout="this.style.opacity='1'"
                title="Klik untuk Info Aplikasi">
                
                <h2 style="margin: 0; line-height: 1.2;"> Smart <span>Itinerary</span></h2>
                <p style="font-size: 11px; color: #64748b; margin: 0;">Banjarbakula</p>
            </div>

            <div style="display: flex; gap: 5px;">
                
                <button onclick="toggleFilterMenu()" class="btn-info-header" title="Atur Budget & Kategori" style="background: #f1f5f9; color: #4f46e5; border-color: #c7d2fe;">
                    ⚙️
                </button>

                <button onclick="openDirectoryModal()" class="btn-info-header" title="Jelajahi Wisata" style="background: #f1f5f9; color: #334155;">
                    📂
                </button>

                <button onclick="toggleSidebar()" class="btn-info-header" title="Tutup Menu" style="background: #fee2e2; color: #ef4444;">
                    X
                </button>
            </div>
            
        </div>

        <div class="sidebar-scroll-area" style="flex-grow: 1; overflow-y: auto; padding: 20px;">
            
            <form action="{{ route('app.peta') }}" method="GET" onsubmit="return validateSearch()" style="margin-bottom: 20px;">
                <input type="hidden" name="action" value="cari_rute">
                
                <div class="form-group" style="margin-bottom: 10px;">
                    <label class="form-label" style="margin-bottom: 5px;">Titik Keberangkatan</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" onclick="getLocation()" class="btn btn-outline" style="padding: 10px;">
                            Deteksi Lokasi
                        </button>
                    </div>
                    <p id="statusLokasi" style="font-size: 10px; color: var(--primary); margin-top: 5px; text-align: right;">
                        {{ request('lat') ? '✅ Lokasi Terkunci' : 'Wajib dideteksi!' }}
                    </p>
                    <input type="hidden" name="lat" id="inputLat" value="{{ request('lat') }}">
                    <input type="hidden" name="long" id="inputLong" value="{{ request('long') }}">
                </div>

                <div id="menuPilihan" style="display: none; animation: slideDown 0.3s ease-out; margin-top: 10px; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #f1f5f9;">
                    
                    <div style="font-size: 10px; font-weight: 700; color: #6366f1; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">
                        Filter Pencarian
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label class="form-label">Kategori Wisata</label>
                        <div class="custom-dropdown" id="kategoriDropdown">
                            <div class="dropdown-selected" onclick="toggleDropdown()">
                                <div class="selected-content">
                                    @php
                                        $kategori = request('kategori');
                                        $icon = 'default.png';
                                        $label = 'Semua Kategori';
                                        if($kategori == 'Alam') { $icon = 'alam.png'; $label = 'Alam'; }
                                        if($kategori == 'Religi') { $icon = 'religi.png'; $label = 'Religi'; }
                                        if($kategori == 'Kuliner') { $icon = 'kuliner.png'; $label = 'Kuliner'; }
                                        if($kategori == 'Belanja') { $icon = 'belanja.png'; $label = 'Belanja'; }
                                        if($kategori == 'Budaya')   { $icon = 'budaya.png';   $label = 'Budaya'; }
                                        if($kategori == 'Rekreasi') { $icon = 'rekreasi.png'; $label = 'Rekreasi'; }
                                        if($kategori == 'Agro')     { $icon = 'agro.png';     $label = 'Agrowisata'; }
                                        if($kategori == 'Edukasi')  { $icon = 'edukasi.png';  $label = 'Edukasi'; }
                                    @endphp
                                    <img src="{{ asset('icons/'.$icon) }}" id="displayIcon">
                                    <span id="displayText">{{ $label }}</span>
                                </div>
                                <span class="dropdown-arrow">▼</span>
                            </div>
                            <div class="dropdown-options">
                                <label class="dropdown-item" onclick="updateDisplay('Semua Kategori', 'default.png')">
                                    <input type="radio" name="kategori" value="" {{ request('kategori') == '' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/default.png') }}"> <span>Semua Kategori</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Alam', 'alam.png')">
                                    <input type="radio" name="kategori" value="Alam" {{ request('kategori') == 'Alam' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/alam.png') }}"> <span>Alam</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Rekreasi', 'rekreasi.png')">
                                    <input type="radio" name="kategori" value="Rekreasi" {{ request('kategori') == 'Rekreasi' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/rekreasi.png') }}"> <span>Rekreasi</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Budaya', 'budaya.png')">
                                    <input type="radio" name="kategori" value="Budaya" {{ request('kategori') == 'Budaya' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/budaya.png') }}"> <span>Budaya</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Religi', 'religi.png')">
                                    <input type="radio" name="kategori" value="Religi" {{ request('kategori') == 'Religi' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/religi.png') }}"> <span>Religi</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Edukasi', 'edukasi.png')">
                                    <input type="radio" name="kategori" value="Edukasi" {{ request('kategori') == 'Edukasi' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/edukasi.png') }}"> <span>Edukasi</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Agrowisata', 'agro.png')">
                                    <input type="radio" name="kategori" value="Agro" {{ request('kategori') == 'Agro' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/agro.png') }}"> <span>Agrowisata</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Kuliner', 'kuliner.png')">
                                    <input type="radio" name="kategori" value="Kuliner" {{ request('kategori') == 'Kuliner' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/kuliner.png') }}"> <span>Kuliner</span>
                                </label>
                                <label class="dropdown-item" onclick="updateDisplay('Belanja', 'belanja.png')">
                                    <input type="radio" name="kategori" value="Belanja" {{ request('kategori') == 'Belanja' ? 'checked' : '' }}>
                                    <img src="{{ asset('icons/belanja.png') }}"> <span>Belanja</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                        <div>
                            <label class="form-label">Wilayah</label>
                            <select name="wilayah" class="form-select">
                                <option value="">Semua</option>
                                <option value="Banjarmasin" {{ request('wilayah') == 'Banjarmasin' ? 'selected' : '' }}>Banjarmasin</option>
                                <option value="Banjarbaru" {{ request('wilayah') == 'Banjarbaru' ? 'selected' : '' }}>Banjarbaru</option>
                                <option value="Martapura" {{ request('wilayah') == 'Martapura' ? 'selected' : '' }}>Kab. Banjar</option>
                                <option value="Barito Kuala" {{ request('wilayah') == 'Barito Kuala' ? 'selected' : '' }}>Barito Kuala</option>
                                <option value="Tanah Laut" {{ request('wilayah') == 'Tanah Laut' ? 'selected' : '' }}>Tanah Laut</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select">
                                <option value="">Bebas</option>
                                <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                <option value="Minggu" {{ request('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label class="form-label">Budget</label>
                            <input type="number" name="budget" class="form-input" value="{{ request('budget', 100000) }}" placeholder="Rp">
                        </div>
                        <div>
                            <label class="form-label">Limit</label>
                            <select name="limit" class="form-select">
                                <option value="100">Max (Auto)</option>
                                <option value="2" {{ request('limit') == '2' ? 'selected' : '' }}>2 Tempat</option>
                                <option value="3" {{ request('limit') == '3' ? 'selected' : '' }}>3 Tempat</option>
                                <option value="4" {{ request('limit') == '4' ? 'selected' : '' }}>4 Tempat</option>
                                <option value="5" {{ request('limit') == '5' ? 'selected' : '' }}>5 Tempat</option>
                            </select>
                        </div>
                    </div>
                </div> 
                <button type="submit" class="btn btn-primary" style="margin-top: 15px; padding: 12px;">
                    🚀 Cari Rute Cerdas
                </button>
            </form>

            <hr style="border: 0; border-top: 1px solid rgba(0,0,0,0.05); margin: 20px 0;">

            @if(isset($hasil) && count($hasil) > 0)
                <script>localStorage.setItem('lastSearchUrl', window.location.href);</script>
                
                <div class="result-summary" style="margin-bottom: 20px; animation: slideIn 0.5s ease-out;">
                    <div>
                        <div style="font-size: 10px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">Total Biaya</div>
                        <div style="font-size: 18px; font-weight: 800;">Rp {{ number_format($total_biaya) }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 10px; opacity: 0.8;">Sisa Budget</div>
                        <div style="font-size: 14px; font-weight: 700; color: #d1fae5;">Rp {{ number_format($sisa_budget) }}</div>
                    </div>
                </div>

                <ul class="wisata-list">
                    @foreach($hasil as $index => $wisata)
                    <li class="wisata-item"
                        onclick="focusOnLocation({{ $wisata->latitude }}, {{ $wisata->longitude }}, {{ $wisata->id }}); openDetailPanelById({{ $wisata->id }});"> 
                        
                        <div style="display: flex; gap: 5px;">
                            @if($index == 0)
                                <span class="badge-clean primary">📍 Terdekat</span>
                            @endif
                            <span class="badge-clean success">💰 Pas Budget</span>
                        </div>

                        <div style="display: flex; gap: 12px; align-items: center;">
                            
                            <img src="{{ Str::startsWith($wisata->gambar, 'http') ? $wisata->gambar : asset('storage/' . $wisata->gambar) }}" 
                                class="wisata-img" 
                                style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover; flex-shrink: 0;"
                                onerror="this.onerror=null; this.src='https://placehold.co/400x400/e2e8f0/64748b?text=No+Image';">
                            
                            <div class="wisata-info" style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0; font-size: 13px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $wisata->nama_tempat }}
                                </h4>
                                <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 11px; color: #64748b;">
                                    <span>{{ $wisata->jarak_km }} KM</span>
                                    <span style="font-weight: bold; color: #f59e0b;">
                                        {{ $wisata->harga_tiket == 0 ? 'Gratis' : number_format($wisata->harga_tiket) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div style="color: #cbd5e1; font-size: 16px;">➝</div>
                        </div>
                    </li>
                    @endforeach
                </ul>

            @elseif(isset($hasil) && count($hasil) == 0)
                <div style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" style="opacity: 0.5; margin-bottom: 10px;">
                    <h4 style="font-size: 14px; margin-bottom: 5px;">Rute Tidak Ditemukan</h4>
                    <p style="font-size: 11px;">Coba atur ulang filter.</p>
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                    <div style="font-size: 40px; margin-bottom: 10px;">🗺️</div>
                    <h4 style="font-size: 14px; margin-bottom: 5px; font-weight: 600;">Siap Berpetualang?</h4>
                    <p style="font-size: 11px;">Deteksi lokasi & atur filter (⚙️) di pojok kanan atas.</p>
                </div>
            @endif
            
            <div style="text-align: center; margin-top: 20px; margin-bottom: 20px; font-size: 10px; color: #cbd5e1;">
                Smart Itinerary Banjarbakula © 2025
            </div>
        </div>
    </div>

    <div id="customToast" class="toast-notification">
        <div class="toast-icon">⚠️</div>
        <div class="toast-message">
            <h4 id="toastTitle">Perhatian!</h4>
            <p id="toastBody">Pesan error muncul disini.</p>
        </div>
    </div>

    <div id="directoryModal" class="modal-overlay" style="display:none;">
        <div class="directory-content">

            <!-- HEADER -->
            <div class="directory-header">
                <div class="directory-title">
                    <h3>Jelajahi Banjarbakula</h3>
                    <p>Temukan destinasi favoritmu</p>
                </div>
                <button onclick="closeDirectoryModal()" class="btn-close-circle">×</button>
            </div>

            <!-- FILTER BAR -->
            <div class="directory-filter-bar">
    
                <div class="filter-top-row">
                    <div class="search-wrapper-modern">
                        <span class="search-icon-modern">🔍</span>
                        <input type="text" id="dirSearchInput" placeholder="Mau kemana hari ini? Cari wisata..." onkeyup="applyDirectoryFilter()">
                    </div>

                    <button onclick="toggleAdvancedFilters()" id="btnFilterToggle" class="btn-filter-toggle">
                        <span class="icon">⚙️</span> Filter
                    </button>
                </div>

                <div id="advancedFilters" class="advanced-filters-wrapper">
                    <div class="filter-grid">
                        
                        <div class="select-wrapper-modern">
                            <label>Waktu Kunjungan</label>
                            <select id="dirJamSelect" onchange="applyDirectoryFilter()">
                                <option value="">Semua Jam</option>
                                <option value="pagi"> Pagi (06–11)</option>
                                <option value="siang"> Siang (11–15)</option>
                                <option value="sore"> Sore (15–18)</option>
                                <option value="malam"> Malam (18–22)</option>
                            </select>
                        </div>

                        <div class="select-wrapper-modern">
                            <label>Wilayah</label>
                            <select id="dirRegionSelect" onchange="applyDirectoryFilter()">
                                <option value="">Semua Wilayah</option>
                                <option value="Banjarmasin">Banjarmasin</option>
                                <option value="Banjarbaru">Banjarbaru</option>
                                <option value="Banjar">Kab. Banjar</option>
                                <option value="Barito Kuala">Barito Kuala</option>
                                <option value="Tanah Laut">Tanah Laut</option>
                            </select>
                        </div>

                        <div class="select-wrapper-modern">
                            <label>Kategori</label>
                            <select id="dirKategoriSelect" onchange="applyDirectoryFilter()">
                                <option value="">Semua Kategori</option>
                                <option value="Alam"> Alam</option>
                                <option value="Religi"> Religi</option>
                                <option value="Kuliner"> Kuliner</option>
                                <option value="Belanja"> Belanja</option>
                                <option value="Agro"> Agrowisata</option>
                                <option value="Edukasi"> Edukasi</option>
                                <option value="Budaya"> Budaya</option>
                                <option value="Rekreasi"> Rekreasi</option>
                            </select>
                        </div>

                        <div class="select-wrapper-modern">
                            <label>Budget Maksimal</label>
                            <select id="dirBudgetSelect" onchange="applyDirectoryFilter()">
                                <option value="">Semua Budget</option>
                                <option value="0">Gratis Only</option>
                                <option value="10000">≤ Rp 10.000</option>
                                <option value="25000">≤ Rp 25.000</option>
                                <option value="50000">≤ Rp 50.000</option>
                            </select>
                        </div>

                    </div>
                </div>

            </div>

            <!-- GRID -->
            <div id="directoryList" class="directory-grid-modern">
                @foreach($semua_wisata as $w)
                    <div class="dir-card"
                        onclick="focusOnLocation({{ $w->latitude }}, {{ $w->longitude }}, {{ $w->id }}); closeDirectoryModal();">
                        <div class="dir-card-img-wrapper">
                            <img
                                src="{{ Str::startsWith($w->gambar, 'http') ? $w->gambar : asset('storage/' . $w->gambar) }}"
                                alt="{{ $w->nama_tempat }}"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='https://placehold.co/600x400/e2e8f0/64748b?text={{ urlencode($w->nama_tempat) }}';"
                            >
                        </div>

                        <div class="dir-card-body">
                            <div class="dir-card-title">{{ $w->nama_tempat }}</div>

                            <div class="dir-card-footer">
                                <span class="dir-cat">{{ $w->kategori ?? 'Umum' }}</span>
                                <span class="dir-price">
                                    {{ $w->harga_tiket == 0 ? 'Gratis' : 'Rp '.number_format($w->harga_tiket,0,',','.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>



    <div id="custom-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 25px; border-radius: 16px; width: 90%; max-width: 320px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            </div>
    </div>

    <div id="infoModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="background: white; width: 90%; max-width: 450px; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh;">
        
        <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; font-size: 18px; color: #1e293b; font-weight: 700;">Tentang Aplikasi</h3>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Smart Itinerary Banjarbakula</p>
            </div>
            <button onclick="closeInfoModal()" class="btn-close-circle" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">×</button>
        </div>

        <div style="padding: 25px; overflow-y: auto; flex: 1;">
            
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="width: 70px; height: 70px; background: #e0e7ff; color: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 30px;">
                    🎓
                </div>
                <h4 style="margin: 0; font-size: 16px; color: #1e293b; font-weight: 700;">Ahmad Sappauni</h4>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #4f46e5; font-weight: 600;">NIM. 2210131210010</p>
                <p style="margin: 2px 0 0 0; font-size: 12px; color: #94a3b8;">Universitas Lambung Mangkurat</p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 20px;">
                <h5 style="margin: 0 0 10px 0; font-size: 13px; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; font-weight: 700;">
                     Teknologi & Algoritma
                </h5>
                <p style="font-size: 12px; color: #475569; line-height: 1.6; margin: 0 0 10px 0; text-align: justify;">
                    Sistem ini menerapkan <b>Algoritma Greedy</b> untuk optimalisasi rute berdasarkan jarak & budget, serta <b>Haversine Formula</b> untuk akurasi jarak geografis.
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 10px; padding: 4px 8px; border-radius: 6px; background: #fee2e2; color: #dc2626; font-weight: 600;">Laravel</span>
                    <span style="font-size: 10px; padding: 4px 8px; border-radius: 6px; background: #dbeafe; color: #2563eb; font-weight: 600;">LeafletJS</span>
                    <span style="font-size: 10px; padding: 4px 8px; border-radius: 6px; background: #dcfce7; color: #16a34a; font-weight: 600;">Greedy</span>
                </div>
            </div>

            <div>
                <h5 style="margin: 0 0 10px 0; font-size: 13px; color: #334155; font-weight: 700;">🗺️ Legenda Peta</h5>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 12px; color: #475569;">
                        <span style="width: 10px; height: 10px; background: #ef4444; border-radius: 50%; display: inline-block; flex-shrink: 0;"></span>
                        <span>Lokasi Kamu</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 12px; color: #475569;">
                        <span style="width: 10px; height: 10px; background: #6366f1; border-radius: 50%; display: inline-block; flex-shrink: 0;"></span>
                        <span>Rute Terpilih</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 12px; color: #475569;">
                        <span style="width: 10px; height: 10px; background: #f59e0b; border-radius: 50%; display: inline-block; flex-shrink: 0;"></span>
                        <span>Wisata Alternatif</span>
                    </li>
                </ul>
            </div>

        </div>

        <div style="padding: 15px; border-top: 1px solid #f1f5f9; text-align: center; background: white;">
            <button onclick="closeInfoModal()" class="btn btn-primary" style="padding: 12px; width: 100%; margin: 0; border-radius: 12px;">
                Tutup
            </button>
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
        
        <span id="panelKategori" class="panel-badge"></span>
    </div>

    <div class="panel-body">
        
        <div class="panel-header">
            <h2 id="panelNama" class="panel-title"></h2>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5px;">
                <p id="panelHarga" class="panel-price"></p>
                <div class="panel-rating">⭐ 4.8 (Ulasan)</div> </div>
            <p id="panelAlamat" class="panel-address"></p>
        </div>

        <div class="panel-nav">
            <button onclick="prevWisata()" class="nav-btn" title="Sebelumnya">←</button>
            <span class="panel-nav-text">Jelajahi Wisata Lain</span>
            <button onclick="nextWisata()" class="nav-btn" title="Berikutnya">→</button>
        </div>

        <div class="panel-divider"></div>

        <div class="panel-actions">
            <button id="panelRuteBtn" class="btn-rute-hero">
                Mulai Rute Dari Sini
            </button>
            
            <a id="panelDetailBtn" class="btn-detail-outline">
                Lihat Detail Lengkap
            </a>
        </div>
    </div>
</div>

</div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polylinedecorator@1.6.0/dist/leaflet.polylineDecorator.js"></script>

   <script>
        // PERBAIKAN: Gunakan ?? [] agar tidak error jika variabel undefined
        window.wisataData = @json(isset($hasil) ? $hasil : null);
        window.wisataLainData = @json(isset($wisata_lain) ? $wisata_lain : null);
        window.allWisataData = @json($semua_wisata ?? []); 
        
        // --- TAMBAHAN PENTING: SISA BUDGET DARI SERVER ---
        // Jika sisa_budget ada (dari controller), pakai itu. Jika tidak, ambil dari input request.
        window.realSisaBudget = @json(isset($sisa_budget) ? $sisa_budget : (request('budget') ?? 0));
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        // Pastikan halaman sudah siap
        document.addEventListener("DOMContentLoaded", function() {
            // Cek apakah URL mengandung 'rute_fix' (artinya habis sambung rute)
            if (window.location.search.includes('rute_fix')) {
                // Panggil fungsi showToast (pastikan fungsi ini ada di script.js)
                if (typeof showToast === 'function') {
                    showToast("Sistem diperbaharui! ", "Menampilkan hasil paling optimal untuk anda");
                }
            }
        });
    </script>
    <script>
        function toggleFilterMenu() {
            var menu = document.getElementById('menuPilihan');
            if (menu.style.display === "none") {
                menu.style.display = "block";
            } else {
                menu.style.display = "none";
            }
        }
    </script>  
</body>
</html>