<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Wisata - Smart Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body class="admin-layout">
    @include('admin.components_admin.sidebar')
    
    <main class="main-content">
        <header class="top-header">
            <div>
                <h2 style="margin:0; font-size:20px; font-weight:700; color:#1e293b;">Pusat Data & Analitik</h2>
                <div style="font-size:12px; color:#64748b; margin-top:4px;">
                    <i class="ri-calendar-line"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
            <div style="width:40px; height:40px; background:#eef2ff; color:#4f46e5; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                A
            </div>
        </header>

        <div class="content-wrapper">
            
            <div class="grid-charts">
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h4 class="chart-title"> 10 Destinasi Terpopuler</h4>
                        <span style="font-size:11px; background:#eef2ff; color:#4f46e5; padding:4px 10px; border-radius:12px; font-weight:600;">
                            Berdasarkan Kunjungan
                        </span>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h4 class="chart-title"> Proporsi Kategori</h4>
                    </div>
                    <div style="height: 250px; position: relative; display:flex; justify-content:center;">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                    <div style="text-align:center; margin-top:20px; padding-top:20px; border-top:1px dashed #e2e8f0;">
                        <p style="font-size:12px; color:#64748b; margin:0;">
                            Kategori paling mendominasi adalah<br>
                            <b style="color:#1e293b; font-size:14px;">
                                {{ count($kategoriStats) > 0 ? array_keys($kategoriStats->toArray())[0] : '-' }}
                            </b>
                        </p>
                    </div>
                </div>

            </div>

            <div style="margin-top: 40px; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-map-pin-range-line" style="color: #4f46e5;"></i> Analisis Wilayah
                </h3>
                <p style="color: #64748b; font-size: 13px; margin-top: 5px;">Perbandingan kinerja pariwisata antar daerah.</p>
            </div>

            <div class="chart-card" style="min-width: 0;">
                <div class="chart-header">
                    <h4 class="chart-title"> Supply vs Demand per Wilayah</h4>
                    <span style="font-size:11px; background:#f1f5f9; padding:4px 10px; border-radius:12px; color:#64748b;">Jumlah Wisata vs Kunjungan</span>
                </div>
                <div style="height: 350px; position: relative; width: 100%;">
                    <canvas id="regionChart"></canvas>
                </div>
            </div>

            <div class="chart-card" style="margin-top: 25px; min-width: 0;">
    
                <div class="chart-header" style="flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h4 class="chart-title">Top Destinasi per Wilayah</h4>
                        <span style="font-size:11px; color:#64748b;">Lihat destinasi terfavorit di setiap kota</span>
                    </div>
                    
                    <div style="position: relative;">
                        <i class="ri-map-pin-line" style="position: absolute; left: 10px; top: 10px; color: #4f46e5;"></i>
                        <select id="citySelector" onchange="updateCityChart()" style="padding: 8px 15px 8px 35px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1e293b; font-weight: 600; outline: none; cursor: pointer; background: #f8fafc;">
                            @foreach(array_keys($statsPerKota) as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="height: 350px; position: relative; width: 100%;">
                    <canvas id="cityDetailChart"></canvas>
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 30px; border-radius: 20px; color: white; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);">
                <div>
                    <h3 style="margin: 0 0 5px 0; font-size: 18px;">Butuh Laporan Bulanan?</h3>
                    <p style="margin: 0; opacity: 0.7; font-size: 13px;">Unduh rekap data statistik ini dalam format PDF siap cetak.</p>
                </div>
                <button onclick="alert('Fitur Export PDF akan segera hadir! ')" style="background: white; color: #0f172a; border: none; padding: 12px 25px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; display:flex; gap:8px; align-items:center;">
                    <i class="ri-download-cloud-2-line"></i> Export Data
                </button>
            </div>

        </div>
        
    </main>

    <script>
        // 1. Definisikan Fungsi Global di LUAR DOMContentLoaded agar aman diakses HTML
        let cityChart; 
        let cityDataRaw; 

        // Fungsi Update Chart
        function initCityChart(kota) {
            const ctxCity = document.getElementById('cityDetailChart').getContext('2d');
            const dataKota = cityDataRaw[kota];
            
            // Cek data kosong
            let labels = ['Belum ada data'];
            let data = [0];

            if (dataKota && dataKota.labels.length > 0) {
                labels = dataKota.labels;
                data = dataKota.data;
            }

            const config = {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Pengunjung',
                        data: data,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal Bar
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { precision: 0 } },
                        y: { grid: { display: false } }
                    }
                }
            };

            if (cityChart) { cityChart.destroy(); }
            cityChart = new Chart(ctxCity, config);
        }

        // Fungsi Trigger Dropdown
        window.updateCityChart = function() {
            const selector = document.getElementById('citySelector');
            initCityChart(selector.value);
        };

        // 2. Jalankan Script Utama saat Halaman Siap
        document.addEventListener("DOMContentLoaded", function() {
            
            // Ambil Data dari Controller (Sekali saja)
            cityDataRaw = @json($statsPerKota); 

            // --- CHART 1: POPULER ---
            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: @json($topWisataLabels),
                    datasets: [{
                        label: 'Total Kunjungan',
                        data: @json($topWisataData),
                        backgroundColor: '#4f46e5', borderRadius: 6, barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // --- CHART 2: KATEGORI (DOUGHNUT) ---
            const ctxDonut = document.getElementById('doughnutChart').getContext('2d');
            const dataKategori = @json($kategoriStats);
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(dataKategori),
                    datasets: [{
                        data: Object.values(dataKategori),
                        backgroundColor: ['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                        borderWidth: 0, hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } }
                }
            });

            // --- CHART 3: REGIONAL (DOUBLE BAR) ---
            const ctxRegion = document.getElementById('regionChart').getContext('2d');
            new Chart(ctxRegion, {
                type: 'bar',
                data: {
                    labels: @json($wilayahLabels),
                    datasets: [
                        {
                            label: 'Jumlah Objek Wisata',
                            data: @json($dataJumlahWisata),
                            backgroundColor: '#cbd5e1', borderRadius: 4
                        },
                        {
                            label: 'Total Kunjungan User',
                            data: @json($dataJumlahKunjungan),
                            backgroundColor: '#4f46e5', borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

            // --- INIT CHART KOTA (DENGAN DELAY 100ms) ---
            // Ini perbaikan utama agar chart muncul saat pertama load
            const selector = document.getElementById('citySelector');
            if (selector.options.length > 0) {
                selector.selectedIndex = 0; // Paksa pilih item pertama
                setTimeout(() => {
                    initCityChart(selector.value);
                }, 100); // Jeda sebentar
            }
        });
    </script>
</body>
</html>