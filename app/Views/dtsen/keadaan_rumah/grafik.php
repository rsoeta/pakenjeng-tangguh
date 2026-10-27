<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .pie-graph-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
        padding: 24px 16px;
    }

    .pie-slice {
        transition: transform 0.25s ease, filter 0.25s ease;
        cursor: pointer;
    }

    .pie-slice:hover {
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.25)) brightness(1.06);
        transform: scale(1.02);
        transform-origin: 500px 280px;
    }

    .callout-group {
        transition: opacity 0.2s, transform 0.2s;
    }

    .callout-group:hover {
        opacity: 1 !important;
        transform: translateY(-2px);
    }

    .stat-pill {
        border-radius: 30px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        background: #fff;
    }

    /* Tombol download / print */
    .btn-action-pill {
        border-radius: 20px;
        font-size: 0.85rem;
        padding: 5px 14px;
    }

    /* 🔧 Hilangkan jarak jomplang antara navbar dan content-header */
    .content-wrapper {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    .content-header {
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
    }
</style>

<div class="content-wrapper mt-0 pt-0 px-2 px-md-3 pb-3">
    <?= view('dtsen/keadaan_rumah/layout_nav'); ?>

    <!-- ========================================== -->
    <!-- 1. TOOLBAR FILTER & SUMMARY               -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Filter Wilayah -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <label class="small fw-bold text-muted mb-0"><i class="fas fa-filter me-1"></i>Filter Wilayah:</label>
                    <select class="form-select form-select-sm" id="filter_rw" style="width: 150px;">
                        <option value="all"><?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RW Tugas' : 'Semua RW' ?></option>
                        <?php foreach ($list_rw as $rw): ?>
                            <option value="<?= esc($rw) ?>">RW <?= esc($rw) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select class="form-select form-select-sm" id="filter_rt" style="width: 140px;" disabled>
                        <option value="all"><?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?></option>
                    </select>

                    <button class="btn btn-sm btn-outline-secondary" id="btnResetFilter" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>

                <!-- Info Total & Aksi Unduh -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="stat-pill border text-primary">
                        <i class="fas fa-users"></i>
                        <span>Total: <strong id="lblTotalKk"><?= number_format($stats['total']) ?></strong> KK</span>
                    </span>

                    <?php if (!empty($keterangan_wilayah)): ?>
                    <span class="stat-pill border text-dark d-none d-md-inline-flex" title="Cakupan Wilayah">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <span class="small fw-bold"><?= esc($keterangan_wilayah) ?></span>
                    </span>
                    <?php endif; ?>

                    <button type="button" class="btn btn-sm btn-outline-primary btn-action-pill" id="btnDownloadSvg" title="Unduh Infografis sebagai Gambar PNG">
                        <i class="fas fa-download me-1"></i> Unduh Grafik
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-action-pill" onclick="window.print()" title="Cetak Halaman">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. CANVAS PIE GRAPH IDENTIK TEMPLATE      -->
    <!-- ========================================== -->
    <div class="pie-graph-container" id="printablePieGraph">
        <svg id="svgPieTemplate" viewBox="0 0 1000 560" style="width: 100%; height: auto; display: block; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;">
            <defs>
                <!-- Shadow Filter -->
                <filter id="shadow" x="-10%" y="-10%" width="125%" height="125%">
                    <feDropShadow dx="0" dy="4" stdDeviation="6" flood-opacity="0.15" />
                </filter>
                <filter id="softGlow" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="3" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
            </defs>

            <!-- ========================================== -->
            <!-- JUDUL BESAR IDENTIK ATAS                   -->
            <!-- ========================================== -->
            <text x="500" y="55" text-anchor="middle" font-size="34" font-family="Georgia, 'Times New Roman', serif" font-weight="normal" fill="#1f2937">
                Pie Graph Keadaan Rumah
            </text>
            <text x="500" y="80" text-anchor="middle" font-size="13" font-family="'Segoe UI', sans-serif" fill="#6b7280" font-style="italic">
                Distribusi Kategori Keadaan Rumah &amp; Tempat Tinggal Keluarga (DTSEN)
            </text>
            <?php if (!empty($keterangan_wilayah)): ?>
            <!-- Keterangan Wilayah Kerja Sesuai Role (RT, RW, Desa) -->
            <text x="500" y="103" text-anchor="middle" font-size="12" font-family="'Segoe UI', Roboto, sans-serif" fill="#4b5563">
                <tspan font-weight="bold" fill="#0d6efd"><?= ($user_login['role_id'] ?? 0) >= 4 ? 'Wilayah Kerja:' : 'Wilayah Desa:' ?></tspan>
                <tspan font-weight="600" fill="#1e293b" id="txtWilayahKerja"> <?= esc($keterangan_wilayah) ?></tspan>
            </text>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- 5 WEDGE SLICES DI TENGAH (DINAMIS SESUAI PERSENTASE DATA) -->
            <!-- Center: (500, 280), Radius: 135, Gap: 6 -->
            <!-- ========================================== -->

            <!-- 01: TEAL (#169B88) - PERMANEN -->
            <g class="pie-slice" id="slice01" onclick="filterAndGo('Permanen')" style="cursor: pointer;">
                <path id="pathSlice01" d="M 494 274 L 494 145 A 130 130 0 0 0 376 230 Z" fill="#169B88" />
                <text id="textSlice01" x="446" y="215" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">01</text>
            </g>

            <!-- 02: BLUE (#3D82A9) - SEMI PERMANEN -->
            <g class="pie-slice" id="slice02" onclick="filterAndGo('Semi Permanen')" style="cursor: pointer;">
                <path id="pathSlice02" d="M 506 274 L 506 145 A 130 130 0 0 1 624 230 Z" fill="#3D82A9" />
                <text id="textSlice02" x="554" y="215" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">02</text>
            </g>

            <!-- 03: OLIVE GREEN (#9EAF22) - PANGGUNG -->
            <g class="pie-slice" id="slice03" onclick="filterAndGo('Panggung')" style="cursor: pointer;">
                <path id="pathSlice03" d="M 506 284 L 626 238 A 130 130 0 0 1 546 408 Z" fill="#9EAF22" />
                <text id="textSlice03" x="568" y="325" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">03</text>
            </g>

            <!-- 04: RED-ORANGE (#D84F2A) - NUMPANG -->
            <g class="pie-slice" id="slice04" onclick="filterAndGo('Numpang')" style="cursor: pointer;">
                <path id="pathSlice04" d="M 498 288 L 536 414 A 130 130 0 0 1 426 396 Z" fill="#D84F2A" />
                <text id="textSlice04" x="484" y="375" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">04</text>
            </g>

            <!-- 05: WARM ORANGE (#F59100) - BELUM DINILAI -->
            <g class="pie-slice" id="slice05" onclick="filterAndGo('belum_dinilai')" style="cursor: pointer;">
                <path id="pathSlice05" d="M 492 282 L 420 388 A 130 130 0 0 1 370 238 Z" fill="#F59100" />
                <text id="textSlice05" x="420" y="315" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">05</text>
            </g>

            <!-- ======================================================== -->
            <!-- CONNECTOR LINES & CALLOUTS RELEVAN DENGAN IKON BARU      -->
            <!-- ======================================================== -->

            <!-- ========================================== -->
            <!-- CALLOUT 01 (TOP-LEFT) - TEAL #169B88       -->
            <!-- IKON: RUMAH TEMBOK KOKOH PERMANEN          -->
            <!-- ========================================== -->
            <g class="callout-group" id="callout01">
                <!-- Connector Line -->
                <polyline id="poly01" points="414,160 414,152 135,152" fill="none" stroke="#169B88" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="106" cy="152" r="28" fill="#169B88" filter="url(#shadow)" />
                <!-- Icon: Rumah Kokoh Tembok Bertingkat / Cerobong -->
                <g transform="translate(92, 138) scale(0.052)" fill="#ffffff">
                    <path d="M543.8 287.6c17 0 32-14 32-31.2 0-8.9-3.7-17.5-10.2-23.7L297.4 6.7c-4.7-4.5-11-7-17.5-7s-12.8 2.5-17.5 7L5.8 232.7c-6.5 6.2-10.2 14.8-10.2 23.7 0 17.2 15 31.2 32 31.2 8.4 0 16.4-3.4 22.4-9.4L64 265.1V448c0 35.3 28.7 64 64 64h96c17.7 0 32-14.3 32-32V384h48v96c0 17.7 14.3 32 32 32h96c35.3 0 64-28.7 64-64V265.1l14 13.1c6 6 14 9.4 22.4 9.4zM416 96V56c0-4.4 3.6-8 8-8h32c4.4 0 8 3.6 8 8v80l-48-40z"/>
                </g>
                <!-- Title & Text -->
                <text x="160" y="142" font-size="16" font-family="Georgia, serif" font-weight="bold" fill="#111827">Permanen</text>
                <text x="160" y="172" font-size="12" fill="#4b5563">
                    <tspan x="160" dy="0" font-weight="bold" fill="#169B88" id="txtCount01"><?= number_format($stats['permanen']['count']) ?> KK (<?= $stats['permanen']['percent'] ?>%)</tspan>
                    <tspan x="160" dy="16" fill="#6b7280">Dinding tembok, lantai semen/keramik/ubin,</tspan>
                    <tspan x="160" dy="14" fill="#6b7280">dan atap genteng/seng/beton/asbes.</tspan>
                </text>
            </g>

            <!-- ========================================== -->
            <!-- CALLOUT 05 (MIDDLE-LEFT) - ORANGE #F59100  -->
            <!-- IKON: PAPAN SURVEI SENSUS BERTANDA TANYA   -->
            <!-- ========================================== -->
            <g class="callout-group" id="callout05">
                <!-- Connector Line -->
                <polyline id="poly05" points="370,285 135,285" fill="none" stroke="#F59100" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="106" cy="285" r="28" fill="#F59100" filter="url(#shadow)" />
                <!-- Icon: Clipboard Question / Belum Dinilai -->
                <g transform="translate(93, 270) scale(0.06)" fill="#ffffff">
                    <path d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H96zM208 48a16 16 0 1 1 32 0 16 16 0 1 1 -32 0zm32 128c35.3 0 64 28.7 64 64c0 23.7-12.9 44.4-32 55.4V304c0 8.8-7.2 16-16 16s-16-7.2-16-16V288c0-17.7 14.3-32 32-32c17.7 0 32-14.3 32-32s-14.3-32-32-32s-32 14.3-32 32c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-35.3 28.7-64 64-64zm0 192a20 20 0 1 1 0 40 20 20 0 1 1 0-40z"/>
                </g>
                <!-- Title & Text -->
                <text x="160" y="275" font-size="16" font-family="Georgia, serif" font-weight="bold" fill="#111827">Belum Dinilai</text>
                <text x="160" y="305" font-size="12" fill="#4b5563">
                    <tspan x="160" dy="0" font-weight="bold" fill="#F59100" id="txtCount05"><?= number_format($stats['belum_dinilai']['count']) ?> KK (<?= $stats['belum_dinilai']['percent'] ?>%)</tspan>
                    <tspan x="160" dy="16" fill="#6b7280">Data kondisi fisik rumah belum terisi</tspan>
                    <tspan x="160" dy="14" fill="#6b7280">lengkap atau menunggu survei sensus.</tspan>
                </text>
            </g>

            <!-- ========================================== -->
            <!-- CALLOUT 04 (BOTTOM-LEFT) - CORAL #D84F2A   -->
            <!-- IKON: BANYAK ORANG DI BAWAH SATU ATAP      -->
            <!-- ========================================== -->
            <g class="callout-group" id="callout04">
                <!-- Connector Line -->
                <polyline id="poly04" points="496,400 496,432 135,432" fill="none" stroke="#D84F2A" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="106" cy="432" r="28" fill="#D84F2A" filter="url(#shadow)" />
                <!-- Icon: People Roof (Menumpang Keluarga / Tinggal Bersama) -->
                <g transform="translate(89, 417) scale(0.054)" fill="#ffffff">
                    <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V42.7L22.2 165.7c-13.8 8.6-18 26.8-9.4 40.5s26.8 18 40.5 9.4L256 94.6l202.7 121c13.7 8.6 31.9 4.4 40.5-9.4s4.4-31.9-9.4-40.5L320 42.7V32zM64 288c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm0 32c-26.5 0-48 21.5-48 48v80c0 8.8 7.2 16 16 16h64c8.8 0 16-7.2 16-16V368c0-26.5-21.5-48-48-48zm128-64c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm-32 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48v128c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V368zm192-80c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm-16 64c-26.5 0-48 21.5-48 48v80c0 8.8 7.2 16 16 16h64c8.8 0 16-7.2 16-16V400c0-26.5-21.5-48-48-48z"/>
                </g>
                <!-- Title & Text -->
                <text x="160" y="422" font-size="16" font-family="Georgia, serif" font-weight="bold" fill="#111827">Numpang</text>
                <text x="160" y="452" font-size="12" fill="#4b5563">
                    <tspan x="160" dy="0" font-weight="bold" fill="#D84F2A" id="txtCount04"><?= number_format($stats['numpang']['count']) ?> KK (<?= $stats['numpang']['percent'] ?>%)</tspan>
                    <tspan x="160" dy="16" fill="#6b7280">Bebas sewa, menumpang di rumah kerabat,</tspan>
                    <tspan x="160" dy="14" fill="#6b7280">orang tua, atau tinggal bersama KK lain.</tspan>
                </text>
            </g>

            <!-- ========================================== -->
            <!-- CALLOUT 02 (TOP-RIGHT) - BLUE #3D82A9      -->
            <!-- IKON: BATA & SENDOK SEMEN PERTUKANGAN      -->
            <!-- ========================================== -->
            <g class="callout-group" id="callout02">
                <!-- Connector Line -->
                <polyline id="poly02" points="586,160 586,152 865,152" fill="none" stroke="#3D82A9" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="894" cy="152" r="28" fill="#3D82A9" filter="url(#shadow)" />
                <!-- Icon: Trowel Bricks / Konstruksi Campuran & Perbaikan -->
                <g transform="translate(880, 138) scale(0.054)" fill="#ffffff">
                    <path d="M416 0C400 0 384.8 8.4 376.1 22.1L288 160.8V96c0-17.7-14.3-32-32-32H32C14.3 64 0 78.3 0 96v96c0 17.7 14.3 32 32 32h224c17.7 0 32-14.3 32-32v-1.1l77.9 123.5c11.6 18.4 32.1 29.6 54.1 29.6c35.3 0 64-28.7 64-64c0-8.5-1.7-16.7-4.7-24.1l-64-160C440.6 7.4 429 0 416 0zM32 288c-17.7 0-32 14.3-32 32v96c0 17.7 14.3 32 32 32h160c17.7 0 32-14.3 32-32V320c0-17.7-14.3-32-32-32H32zm224 0c-17.7 0-32 14.3-32 32v96c0 17.7 14.3 32 32 32h224c17.7 0 32-14.3 32-32V320c0-17.7-14.3-32-32-32H256z"/>
                </g>
                <!-- Title & Text (Right-aligned feel) -->
                <text x="630" y="142" font-size="16" font-family="Georgia, serif" font-weight="bold" fill="#111827">Semi Permanen</text>
                <text x="630" y="172" font-size="12" fill="#4b5563">
                    <tspan x="630" dy="0" font-weight="bold" fill="#3D82A9" id="txtCount02"><?= number_format($stats['semi_permanen']['count']) ?> KK (<?= $stats['semi_permanen']['percent'] ?>%)</tspan>
                    <tspan x="630" dy="16" fill="#6b7280">Konstruksi campuran (setengah tembok,</tspan>
                    <tspan x="630" dy="14" fill="#6b7280">bilik bambu tapak semen, plesteran/GRC).</tspan>
                </text>
            </g>

            <!-- ========================================== -->
            <!-- CALLOUT 03 (BOTTOM-RIGHT) - OLIVE #9EAF22  -->
            <!-- IKON: RUMAH PANGGUNG BERTIANG KOLONG       -->
            <!-- ========================================== -->
            <g class="callout-group" id="callout03">
                <!-- Connector Line -->
                <polyline id="poly03" points="586,400 586,432 865,432" fill="none" stroke="#9EAF22" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="894" cy="432" r="28" fill="#9EAF22" filter="url(#shadow)" />
                <!-- Icon: Rumah Panggung Bertiang Kolong Khas Nusantara -->
                <g transform="translate(879, 417)" fill="#ffffff">
                    <!-- Atap Rumah Tradisional Panggung -->
                    <path d="M15 2 L1 12 L3.5 13 L15 4.5 L26.5 13 L29 12 Z" />
                    <!-- Dinding Kayu Atas -->
                    <rect x="5" y="10" width="20" height="8" rx="0.5" />
                    <!-- Pintu & Jendela -->
                    <rect x="12.5" y="11" width="5" height="7" fill="#9EAF22" rx="0.3" />
                    <rect x="7" y="11.5" width="3.5" height="3.5" fill="#9EAF22" rx="0.3" />
                    <rect x="19.5" y="11.5" width="3.5" height="3.5" fill="#9EAF22" rx="0.3" />
                    <!-- Lantai Panggung Kayu Tebal -->
                    <rect x="3" y="18" width="24" height="2.5" rx="0.6" />
                    <!-- 4 Tiang Kolong Rumah Panggung -->
                    <rect x="5.5" y="20.5" width="2" height="8.5" rx="0.4" />
                    <rect x="11" y="20.5" width="2" height="8.5" rx="0.4" />
                    <rect x="17" y="20.5" width="2" height="8.5" rx="0.4" />
                    <rect x="22.5" y="20.5" width="2" height="8.5" rx="0.4" />
                    <!-- Garis Tanah / Dasar -->
                    <line x1="2" y1="29.5" x2="28" y2="29.5" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round" />
                </g>
                <!-- Title & Text -->
                <text x="630" y="422" font-size="16" font-family="Georgia, serif" font-weight="bold" fill="#111827">Panggung</text>
                <text x="630" y="452" font-size="12" fill="#4b5563">
                    <tspan x="630" dy="0" font-weight="bold" fill="#9EAF22" id="txtCount03"><?= number_format($stats['panggung']['count']) ?> KK (<?= $stats['panggung']['percent'] ?>%)</tspan>
                    <tspan x="630" dy="16" fill="#6b7280">Konstruksi bertiang/kolong dengan</tspan>
                    <tspan x="630" dy="14" fill="#6b7280">lantai kayu atau bambu (palupuh).</tspan>
                </text>
            </g>
        </svg>
    </div>
</div>

<script>
    // Navigasi cepat ke data tabel terfilter
    function filterAndGo(kategori) {
        const rw = $('#filter_rw').val();
        const rt = $('#filter_rt').val();
        let url = '<?= base_url('dtsen/keadaan-rumah') ?>?kategori=' + encodeURIComponent(kategori);
        if (rw && rw !== 'all') url += '&rw=' + encodeURIComponent(rw);
        if (rt && rt !== 'all') url += '&rt=' + encodeURIComponent(rt);
        window.location.href = url;
    }

    $(document).ready(function() {
        // Filter RW change
        $('#filter_rw').on('change', function() {
            const rw = $(this).val();
            const $rt = $('#filter_rt');
            const defaultRtText = '<?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?>';
            $rt.empty().append('<option value="all">' + defaultRtText + '</option>');

            if (rw && rw !== 'all') {
                $.getJSON('<?= base_url('dtsen/keadaan-rumah/rt-by-rw') ?>/' + encodeURIComponent(rw), function(res) {
                    if (res && res.length > 0) {
                        res.forEach(item => {
                            $rt.append(`<option value="${item.rt}">RT ${item.rt}</option>`);
                        });
                        $rt.prop('disabled', false);
                    } else {
                        $rt.prop('disabled', true);
                    }
                    loadGrafikData();
                });
            } else {
                $rt.prop('disabled', true);
                loadGrafikData();
            }
        });

        $('#filter_rt').on('change', function() {
            loadGrafikData();
        });

        $('#btnResetFilter').on('click', function() {
            const defaultRtText = '<?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?>';
            $('#filter_rw').val('all');
            $('#filter_rt').empty().append('<option value="all">' + defaultRtText + '</option>').prop('disabled', true);
            loadGrafikData();
        });

                const initialStats = <?= json_encode($stats) ?>;

        // ==============================================================
        // 🥧 FUNGSI RENDER POTONGAN PIE DIAGRAM SECARA DINAMIS & MATEMATIS
        // ==============================================================
        function renderPieGraph(data) {
            if (!data) return;

            const total = Number(data.total) || 0;
            const order = [
                { id: '02', key: 'semi_permanen' },
                { id: '03', key: 'panggung' },
                { id: '04', key: 'numpang' },
                { id: '05', key: 'belum_dinilai' },
                { id: '01', key: 'permanen' }
            ];

            const cx0 = 500;
            const cy0 = 280;
            const R   = 135;
            const gap = 6; // Jarak detached potongan ke arah sudut tengah

            let currentAngle = -Math.PI / 2; // Mulai dari puncak jam 12 (-90 deg)

            order.forEach(item => {
                const catData = data[item.key] || { count: 0, percent: 0 };
                const count = Number(catData.count) || 0;
                const fraction = (total > 0) ? (count / total) : 0;
                const delta = fraction * 2 * Math.PI;

                const startAngle = currentAngle;
                const endAngle   = currentAngle + delta;
                const midAngle   = startAngle + delta / 2;
                currentAngle     = endAngle;

                const $slice = $(`#slice${item.id}`);
                const $path  = $(`#pathSlice${item.id}`);
                const $text  = $(`#textSlice${item.id}`);
                const $poly  = $(`#poly${item.id}`);

                if (fraction <= 0) {
                    $slice.hide();
                    $poly.css('opacity', '0.15');
                    return;
                }

                $slice.show();
                $poly.css('opacity', '1');

                const cx = cx0 + gap * Math.cos(midAngle);
                const cy = cy0 + gap * Math.sin(midAngle);

                const x1 = cx + R * Math.cos(startAngle);
                const y1 = cy + R * Math.sin(startAngle);
                const x2 = cx + R * Math.cos(endAngle);
                const y2 = cy + R * Math.sin(endAngle);

                const largeArc = delta > Math.PI ? 1 : 0;
                const pathD = `M ${cx.toFixed(1)} ${cy.toFixed(1)} L ${x1.toFixed(1)} ${y1.toFixed(1)} A ${R} ${R} 0 ${largeArc} 1 ${x2.toFixed(1)} ${y2.toFixed(1)} Z`;

                $path.attr('d', pathD);

                // Posisi teks nomor ("01", "02", ...)
                const rText = fraction < 0.08 ? R * 0.72 : R * 0.62;
                const tx = cx + rText * Math.cos(midAngle);
                const ty = cy + rText * Math.sin(midAngle) + 9;
                const fontSize = fraction < 0.04 ? 14 : (fraction < 0.08 ? 18 : (fraction < 0.15 ? 22 : 26));

                $text.attr('x', tx.toFixed(1))
                     .attr('y', ty.toFixed(1))
                     .attr('font-size', fontSize);

                // Posisi garis penghubung (connector line) dari tepi luar busur
                const edgeX = cx + (R + 4) * Math.cos(midAngle);
                const edgeY = cy + (R + 4) * Math.sin(midAngle);

                let polyPoints = '';
                if (item.id === '01') {
                    // Callout 01 (Kiri Atas): target (135, 152)
                    const bendX = Math.min(Math.max(edgeX - 10, 180), 414);
                    polyPoints = `${edgeX.toFixed(1)},${edgeY.toFixed(1)} ${bendX.toFixed(1)},152 135,152`;
                } else if (item.id === '02') {
                    // Callout 02 (Kanan Atas): target (865, 152)
                    const bendX = Math.max(Math.min(edgeX + 10, 820), 586);
                    polyPoints = `${edgeX.toFixed(1)},${edgeY.toFixed(1)} ${bendX.toFixed(1)},152 865,152`;
                } else if (item.id === '03') {
                    // Callout 03 (Kanan Bawah): target (865, 432)
                    const bendX = Math.max(Math.min(edgeX + 10, 820), 586);
                    polyPoints = `${edgeX.toFixed(1)},${edgeY.toFixed(1)} ${bendX.toFixed(1)},432 865,432`;
                } else if (item.id === '04') {
                    // Callout 04 (Kiri Bawah): target (135, 432)
                    const bendX = Math.min(Math.max(edgeX - 10, 180), 496);
                    polyPoints = `${edgeX.toFixed(1)},${edgeY.toFixed(1)} ${bendX.toFixed(1)},432 135,432`;
                } else if (item.id === '05') {
                    // Callout 05 (Kiri Tengah): target (135, 285)
                    const bendX = Math.min(Math.max(edgeX - 15, 180), 370);
                    polyPoints = `${edgeX.toFixed(1)},${edgeY.toFixed(1)} ${bendX.toFixed(1)},285 135,285`;
                }

                if (polyPoints) {
                    $poly.attr('points', polyPoints);
                }
            });
        }

        function loadGrafikData() {
            const rw = $('#filter_rw').val();
            const rt = $('#filter_rt').val();

            $.getJSON('<?= base_url('dtsen/keadaan-rumah/grafik-data') ?>', {
                rw: rw,
                rt: rt
            }, function(data) {
                if (data) {
                    $('#lblTotalKk').text(Number(data.total).toLocaleString('id-ID'));

                    $('#txtCount01').text(`${Number(data.permanen.count).toLocaleString('id-ID')} KK (${data.permanen.percent}%)`);
                    $('#txtCount02').text(`${Number(data.semi_permanen.count).toLocaleString('id-ID')} KK (${data.semi_permanen.percent}%)`);
                    $('#txtCount03').text(`${Number(data.panggung.count).toLocaleString('id-ID')} KK (${data.panggung.percent}%)`);
                    $('#txtCount04').text(`${Number(data.numpang.count).toLocaleString('id-ID')} KK (${data.numpang.percent}%)`);
                    $('#txtCount05').text(`${Number(data.belum_dinilai.count).toLocaleString('id-ID')} KK (${data.belum_dinilai.percent}%)`);

                    // 🚀 HIDUPKAN POTONGAN DIAGRAM SECARA DINAMIS
                    renderPieGraph(data);
                }
            });
        }

        // Inisialisasi awal potongan diagram pie sesuai data pertama kali
        renderPieGraph(initialStats);

        // Unduh sebagai PNG
        $('#btnDownloadSvg').on('click', function() {
            const svgElement = document.getElementById('svgPieTemplate');
            const svgString = new XMLSerializer().serializeToString(svgElement);
            const svgBlob = new Blob([svgString], {
                type: 'image/svg+xml;charset=utf-8'
            });
            const URL = window.URL || window.webkitURL || window;
            const blobURL = URL.createObjectURL(svgBlob);

            const image = new Image();
            image.onload = function() {
                const canvas = document.createElement('canvas');
                canvas.width = 2000; // High-res (2x scale)
                canvas.height = 1120;
                const context = canvas.getContext('2d');
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, canvas.width, canvas.height);
                context.drawImage(image, 0, 0, canvas.width, canvas.height);

                const png = canvas.toDataURL('image/png');
                const downloadLink = document.createElement('a');
                downloadLink.download = 'Grafik_Keadaan_Rumah_' + (new Date().toISOString().slice(0, 10)) + '.png';
                downloadLink.href = png;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            };
            image.src = blobURL;
        });
    });
</script>

<?= $this->endSection(); ?>