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
                        <img src="{{ Str::startsWith($w->gambar, ['http', 'data:']) ? $w->gambar : asset($w->gambar) }}"
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
                            <a href="{{ route('admin.edit', $w->id) }}" class="action-btn edit" title="Edit">
                                <i class="ri-pencil-line"></i>
                            </a>

                            <form id="delete-form-{{ $w->id }}" action="{{ route('admin.destroy', $w->id) }}"
                                method="POST" style="display:none;">
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
