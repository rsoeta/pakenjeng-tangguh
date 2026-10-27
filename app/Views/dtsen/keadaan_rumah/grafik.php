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
            <!-- 5 WEDGE SLICES DI TENGAH                   -->
            <!-- Center: (500, 280), Radius: 125, Gap: 6   -->
            <!-- ========================================== -->

            <!-- 01: TEAL (#169B88) - TOP-LEFT -->
            <g class="pie-slice" id="slice01" onclick="filterAndGo('Permanen')">
                <path d="M 494 274 L 494 145 A 130 130 0 0 0 376 230 Z" fill="#169B88" />
                <text x="446" y="215" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">01</text>
            </g>

            <!-- 02: BLUE (#3D82A9) - TOP-RIGHT -->
            <g class="pie-slice" id="slice02" onclick="filterAndGo('Semi Permanen')">
                <path d="M 506 274 L 506 145 A 130 130 0 0 1 624 230 Z" fill="#3D82A9" />
                <text x="554" y="215" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">02</text>
            </g>

            <!-- 03: OLIVE GREEN (#9EAF22) - BOTTOM-RIGHT -->
            <g class="pie-slice" id="slice03" onclick="filterAndGo('Panggung')">
                <path d="M 506 284 L 626 238 A 130 130 0 0 1 546 408 Z" fill="#9EAF22" />
                <text x="568" y="325" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">03</text>
            </g>

            <!-- 04: RED-ORANGE (#D84F2A) - BOTTOM-LEFT -->
            <g class="pie-slice" id="slice04" onclick="filterAndGo('Numpang')">
                <path d="M 498 288 L 536 414 A 130 130 0 0 1 426 396 Z" fill="#D84F2A" />
                <text x="484" y="375" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">04</text>
            </g>

            <!-- 05: WARM ORANGE (#F59100) - MIDDLE-LEFT -->
            <g class="pie-slice" id="slice05" onclick="filterAndGo('belum_dinilai')">
                <path d="M 492 282 L 420 388 A 130 130 0 0 1 370 238 Z" fill="#F59100" />
                <text x="420" y="315" text-anchor="middle" font-size="28" font-family="Georgia, serif" font-weight="bold" fill="#ffffff">05</text>
            </g>

            <!-- ======================================================== -->
            <!-- CONNECTOR LINES & CALLOUTS IDENTIK GAMBAR                -->
            <!-- ======================================================== -->

            <!-- ========================================== -->
            <!-- CALLOUT 01 (TOP-LEFT) - TEAL #169B88       -->
            <!-- ========================================== -->
            <g class="callout-group" id="callout01">
                <!-- Connector Line -->
                <polyline points="414,160 414,152 135,152" fill="none" stroke="#169B88" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="106" cy="152" r="28" fill="#169B88" filter="url(#shadow)" />
                <!-- Icon Briefcase / Building (SVG Path) -->
                <g transform="translate(94, 140) scale(0.048)" fill="#ffffff">
                    <path d="M128 0C92.7 0 64 28.7 64 64v32H16C7.2 96 0 103.2 0 112v256c0 8.8 7.2 16 16 16h480c8.8 0 16-7.2 16-16V112c0-8.8-7.2-16-16-16h-48V64c0-35.3-28.7-64-64-64H128zm32 64h192v32H160V64zm-96 96h384v64H64v-64zm0 128h384v128H64V288z" />
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
            <!-- ========================================== -->
            <g class="callout-group" id="callout05">
                <!-- Connector Line -->
                <polyline points="370,285 135,285" fill="none" stroke="#F59100" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="106" cy="285" r="28" fill="#F59100" filter="url(#shadow)" />
                <!-- Icon Paper Plane (SVG Path) -->
                <g transform="translate(93, 272) scale(0.052)" fill="#ffffff">
                    <path d="M16.1 260.2c-22.6 12.9-20.5 47.3 3.6 57.3L160 376v104a31.9 31.9 0 0 0 54.4 22.6l64.3-64.3 124.6 74.8c21.2 12.7 48.7 1.4 54.6-22.5L511.4 29.5c6-24.1-17.7-44.5-40.9-35.3L16.1 260.2zM432 96L213.3 314.7 186.7 288 432 96z" />
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
            <!-- ========================================== -->
            <g class="callout-group" id="callout04">
                <!-- Connector Line -->
                <polyline points="496,400 496,432 135,432" fill="none" stroke="#D84F2A" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="106" cy="432" r="28" fill="#D84F2A" filter="url(#shadow)" />
                <!-- Icon Bar Chart (SVG Path) -->
                <g transform="translate(94, 420) scale(0.048)" fill="#ffffff">
                    <path d="M64 480c-17.7 0-32-14.3-32-32V320c0-17.7 14.3-32 32-32s32 14.3 32 32v128c0 17.7-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32s32 14.3 32 32v288c0 17.7-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32V32c0-17.7 14.3-32 32-32s32 14.3 32 32v416c0 17.7-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32V224c0-17.7 14.3-32 32-32s32 14.3 32 32v224c0 17.7-14.3 32-32 32z" />
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
            <!-- ========================================== -->
            <g class="callout-group" id="callout02">
                <!-- Connector Line -->
                <polyline points="586,160 586,152 865,152" fill="none" stroke="#3D82A9" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="894" cy="152" r="28" fill="#3D82A9" filter="url(#shadow)" />
                <!-- Icon Cog / Gear (SVG Path) -->
                <g transform="translate(881, 139) scale(0.05)" fill="#ffffff">
                    <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6l-43.3 119.2c-3.2 8.7-11.8 14.3-21.2 13.5l-58-4.8c-12 9.5-25.5 17.3-40.1 23.1l-14.8 56.4c-2.4 9-10.4 15.3-19.8 15.3H198.8c-9.4 0-17.4-6.3-19.8-15.3l-14.8-56.4c-14.6-5.8-28.1-13.6-40.1-23.1l-58 4.8c-9.4 .8-18-4.8-21.2-13.5L1.5 305.4c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C50.1 233.1 49.5 224.6 49.5 216s.6-17.1 1.7-25.4L7.9 151.2C1 145-1.7 135.3 1.5 126.6L44.8 7.4C48-1.3 56.6-6.9 66-6.1l58 4.8c12-9.5 25.5-17.3 40.1-23.1l14.8-56.4c2.4-9 10.4-15.3 19.8-15.3h114.4c9.4 0 17.4 6.3 19.8 15.3l14.8 56.4c14.6 5.8 28.1 13.6 40.1 23.1l58-4.8c9.4-.8 18 4.8 21.2 13.5l43.3 119.2zM256 160a96 96 0 1 0 0 192 96 96 0 1 0 0-192z" />
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
            <!-- ========================================== -->
            <g class="callout-group" id="callout03">
                <!-- Connector Line -->
                <polyline points="586,400 586,432 865,432" fill="none" stroke="#9EAF22" stroke-width="2" />
                <!-- Circular Icon Badge -->
                <circle cx="894" cy="432" r="28" fill="#9EAF22" filter="url(#shadow)" />
                <!-- Icon Target / Bullseye (SVG Path) -->
                <g transform="translate(881, 419) scale(0.05)" fill="#ffffff">
                    <path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256 256-114.6 256-256S397.4 0 256 0zm0 464c-114.7 0-208-93.3-208-208S141.3 48 256 48s208 93.3 208 208-93.3 208-208 208zm0-352c-79.5 0-144 64.5-144 144s64.5 144 144 144 144-64.5 144-144-64.5-144-144-144zm0 224c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z" />
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
                }
            });
        }

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