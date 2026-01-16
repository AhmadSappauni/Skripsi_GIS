<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wisata - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-wrapper">

    <nav class="admin-navbar">
        <div class="brand-logo">
             Smart<span>Admin</span>
        </div>
        <div class="nav-profile">
            <span style="font-size: 13px; color: #64748b;">Mode: Edit Data</span>
            <div class="avatar-circle" style="background:#fff7ed; color:#ea580c;">✎</div>
        </div>
    </nav>

    <div class="admin-container">
        
        <div style="margin-bottom: 20px;">
            <a href="{{ route('admin.index') }}" style="text-decoration: none; color: #64748b; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                ⬅ Batal & Kembali
            </a>
        </div>

        <div class="admin-card">
            <div class="admin-header" style="text-align: left; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 30px;">
                <h2 style="color: #ea580c;">✏️ Edit Destinasi</h2>
                <p>Memperbarui data untuk: <strong>{{ $wisata->nama_tempat }}</strong></p>
            </div>

            <form action="{{ route('admin.update', $wisata->id) }}" method="POST" enctype="multipart/form-data">
                @csrf 
                @method('PUT') <div class="form-section-title">1. Informasi Utama</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Tempat</label>
                        <input type="text" name="nama_tempat" class="form-input" value="{{ $wisata->nama_tempat }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Tiket (Rp)</label>
                        <input type="number" name="harga_tiket" class="form-input" value="{{ $wisata->harga_tiket }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="margin-bottom: 12px;">Pilih Kategori</label>
                    <div class="cat-grid">
                        @php
                            $listKategori = [
                                'Alam' => 'alam.png',
                                'Religi' => 'religi.png',
                                'Kuliner' => 'kuliner.png',
                                'Belanja' => 'belanja.png',
                                'Budaya' => 'budaya.png',
                                'Edukasi' => 'edukasi.png',
                                'Rekreasi' => 'rekreasi.png',
                                'Agrowisata' => 'agro.png'
                            ];
                        @endphp

                        @foreach($listKategori as $nama => $icon)
                        <label>
                            <input type="radio" name="kategori" value="{{ $nama }}" class="cat-input" 
                                {{ $wisata->kategori == $nama ? 'checked' : '' }}>
                            
                            <div class="cat-box">
                                <img src="{{ asset('icons/' . $icon) }}" class="cat-icon" alt="{{ $nama }}">
                                <span class="cat-label">{{ $nama }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-input" rows="3">{{ $wisata->deskripsi }}</textarea>
                </div>

                <div class="form-section-title">2. Foto Wisata</div>

                <div class="form-group upload-box">
                    @if($wisata->gambar)
                        <div style="margin-bottom: 20px; text-align: center;">
                            <label class="form-label" style="text-align: center;">Foto Saat Ini</label>
                            <img src="{{ Str::startsWith($wisata->gambar, 'http') ? $wisata->gambar : asset('storage/' . $wisata->gambar) }}" 
                                 alt="Foto Lama" 
                                 style="height: 150px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 2px solid white;">
                        </div>
                    @endif
                    
                    <label class="form-label" style="margin-bottom: 10px; color: #ea580c;">Ganti Foto (Opsional)</label>
                    <input type="file" name="gambar_file" class="form-input" accept="image/*" style="background: white; margin-bottom: 10px;">
                    
                    <div class="divider-text">--- ATAU GANTI URL ---</div>

                    <input type="url" name="gambar_url" class="form-input" placeholder="https://..." 
                           value="{{ Str::startsWith($wisata->gambar, 'http') ? $wisata->gambar : '' }}" style="background: white;">
                </div>

                <div class="form-section-title">3. Lokasi & Jadwal</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-input" value="{{ $wisata->latitude }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-input" value="{{ $wisata->longitude }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-input" rows="2">{{ $wisata->alamat }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Operasional</label>
                    <input type="text" name="jam_buka" class="form-input" value="{{ $wisata->jam_buka }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" style="margin-bottom: 10px;">Hari Buka</label>
                    <div class="checkbox-group">
                        @php
                            $hariTersimpan = explode(',', $wisata->hari_buka); 
                        @endphp
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                            <label>
                                <input type="checkbox" name="hari_buka[]" value="{{ $hari }}" 
                                    {{ in_array($hari, $hariTersimpan) ? 'checked' : '' }}>
                                <span class="checkbox-label">{{ $hari }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <hr style="margin: 40px 0; border:0; border-top:1px dashed #cbd5e1;">

                <div style="display: flex; gap: 15px;">
                    <a href="{{ route('admin.index') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">Batal</a>
                    <button type="submit" class="btn btn-primary" style="flex: 2; justify-content: center; background: linear-gradient(135deg, #f97316, #ea580c); box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);">
                         Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>