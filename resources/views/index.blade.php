<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Itinerary Banjarbakula</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
</head>
<body>
    @include('components.map')

    <div id="loadingOverlay">
        <div class="spinner"></div>
        <h3 style="color: var(--primary); margin-top: 20px; font-weight: 800;">Sistem Bekerja...</h3>
        <p style="color: var(--text-light); font-size: 14px;">Mencari rute terbaik untuk liburanmu</p>
    </div>

    <button id="btnShowSidebar" onclick="toggleSidebar()"
        style="display: none; position: absolute; top: 20px; left: 20px; z-index: 1000; background: white; border: none; padding: 12px 18px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer; font-weight: 700; color: #1e293b; align-items: center; gap: 8px;">
        <span style="font-size: 18px;">☰</span> Menu
    </button>

    @include('components.floating_sidebar')
    @include('components.koleksiku')
    @include('components.directory')
    @include('components.panel-detail')
    @include('components.about')

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

        <script>
            window.visitedIds = @json($visitedIds ?? []); 
            window.isLoggedIn = true;
            window.csrfToken  = "{{ csrf_token() }}"; 
        </script>

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
        <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>