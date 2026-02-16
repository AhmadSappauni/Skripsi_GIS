
<style>
    /* Tombol Header Bersih */
.btn-action-clean {
    width: 40px; height: 40px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #334155;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
}
.btn-action-clean:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.btn-action-clean.danger:hover {
    background: #fef2f2; border-color: #fca5a5; color: #ef4444;
}

/* Dropdown Menu Premium */
.menu-dropdown-wrapper { position: relative; }

.premium-dropdown {
    display: none; /* Hidden by default */
    position: absolute;
    top: 50px; right: 0;
    width: 260px;
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
    padding: 8px;
    z-index: 100;
    animation: scaleIn 0.2s ease-out;
    transform-origin: top right;
}
.premium-dropdown.active { display: block; }

.dropdown-header {
    font-size: 10px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 8px 12px;
}

.p-dropdown-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    transition: 0.2s;
    cursor: pointer;
}
.p-dropdown-item:hover { background: #f8fafc; }

/* Icon Box Warna-Warni */
.icon-box {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.icon-box.purple { background: #eef2ff; color: #4f46e5; }
.icon-box.orange { background: #fff7ed; color: #ea580c; }
.icon-box.green { background: #ecfdf5; color: #059669; }

/* Typography */
.item-title { font-size: 13px; font-weight: 600; color: #1e293b; }
.item-desc { font-size: 11px; color: #64748b; margin-top: 2px; }

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

</style>

<div class="floating-sidebar" id="mainSidebar">

    <div class="sidebar-header"
    style="flex-shrink: 0; background: white; z-index: 20; border-bottom: 1px solid #f1f5f9; padding: 25px; display: flex; justify-content: space-between; align-items: start;">
    
    <div onclick="openInfoModal()" style="cursor: pointer; flex: 1;">
        <h2 style="margin: 0; line-height: 1; font-size: 24px; color: #0f172a; letter-spacing: -0.5px;">
            Smart <span style="color: #4f46e5;">Itinerary</span>
        </h2>
        <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px;">
            <span style="font-size: 11px; background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 20px; font-weight: 600;">Banjarbakula</span>
        </div>
    </div>

    <div style="display: flex; gap: 8px;">
        
        <div class="menu-dropdown-wrapper">
            <button onclick="toggleMainMenu()" class="btn-action-clean" title="Menu Aplikasi">
                <i class="ri-menu-4-line" style="font-size: 18px;"></i>
            </button>
            
            <div id="mainMenuDropdown" class="premium-dropdown">
                <div class="dropdown-header">Akses Cepat</div>
                
                <a href="javascript:void(0)" onclick="toggleFilterMenu(); toggleMainMenu()" class="p-dropdown-item">
                    <div class="icon-box purple"><i class="ri-equalizer-line"></i></div>
                    <div>
                        <div class="item-title">Filter Pencarian</div>
                        <div class="item-desc">Atur budget & kategori</div>
                    </div>
                </a>

                <a href="javascript:void(0)" onclick="openDirectoryModal(); toggleMainMenu()" class="p-dropdown-item">
                    <div class="icon-box orange"><i class="ri-folder-3-line"></i></div>
                    <div>
                        <div class="item-title">Jelajahi Wisata</div>
                        <div class="item-desc">Lihat semua destinasi</div>
                    </div>
                </a>

                <a href="javascript:void(0)" onclick="openVisitedModal(); toggleMainMenu()" class="p-dropdown-item">
                    <div class="icon-box green"><i class="ri-footprint-line"></i></div>
                    <div>
                        <div class="item-title">Jurnal Perjalanan</div>
                        <div class="item-desc">Catatan & koleksimu</div>
                    </div>
                </a>
            </div>
        </div>

        <button onclick="toggleSidebar()" class="btn-action-clean danger" title="Tutup Sidebar">
            <i class="ri-close-line" style="font-size: 20px;"></i>
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

                        <div style="grid-column: span 2; position: relative;">
                            <div class="label-with-tooltip">
                                <label class="form-label" style="margin-bottom: 0;">Minimal Rating</label>
                                <span class="info-icon"
                                    data-tooltip="Hanya tampilkan wisata dengan kualitas tertentu">!</span>
                            </div>

                            <input type="hidden" name="min_rating" id="hiddenRatingInput"
                                value="{{ request('min_rating', 0) }}">

                            <div class="rating-dropdown-trigger" id="ratingTrigger" onclick="toggleRatingMenu()">
                                <i class="ri-star-fill rating-main-icon"></i>

                                <span class="selected-text" id="selectedRatingDisplay">
                                    @if (request('min_rating') == '3')
                                        3 Bintang+
                                    @elseif(request('min_rating') == '4')
                                        4 Bintang+
                                    @elseif(request('min_rating') == '4.5')
                                        4.5 Bintang+
                                    @else
                                        Semua Bintang
                                    @endif
                                </span>

                                <i class="ri-arrow-down-s-line trigger-arrow"></i>
                            </div>

                            <div class="rating-options-list" id="ratingMenuList">

                                <div class="rating-option-item" onclick="selectRating(0, 'Semua Bintang')">
                                    <span style="font-weight: 600; color: #334155;">Semua Bintang</span>
                                </div>

                                <div class="rating-option-item" onclick="selectRating(3, '3 Bintang+')">
                                    <div class="premium-stars">
                                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i
                                            class="ri-star-fill"></i>
                                    </div>
                                    <span class="rating-label">(3+)</span>
                                </div>

                                <div class="rating-option-item" onclick="selectRating(4, '4 Bintang+')">
                                    <div class="premium-stars">
                                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i
                                            class="ri-star-fill"></i><i class="ri-star-fill"></i>
                                    </div>
                                    <span class="rating-label">(4+)</span>
                                </div>

                                <div class="rating-option-item" onclick="selectRating(4.5, '4.5 Bintang+')">
                                    <div class="premium-stars">
                                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i
                                            class="ri-star-fill"></i><i class="ri-star-fill"></i><i
                                            class="ri-star-half-fill"></i>
                                    </div>
                                    <span class="rating-label">(4.5+)</span>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <button type="submit" class="btn btn-primary"
                style="width: 100%; padding: 14px; font-weight: 700; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);">
                Cari Rute Cerdas
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
                            <img src="{{ Str::startsWith($wisata->gambar, ['http', 'data:']) ? $wisata->gambar : asset($wisata->gambar) }}"
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


<script>
    window.toggleMainMenu = function() {
    const menu = document.getElementById('mainMenuDropdown');
    menu.classList.toggle('active');

    // Klik di luar untuk menutup
    if (menu.classList.contains('active')) {
        document.addEventListener('click', function closeMenu(e) {
            if (!e.target.closest('.menu-dropdown-wrapper')) {
                menu.classList.remove('active');
                document.removeEventListener('click', closeMenu);
            }
        });
    }
};
</script>