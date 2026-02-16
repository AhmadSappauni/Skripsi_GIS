<div id="map" style="height: 100vh; width: 100%;"></div>

<div class="layer-control-wrapper">
    <button class="btn-layer-toggle" onclick="toggleLayerPanel()" title="Pengaturan Peta">
        <i class="ri-stack-line"></i> </button>

    <div class="layer-panel" id="layerPanel">

        <div class="panel-section">
            <div class="layer-header">Mode Peta</div>

            <div class="basemap-grid"> <label class="basemap-card" onclick="switchBaseMap('street')">
                    <input type="radio" name="basemap" checked>
                    <span class="basemap-icon"><i class="ri-map-2-line"></i></span>
                    <span class="basemap-label">Jalan</span>
                </label>

                <label class="basemap-card" onclick="switchBaseMap('satellite')">
                    <input type="radio" name="basemap">
                    <span class="basemap-icon"><i class="ri-earth-line"></i></span>
                    <span class="basemap-label">Satelit</span>
                </label>

                <label class="basemap-card" onclick="switchBaseMap('terrain')">
                    <input type="radio" name="basemap">
                    <span class="basemap-icon"><i class="ri-landscape-line"></i></span>
                    <span class="basemap-label">Terrain</span>
                </label>

            </div>
        </div>

        <hr class="panel-divider">

        <div class="panel-section">
            <div class="layer-header">
                <span>Filter Wilayah</span>
                <button onclick="resetLayers(false)" class="btn-xs-reset">Hide All</button>
            </div>

            <div class="layer-list">
                <label class="layer-item">
                    <span class="layer-name">Kota Banjarmasin</span>
                    <input type="checkbox" id="chk-Banjarmasin" checked
                        onchange="toggleSpecificLayer('Banjarmasin', this.checked)">
                    <span class="custom-toggle"></span>
                </label>
                <label class="layer-item">
                    <span class="layer-name">Kota Banjarbaru</span>
                    <input type="checkbox" id="chk-Banjarbaru" checked
                        onchange="toggleSpecificLayer('Banjarbaru', this.checked)">
                    <span class="custom-toggle"></span>
                </label>
                <label class="layer-item">
                    <span class="layer-name">Kab. Banjar</span>
                    <input type="checkbox" id="chk-Banjar" checked
                        onchange="toggleSpecificLayer('Banjar', this.checked)">
                    <span class="custom-toggle"></span>
                </label>
                <label class="layer-item">
                    <span class="layer-name">Tanah Laut</span>
                    <input type="checkbox" id="chk-TanahLaut" checked
                        onchange="toggleSpecificLayer('TanahLaut', this.checked)">
                    <span class="custom-toggle"></span>
                </label>
                <label class="layer-item">
                    <span class="layer-name">Barito Kuala</span>
                    <input type="checkbox" id="chk-Batola" checked
                        onchange="toggleSpecificLayer('Batola', this.checked)">
                    <span class="custom-toggle"></span>
                </label>
            </div>
        </div>
    </div>
</div>
