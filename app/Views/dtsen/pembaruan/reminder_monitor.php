<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Monitoring Reminder WA</h1>
            <!-- tampilkan session id -->
            <?php
            $adminId = session()->get('id');
            echo "<p class='text-muted'>Admin ID: $adminId</p>";
            ?>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row mb-3">

                <div class="col-md-3">
                    <div class="card shadow-sm border-left-warning">
                        <div class="card-body">
                            <h6>Total Pending</h6>
                            <h3 id="cardPending" class="text-warning">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-left-success">
                        <div class="card-body">
                            <h6>Sent Today</h6>
                            <h3 id="cardSentToday" class="text-success">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-left-danger">
                        <div class="card-body">
                            <h6>Failed</h6>
                            <h3 id="cardFailed" class="text-danger">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-left-primary">
                        <div class="card-body">
                            <h6>Due Next 1 Hour</h6>
                            <h3 id="cardNextHour" class="text-primary">0</h3>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card shadow">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select id="filterStatus" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="pending" selected>Pending</option>
                                <option value="sent">Sent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Search</label>
                            <input id="filterQ" class="form-control form-control-sm" placeholder="No KK / Nama KK / Admin">
                        </div>
                        <div class="col-md-5 text-right">
                            <label>&nbsp;</label><br>
                            <button id="btnRefresh" class="btn btn-sm btn-secondary">Refresh</button>
                        </div>
                    </div>

                    <table id="tblReminder" class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No KK</th>
                                <th>Nama KK</th>
                                <th>Admin Desa</th>
                                <th>No. HP</th>
                                <th>Due Date</th>
                                <th>Status</th>
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
        $.get(baseUrl + '/dtsen/reminder-monitor/summary', function(resp) {
            $('#cardPending').text(resp.pending);
            $('#cardSentToday').text(resp.sent_today);
            $('#cardFailed').text(resp.failed);
            $('#cardNextHour').text(resp.due_next_hour);
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
                    data: 'admin'
                },
                {
                    data: 'nope'
                },
                {
                    data: 'due_date'
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
        $('#filterStatus, #filterQ').on('change keyup', function() {
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