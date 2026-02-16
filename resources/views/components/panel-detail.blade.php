<div id="detailPanel" class="detail-panel">

    <div class="panel-image-wrapper">
        <div class="carousel-wrapper">
            <div id="carouselTrack" class="carousel-track"></div>
            <button class="carousel-btn left" onclick="prevImage()">‹</button>
            <button class="carousel-btn right" onclick="nextImage()">›</button>
            <div id="carouselDots" class="carousel-dots"></div>
        </div>
        <div class="panel-overlay-gradient"></div>
        <button class="panel-close" onclick="closeDetailPanel()">×</button>
        <span id="panelKategori" class="panel-badge">Kategori</span>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="panel-tabs">
            <button class="tab-btn active" onclick="switchTab('info')">
                <i class="ri-information-fill"></i> Informasi
            </button>
            <button class="tab-btn" onclick="switchTab('ulasan')">
                <i class="ri-star-smile-fill"></i> Ulasan
            </button>
        </div>

        <div style="padding: 0 25px 25px 25px;">

            <div id="tabInfo" class="tab-content active">

                <div class="panel-header">
                    <h2 id="panelNama" class="panel-title">Nama Tempat</h2>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                        <p id="panelHarga" class="panel-price">Rp 0</p>
                        <div id="panelRating" class="panel-rating" onclick="switchTab('ulasan')"
                            style="cursor:pointer;">
                            ⭐ 0.0 (0)
                        </div>
                    </div>
                </div>

                <div id="panelFasilitas" class="panel-fasilitas-list"></div>
                <p id="panelAlamat" class="panel-address">Alamat lengkap...</p>

                <div class="panel-divider"></div>

                <div class="panel-nav">
                    <button onclick="prevWisata()" class="nav-btn">←</button>
                    <span class="panel-nav-text">Navigasi Rute</span>
                    <button onclick="nextWisata()" class="nav-btn">→</button>
                </div>

                <div class="panel-actions" style="margin-top: 15px;">
                    <button id="panelRuteBtn" class="btn-rute-hero" onclick="alert('Membuka Maps...')">
                        <i class="ri-direction-fill"></i> Buka Google Maps
                    </button>
                </div>
            </div>

            <div id="tabUlasan" class="tab-content">

                <div class="rating-box" style="margin-top: 0;">
                    <h4 style="font-size:15px; font-weight:700; margin-bottom:10px; text-align:center;">
                        Bagikan Pengalamanmu
                    </h4>

                    <form id="formReview" onsubmit="submitReview(event)">
                        <input type="hidden" id="reviewWisataId" name="wisata_id">

                        <div class="star-rating">
                            <input type="radio" name="rating" id="star5" value="5"><label
                                for="star5">★</label>
                            <input type="radio" name="rating" id="star4" value="4"><label
                                for="star4">★</label>
                            <input type="radio" name="rating" id="star3" value="3"><label
                                for="star3">★</label>
                            <input type="radio" name="rating" id="star2" value="2"><label
                                for="star2">★</label>
                            <input type="radio" name="rating" id="star1" value="1"><label
                                for="star1">★</label>
                        </div>

                        <textarea name="komentar" id="reviewKomentar" rows="3" class="form-input" placeholder="Tulis ulasan disini..."
                            style="width:100%; margin-top:10px; resize:none;"></textarea>

                        <button type="submit" class="btn-rute-hero"
                            style="margin-top:15px; background:#1e293b; width:100%; border:none;">
                            Kirim Ulasan
                        </button>
                    </form>
                </div>

                <div style="margin-top: 30px; margin-bottom: 50px;">
                    <h4 style="font-size:14px; font-weight:700; margin-bottom:15px; color:#334155;">
                        Ulasan Terbaru
                    </h4>
                    <div id="reviewContainer" class="review-list">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
