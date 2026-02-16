<aside class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">Smart<span>Admin</span></div>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-label">Menu Utama</li>
        
        <li>
            <a href="{{ route('admin.index') }}" class="menu-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <i class="ri-dashboard-3-line icon"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('admin.stats') }}" class="menu-link {{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                <i class="ri-pie-chart-2-line icon"></i> Statistik
            </a>
        </li>

        <li>
            <a href="{{ route('admin.create') }}" class="menu-link {{ request()->routeIs('admin.create') ? 'active' : '' }}">
                <i class="ri-add-circle-line icon"></i> Tambah Wisata
            </a>
        </li>

        <li class="menu-label">Pengaturan</li>
        <li>
            <a href="/" target="_blank" class="menu-link">
                <i class="ri-global-line icon"></i> Lihat Website
            </a>
        </li>
    </ul>
</aside>