<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Monitoring Reminder WA</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- 🚀 6 KARTU SUMMARY (Grid Mobile 3 Kolom = col-4, Desktop 6 Kolom = col-md-2) -->
            <div class="row mb-3 px-2">
                <div class="col-4 col-md-2 mb-2 px-1">
                    <div class="card shadow-sm border-left-warning h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-xs fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Pending</h6>
                            <h5 id="cardPending" class="text-warning mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-2 mb-2 px-1">
                    <div class="card shadow-sm border-left-success h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-xs fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Sent Today</h6>
                            <h5 id="cardSentToday" class="text-success mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-2 mb-2 px-1">
                    <div class="card shadow-sm border-left-danger h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-xs fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Failed</h6>
                            <h5 id="cardFailed" class="text-danger mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-2 mb-2 px-1">
                    <div class="card shadow-sm border-left-primary h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-xs fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Due 1 Hour</h6>
                            <h5 id="cardNextHour" class="text-primary mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-2 mb-2 px-1">
                    <div class="card shadow-sm border-left-info h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-xs fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Total KK</h6>
                            <h5 id="cardTotalKK" class="text-info mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-2 mb-2 px-1">
                    <div class="card shadow-sm border-left-secondary h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-xs fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Total Jiwa</h6>
                            <h5 id="cardTotalART" class="text-secondary mb-0">0</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <!-- 🚀 TAMBAHAN FILTER RENTANG TANGGAL -->
                    <div class="row mb-3 g-2">
                        <div class="col-6 col-md-2">
                            <label class="small fw-bold">Mulai Tgl</label>
                            <input type="date" id="filterStartDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="small fw-bold">Sampai Tgl</label>
                            <input type="date" id="filterEndDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="small fw-bold">Status</label>
                            <select id="filterStatus" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="pending" selected>Pending</option>
                                <option value="sent">Sent</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold">Search</label>
                            <input id="filterQ" class="form-control form-control-sm" placeholder="No KK / Nama KK / Admin">
                        </div>
                        <div class="col-12 col-md-2 text-md-right mt-4 mt-md-0 d-flex align-items-end justify-content-md-end">
                            <button id="btnRefresh" class="btn btn-sm btn-secondary w-100 w-md-auto"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                    </div>

                    <table id="tblReminder" class="table table-bordered table-hover table-sm w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No KK</th>
                                <th>Nama KK</th>
                                <th>Status</th>
                                <th>Admin Desa</th>
                                <th>No. HP</th>
                                <th>Due Date</th>
                                <th>Sent At</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    const baseUrl = "<?= base_url() ?>";

    function loadSummary() {
        $.get(baseUrl + '/dtsen/reminder-monitor/summary', {
            start_date: $('#filterStartDate').val(),
            end_date: $('#filterEndDate').val()
        }, function(resp) {

            // 🚀 Inisialisasi Formatter Angka Ribuan ala Indonesia
            let formatRibuan = new Intl.NumberFormat('id-ID');

            $('#cardPending').text(formatRibuan.format(resp.pending));
            $('#cardSentToday').text(formatRibuan.format(resp.sent_today));
            $('#cardFailed').text(formatRibuan.format(resp.failed));
            $('#cardNextHour').text(formatRibuan.format(resp.due_next_hour));

            // 🚀 Format Ribuan Diterapkan pada KK & ART
            $('#cardTotalKK').text(formatRibuan.format(resp.total_kk));
            $('#cardTotalART').text(formatRibuan.format(resp.total_art));
        });
    }

    $(function() {

        loadSummary();

        const table = $('#tblReminder').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ajax: {
                url: baseUrl + '/dtsen/reminder-monitor/list',
                dataSrc: 'data',
                data: function(d) {
                    d.status = $('#filterStatus').val();
                    d.q = $('#filterQ').val();
                    d.start_date = $('#filterStartDate').val(); // 🚀 Lempar ke Backend
                    d.end_date = $('#filterEndDate').val(); // 🚀 Lempar ke Backend
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                // 🔹 No KK
                {
                    data: 'no_kk',
                    className: 'text-nowrap text-start',
                    render: function(noKK) {
                        if (!noKK) return '-';
                        return `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">${noKK}</span>
                        <button 
                            type="button"
                            class="btn btn-outline-secondary btn-xs btnCopyNoKK"
                            data-value="${noKK}"
                            title="Salin No KK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `;
                    }
                },

                {
                    data: 'nama_kk'
                },
                {
                    data: 'status',
                    render: function(d) {
                        if (d === 'pending') return '<span class="badge badge-warning">Pending</span>';
                        if (d === 'sent') return '<span class="badge badge-success">Sent</span>';
                        if (d === 'failed') return '<span class="badge badge-danger">Failed</span>';
                        return d;
                    }
                },
                {
                    data: 'admin'
                },
                {
                    data: 'nope'
                },
                {
                    data: 'due_date'
                },
                {
                    data: 'sent_at'
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `<button class="btn btn-sm btn-primary btn-resend" data-id="${data.id}">
                                    <i class="fa fa-paper-plane"></i> Resend
                                </button>`;
                    }
                }
            ]
        });

        // Filter & Refresh
        $('#filterStatus, #filterQ, #filterStartDate, #filterEndDate').on('change keyup', function() {
            table.ajax.reload();
            loadSummary();
        });

        $('#btnRefresh').on('click', function() {
            table.ajax.reload();
            loadSummary();
        });

        // Resend action
        $('#tblReminder').on('click', '.btn-resend', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Kirim ulang reminder?',
                text: 'Pesan akan dikirim ulang ke Admin Desa.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kirim'
            }).then((res) => {
                if (!res.isConfirmed) return;

                $.ajax({
                    url: baseUrl + '/dtsen/reminder-monitor/resend',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.status) {
                            Swal.fire('Berhasil', resp.message, 'success');
                            table.ajax.reload();
                            loadSummary();
                        } else {
                            Swal.fire('Gagal', resp.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Koneksi server gagal.', 'error');
                    }
                });
            });
        });

        // ========================= 📋 COPY NO KK =========================
        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).data('value');

            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            }).catch(() => {
                Swal.fire('Gagal', 'Tidak dapat menyalin No. KK', 'error');
            });
        });

    });
</script>

<?= $this->endSection(); ?>