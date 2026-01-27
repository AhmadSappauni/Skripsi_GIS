<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Premium - SmartAdmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }

        /* --- 1. FIX HEADER STICKY (Matikan Sticky) --- */
        .top-header {
            position: relative !important;
            top: auto !important;
            background: transparent;
            padding: 30px 40px 10px 40px;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1;
        }

        .page-title {
            font-size: 26px;
            letter-spacing: -0.5px;
            color: #111827;
        }

        .date-display {
            font-size: 13px;
            font-weight: 600;
            color: #9ca3af;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* --- HERO CARD --- */
        .hero-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px -15px rgba(79, 70, 229, 0.5);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .hero-bg-circle {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            top: -100px;
            right: -50px;
        }

        .hero-content h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero-content p {
            font-size: 15px;
            opacity: 0.9;
            max-width: 400px;
            line-height: 1.6;
        }

        .hero-stats {
            display: flex;
            gap: 30px;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .h-stat-item {
            text-align: center;
        }

        .h-stat-item h4 {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            line-height: 1;
        }

        .h-stat-item span {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            font-weight: 600;
        }

        /* --- SEARCH & BUTTONS --- */
        .control-bar {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-group {
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-capsule {
            background: white;
            border-radius: 16px;
            padding: 0 0 0 20px;
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #e5e7eb;
            transition: 0.3s;
            height: 54px;
        }

        .search-capsule:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            transform: translateY(-2px);
        }

        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            color: #374151;
            font-weight: 500;
            background: transparent;
        }

        .btn-search {
            width: 60px;
            height: 100%;
            background: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: 0.2s;
            font-size: 18px;
            border-radius: 0 16px 16px 0;
        }

        .btn-search:hover {
            color: #4f46e5;
            background: #f9fafb;
        }

        .btn-filter-toggle {
            height: 54px;
            padding: 0 24px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .btn-filter-toggle:hover,
        .btn-filter-toggle.active {
            border-color: #6366f1;
            color: #4f46e5;
            background: #eef2ff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.2);
        }

        .btn-create {
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            color: white;
            padding: 0 30px;
            height: 54px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            margin-left: auto;
        }

        .btn-create:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #000000 0%, #1f2937 100%);
        }

        /* --- FILTER DRAWER --- */
        .filter-drawer {
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0;
            opacity: 0;
            transform: translateY(-10px);
        }

        .filter-drawer.open {
            max-height: 300px;
            opacity: 1;
            margin-bottom: 30px;
            transform: translateY(0);
        }

        .filter-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
            display: grid;
            grid-template-columns: repeat(4, 1fr) auto;
            gap: 20px;
            align-items: end;
        }

        .filter-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .filter-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            color: #374151;
            background: #f9fafb;
            outline: none;
            transition: 0.2s;
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-apply {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            height: 46px;
            width: 100%;
            transition: 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-apply:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .btn-reset {
            text-decoration: none;
            color: #ef4444;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            display: block;
            margin-top: 10px;
        }

        /* --- TABLE --- */
        .table-wrapper {
            margin-top: 10px;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .custom-table th {
            text-align: left;
            padding: 0 24px;
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .row-card td {
            background: white;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 24px;
            transition: 0.2s;
            vertical-align: middle;
        }

        .row-card td:first-child {
            border-left: 1px solid #e5e7eb;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .row-card td:last-child {
            border-right: 1px solid #e5e7eb;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            text-align: right;
        }

        .row-card:hover td {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
            border-color: transparent;
        }

        .t-img {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .t-name {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .t-sub {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .t-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .t-price {
            font-weight: 700;
            color: #059669;
            font-size: 14px;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            color: #9ca3af;
            border: 1px solid #f3f4f6;
            text-decoration: none;
            font-size: 18px;
        }

        .action-btn.edit:hover {
            background: #fff7ed;
            color: #ea580c;
            box-shadow: 0 5px 15px -3px rgba(234, 88, 12, 0.2);
            transform: translateY(-3px);
        }

        .action-btn.del:hover {
            background: #fef2f2;
            color: #ef4444;
            box-shadow: 0 5px 15px -3px rgba(239, 68, 68, 0.2);
            transform: translateY(-3px);
        }

        /* --- 2. SMART MODAL (GLASSMORPHISM) --- */
        .smart-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(12px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .smart-modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .smart-modal-card {
            background: white;
            width: 90%;
            max-width: 400px;
            border-radius: 28px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .smart-modal-overlay.open .smart-modal-card {
            transform: scale(1) translateY(0);
        }

        .modal-icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fef2f2;
            color: #ef4444;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            position: relative;
        }

        .modal-icon-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            border: 2px solid currentColor;
            opacity: 0.2;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.4;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .modal-title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
        }

        .modal-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn-modal {
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: 0.2s;
            flex: 1;
        }

        .btn-cancel-modal {
            background: #f3f4f6;
            color: #4b5563;
        }

        .btn-cancel-modal:hover {
            background: #e5e7eb;
        }

        .btn-delete-modal {
            background: #ef4444;
            color: white;
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.4);
        }

        .btn-delete-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(239, 68, 68, 0.5);
        }


        /* ===============================
   PREMIUM PAGINATION – SMARTADMIN
   =============================== */

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin: 24px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 14px;
            border-radius: 12px;
            background: #f1f5f9;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        /* Hover */
        .pagination .page-link:hover {
            background: #eef2ff;
            color: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px -6px rgba(79, 70, 229, 0.4);
        }

        /* Active */
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 10px 20px -8px rgba(79, 70, 229, 0.6);
        }

        /* Disabled */
        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
    </style>
</head>

<body class="admin-layout">

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo">Smart<span>Admin</span></div>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-label">Menu Utama</li>
            <li><a href="{{ route('admin.index') }}" class="menu-link active"><i class="ri-dashboard-3-line icon"></i>
                    Dashboard</a></li>
            <li><a href="{{ route('admin.create') }}" class="menu-link"><i class="ri-add-circle-line icon"></i> Tambah
                    Wisata</a></li>
            <li class="menu-label">Pengaturan</li>
            <li><a href="/" target="_blank" class="menu-link"><i class="ri-global-line icon"></i> Lihat
                    Website</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div>
                <h2 class="page-title">Overview</h2>
                <div class="date-display">
                    <i class="ri-calendar-event-line"></i>
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
            <div class="nav-profile">
                <div class="avatar-circle">A</div>
            </div>
        </header>

        <div class="content-wrapper">

            <div class="hero-card">
                <div class="hero-bg-circle"></div>
                <div class="hero-content">
                    <h1>Halo, Admin! 👋</h1>
                    <p>Pantau data wisata terbaru dan gunakan filter canggih untuk mengelola destinasi.</p>
                </div>
                <div class="hero-stats">
                    <div class="h-stat-item">
                        <h4>{{ $dataWisata->total() }}</h4><span>Destinasi</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.index') }}" method="GET" id="filterForm">

                <div class="control-bar">
                    <div class="search-group">
                        <div class="search-capsule">
                            <input type="text" name="search" class="search-input" placeholder="Cari nama tempat..."
                                value="{{ request('search') }}" autocomplete="off">
                            <button type="submit" class="btn-search"><i class="ri-search-2-line"></i></button>
                        </div>
                        <button type="button" class="btn-filter-toggle" onclick="toggleFilter()" id="filterBtn">
                            <i class="ri-filter-3-line"></i> Filter
                        </button>
                    </div>

                    <a href="{{ route('admin.create') }}" class="btn-create">
                        <i class="ri-add-line"></i> Tambah Data
                    </a>
                </div>

                <div class="filter-drawer" id="filterDrawer">
                    <div class="filter-card">
                        <div class="filter-group">
                            <label>Kategori</label>
                            <select name="kategori_filter" class="filter-select">
                                <option value="">Semua Kategori</option>
                                @foreach (['Alam', 'Religi', 'Kuliner', 'Belanja', 'Budaya', 'Edukasi', 'Rekreasi', 'Agrowisata'] as $kat)
                                    <option value="{{ $kat }}"
                                        {{ request('kategori_filter') == $kat ? 'selected' : '' }}>{{ $kat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Harga Tiket</label>
                            <select name="harga_filter" class="filter-select">
                                <option value="">Semua Harga</option>
                                <option value="gratis" {{ request('harga_filter') == 'gratis' ? 'selected' : '' }}>
                                    Gratis</option>
                                <option value="berbayar" {{ request('harga_filter') == 'berbayar' ? 'selected' : '' }}>
                                    Berbayar</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Hari Operasional</label>
                            <select name="hari_filter" class="filter-select">
                                <option value="">Semua Hari</option>
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <option value="{{ $hari }}"
                                        {{ request('hari_filter') == $hari ? 'selected' : '' }}>{{ $hari }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Jam Buka</label>
                            <select name="jam_filter" class="filter-select">
                                <option value="">Semua Jam</option>
                                <option value="24jam" {{ request('jam_filter') == '24jam' ? 'selected' : '' }}>Buka 24
                                    Jam</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn-apply"><i class="ri-check-line"></i> Terapkan</button>
                            @if (request()->anyFilled(['kategori_filter', 'harga_filter', 'hari_filter', 'jam_filter', 'search']))
                                <a href="{{ route('admin.index') }}" class="btn-reset">Reset Filter</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="80">Foto</th>
                            <th>Informasi Tempat</th>
                            <th>Kategori</th>
                            <th>Tiket</th>
                            <th>Jam Buka</th>
                            <th width="120" style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataWisata as $w)
                            <tr class="row-card">
                                <td>
                                    <img src="{{ Str::startsWith($w->gambar, ['http', 'data:']) ? $w->gambar : asset('images/' . $w->gambar) }}"
                                        class="t-img" onerror="this.src='https://placehold.co/100?text=IMG'">
                                </td>
                                <td>
                                    <div class="t-name">{{ $w->nama_tempat }}</div>
                                    <div class="t-sub"><i class="ri-map-pin-line" style="font-size: 12px;"></i>
                                        {{ Str::limit($w->alamat, 40) }}</div>
                                </td>
                                <td><span class="t-badge">{{ $w->kategori }}</span></td>
                                <td>
                                    @if ($w->harga_tiket == 0)
                                        <span class="t-price" style="color: #6b7280;">Gratis</span>
                                    @else
                                        <span class="t-price">Rp
                                            {{ number_format($w->harga_tiket, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td style="font-size: 13px; font-weight: 500; color: #4b5563;">
                                    {{ $w->jam_buka }}
                                </td>
                                <td>
                                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                        <a href="{{ route('admin.edit', $w->id) }}" class="action-btn edit"
                                            title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </a>

                                        <form id="delete-form-{{ $w->id }}"
                                            action="{{ route('admin.destroy', $w->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>

                                        <button class="action-btn del" title="Hapus"
                                            onclick="showDeleteModal({{ $w->id }})">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 60px; color: #9ca3af;">
                                    <i class="ri-search-eye-line"
                                        style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                    <p>Tidak ada data yang cocok dengan filter kamu.</p>
                                    <a href="{{ route('admin.index') }}"
                                        style="color: #4f46e5; font-weight: 600; text-decoration: none;">Reset
                                        Filter</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $dataWisata->links('pagination.admin') }}

            </div>


        </div>
    </main>

    <div class="smart-modal-overlay" id="deleteModal">
        <div class="smart-modal-card">
            <div class="modal-icon-box">
                <i class="ri-delete-bin-2-line"></i>
            </div>
            <h3 class="modal-title">Hapus Data Ini?</h3>
            <p class="modal-desc">Tindakan ini tidak dapat dibatalkan. Data wisata akan hilang secara permanen.</p>

            <div class="modal-actions">
                <button onclick="closeDeleteModal()" class="btn-modal btn-cancel-modal">Batal</button>
                <button onclick="confirmDelete()" class="btn-modal btn-delete-modal">Ya, Hapus!</button>
            </div>
        </div>
    </div>

    <script>
        // LOGIC FILTER DRAWER
        function toggleFilter() {
            const drawer = document.getElementById('filterDrawer');
            const btn = document.getElementById('filterBtn');
            drawer.classList.toggle('open');
            btn.classList.toggle('active');
        }

        // LOGIC SMART MODAL DELETE
        let deleteId = null;

        function showDeleteModal(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('open');
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId) {
                document.getElementById('delete-form-' + deleteId).submit();
            }
        }

        // Tutup modal jika klik luar
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
        });
    </script>

</body>

</html>
