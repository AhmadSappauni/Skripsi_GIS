<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $wisata->nama_tempat }} - Detail Wisata</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}"> </head>

<body>

    <div class="detail-container">
        
        <div class="detail-header">
            <a href="#" onclick="goBackToSearch()" class="btn-back" title="Kembali">
                ←
            </a>
            
            <div class="image-wrapper">
                <img src="{{ Str::startsWith($wisata->gambar, 'http') ? $wisata->gambar : asset('storage/' . $wisata->gambar) }}" 
                     alt="{{ $wisata->nama_tempat }}" 
                     class="detail-img"
                     onerror="this.onerror=null; this.src='https://placehold.co/800x500/e2e8f0/64748b?text=No+Image';">
                <div class="image-overlay"></div>
            </div>
        </div>

        <div class="detail-body">
            
            <div class="detail-title-section">
                <div class="badges">
                    <span class="badge badge-category">📂 {{ $wisata->kategori }}</span>
                    @if($wisata->harga_tiket == 0)
                        <span class="badge badge-free">🎟️ Gratis</span>
                    @else
                        <span class="badge badge-price">🎟️ Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</span>
                    @endif
                </div>
                <h1>{{ $wisata->nama_tempat }}</h1>
                <p class="address">📍 {{ $wisata->alamat }}</p>
            </div>

            <div class="divider"></div>

            <div class="detail-section">
                <h3>Tentang Tempat Ini</h3>
                <p class="description">
                    {{ $wisata->deskripsi ?? 'Belum ada deskripsi detail untuk tempat wisata ini. Namun tempat ini menawarkan pengalaman menarik yang layak untuk dikunjungi.' }}
                </p>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">⏰</div>
                    <div class="info-text">
                        <label>Jam Operasional</label>
                        <span>{{ $wisata->jam_buka }}</span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">🗓️</div>
                    <div class="info-text">
                        <label>Hari Buka</label>
                        <span>
                            @php
                                $daftarHari = explode(',', $wisata->hari_buka);
                                $jumlahHari = count($daftarHari);
                            @endphp

                            @if($jumlahHari == 7)
                                <span class="text-success">✅ Buka Setiap Hari</span>
                            @elseif($jumlahHari == 0 || $wisata->hari_buka == '')
                                <span class="text-danger">⛔ Tutup Sementara</span>
                            @else
                                {{ str_replace(',', ', ', $wisata->hari_buka) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <<a href="https://www.google.com/maps/dir/?api=1&destination={{ $wisata->latitude }},{{ $wisata->longitude }}" target="_blank" class="btn-maps">
                <span style="font-size: 20px; margin-right: 10px;">🗺️</span> 
                Buka Petunjuk Arah (Google Maps)
            </a>

        </div>
    </div>

    <script src="{{ asset('js/detail.js') }}"></script>
</body>
</html>