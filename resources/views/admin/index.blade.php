<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Itinerary</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    <style>
        .pagination { display: flex; justify-content: center; gap: 5px; list-style: none; margin-top: 30px; }
        .pagination li a, .pagination li span {
            padding: 8px 14px; border-radius: 8px; border: 1px solid #e2e8f0;
            color: #64748b; text-decoration: none; font-size: 13px; font-weight: 500;
        }
        .pagination li.active span { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination li a:hover { background: #f1f5f9; }
        .w-5 { width: 20px; } /* Fix ikon svg pagination laravel jika muncul besar */
        .h-5 { height: 20px; }
    </style>
</head>
<body class="admin-wrapper page-index">

    <nav class="admin-navbar">
        <div class="brand-logo">Smart<span>Admin</span></div>
        <div class="nav-profile">
            <span style="font-size: 13px; color: #64748b;">Administrator</span>
            <div class="avatar-circle">A</div>
        </div>
    </nav>

    <div class="admin-container">

        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Halo, Admin! 👋</h1>
                <p>Kelola data wisata Banjarbakula dengan mudah dan cepat.</p>
            </div>
            <a href="/" target="_blank" class="btn-white-glass">🌏 Lihat Website Utama</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card purple">
                <div class="icon-box">📍</div>
                <div class="stat-info">
                    <h3>{{ $dataWisata->total() }}</h3>
                    <p>Total Data</p>
                </div>
            </div>
            <div class="stat-card green">
                <div class="icon-box">📅</div>
                <div class="stat-info">
                    <h3>{{ date('d M') }}</h3>
                    <p>Hari Ini</p>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="icon-box">⚙️</div>
                <div class="stat-info">
                    <h3>v1.0</h3>
                    <p>Versi Sistem</p>
                </div>
            </div>
        </div>

        @if(session('sukses'))
            <div class="alert-success">
                <span style="font-size: 20px;">✅</span> 
                <div>{{ session('sukses') }}</div>
            </div>
        @endif

        <div class="section-header" style="flex-wrap: wrap; gap: 20px;">
            <div style="flex: 1; min-width: 200px;">
                <h3 class="section-title">Daftar Destinasi</h3>
                <p style="font-size: 12px; color: #64748b;">
                    Menampilkan data wisata terdaftar
                </p>
            </div>

            <form action="{{ route('admin.index') }}" method="GET" style="flex: 2; display: flex; justify-content: flex-end; gap: 10px;">
                <div class="modern-search-wrapper">
                    <input type="text" name="search" class="modern-search-input" 
                           placeholder="Cari nama tempat, kategori..." 
                           value="{{ request('search') }}" autocomplete="off">
                    
                    @if(request('search'))
                        <a href="{{ route('admin.index') }}" class="btn-search-icon btn-search-reset" title="Reset">✕</a>
                    @endif
                    
                    <button type="submit" class="btn-search-icon btn-search-submit" title="Cari">🔍</button>
                </div>

                <a href="{{ route('admin.create') }}" class="btn-add">+ Tambah</a>
            </form>
        </div>

        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Foto</th>
                        <th width="25%">Nama & Lokasi</th>
                        <th width="15%">Kategori</th>
                        <th width="15%">Harga Tiket</th>
                        <th width="15%">Jam Buka</th>
                        <th width="15%" style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataWisata as $index => $w)
                    <tr>
                        <td style="font-weight: bold; color: #cbd5e1;">
                            {{ $dataWisata->firstItem() + $index }}
                        </td>
                        <td>
                            <img src="{{ Str::startsWith($w->gambar, 'http') ? $w->gambar : asset('storage/' . $w->gambar) }}" 
                                 class="table-img" 
                                 alt="img"
                                 onerror="this.src='https://placehold.co/100?text=IMG'">
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 14px; color: #1e293b;">{{ $w->nama_tempat }}</div>
                            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                                📍 {{ Str::limit($w->alamat, 30) }}
                            </div>
                        </td>
                        <td>
                            <span class="badge primary">{{ $w->kategori }}</span>
                        </td>
                        <td>
                            @if($w->harga_tiket == 0)
                                <span class="badge success">Gratis</span>
                            @else
                                <span style="font-weight: 600; font-size: 13px; color: #475569;">
                                    Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: #64748b;">
                            @if($w->jam_operasional)
                                {{ $w->jam_operasional }}
                            @elseif($w->is_24_jam)
                                24 Jam
                            @elseif($w->jam_buka && $w->jam_tutup)
                                {{ $w->jam_buka }} - {{ $w->jam_tutup }}
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.edit', $w->id) }}" class="btn-icon btn-edit" title="Edit Data">
                                    ✏️
                                </a>
                                
                                <form action="{{ route('admin.destroy', $w->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-delete" title="Hapus Data">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 50px; color: #94a3b8;">
                            @if(request('search'))
                                <div style="font-size: 24px; margin-bottom: 10px;">🔍</div>
                                Data tidak ditemukan untuk pencarian "<strong>{{ request('search') }}</strong>".
                            @else
                                <div style="font-size: 24px; margin-bottom: 10px;">📂</div>
                                Belum ada data wisata. Silakan tambah data baru.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $dataWisata->links() }}
        </div>

        <div style="text-align: center; margin-top: 40px; color: #cbd5e1; font-size: 11px;">
            &copy; {{ date('Y') }} Smart Itinerary Banjarbakula Admin Panel
        </div>

    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>