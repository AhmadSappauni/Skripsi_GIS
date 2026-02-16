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

</head>

<body class="admin-layout">

    @include('admin.components_admin.sidebar')

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

            @include('admin.components_admin.daftar_wisata')

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