<div id="directoryModal" class="modal-overlay" style="display: none; z-index: 9999;">
    <div class="modal-content"
        style="background: white; width: 95%; max-width: 900px; height: 90vh; border-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">

        <div
            style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: white;">
            <div>
                <h3 style="margin: 0; font-size: 20px; color: #1e293b; font-weight: 800;">Jelajahi Wisata</h3>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">Temukan destinasi terbaik di
                    Banjarbakula</p>
            </div>
            <button onclick="window.closeDirectoryModal()"
                style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e2e8f0; background: white; color: #64748b; cursor: pointer; display:flex; align-items:center; justify-content:center; transition:0.2s;">✕</button>
        </div>

        <div class="dir-action-bar">
            <div class="search-pill">
                <i class="ri-search-line" style="color: #94a3b8;"></i>
                <input type="text" id="dirSearchInput" placeholder="Ketik nama tempat wisata..."
                    onkeyup="applyDirectoryFilter()">
            </div>
            <button id="btnDirFilter" class="btn-filter-toggle" onclick="toggleDirectoryFilters()"
                title="Filter Lanjutan">
                <i class="ri-equalizer-line"></i>
            </button>
        </div>

        <div id="dirFilterPanel" class="hidden-filter-panel">
            <div class="filter-grid">
                <div>
                    <label
                        style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">WILAYAH</label>
                    <select id="dirRegionSelect" class="form-select" onchange="applyDirectoryFilter()">
                        <option value="">Semua Wilayah</option>
                        <option value="Banjarmasin">Banjarmasin</option>
                        <option value="Banjarbaru">Banjarbaru</option>
                        <option value="Martapura">Kab. Banjar</option>
                        <option value="Barito Kuala">Barito Kuala</option>
                        <option value="Tanah Laut">Tanah Laut</option>
                    </select>
                </div>
                <div>
                    <label
                        style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">KATEGORI</label>
                    <select id="dirKategoriSelect" class="form-select" onchange="applyDirectoryFilter()">
                        <option value="">Semua Kategori</option>
                        <option value="Alam">Alam</option>
                        <option value="Religi">Religi</option>
                        <option value="Kuliner">Kuliner</option>
                        <option value="Belanja">Belanja</option>
                        <option value="Budaya">Budaya</option>
                        <option value="Rekreasi">Rekreasi</option>
                    </select>
                </div>

                <div>
                    <label
                        style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">BUDGET
                        MAX</label>
                    <select id="dirBudgetSelect" class="form-select" onchange="applyDirectoryFilter()">
                        <option value="">Semua Budget</option>
                        <option value="0">Gratis</option>
                        <option value="25000">
                            < 25rb</option>
                        <option value="50000">
                            < 50rb</option>
                        <option value="100000">
                            < 100rb</option>
                    </select>
                </div>
                <div>
                    <label
                        style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:5px; display:block;">RATING</label>
                    <select id="dirRatingSelect" class="form-select" onchange="applyDirectoryFilter()">
                        <option value="0">Semua Bintang</option>
                        <option value="1">⭐ 1+</option>
                        <option value="2">⭐ 2+</option>
                        <option value="3">⭐ 3+</option>
                        <option value="4">⭐ 4+</option>
                        <option value="5">⭐ 5</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="directoryList" style="flex: 1; overflow-y: auto;">
        </div>
    </div>
</div>
