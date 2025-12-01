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

    $(function() {
        const table = $('#tblReminder').DataTable({
            responsive: true,
            processing: true,
            serverSide: false, // client-side as we returned all rows
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
                {
                    data: 'no_kk'
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
                        let btn = `<button class="btn btn-sm btn-primary btn-resend" data-id="${data.id}"><i class="fa fa-paper-plane"></i> Resend</button>`;
                        return btn;
                    }
                }
            ]
        });

        $('#filterStatus, #filterQ').on('change keyup', function() {
            table.ajax.reload();
        });

        $('#btnRefresh').on('click', function() {
            table.ajax.reload();
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
                        } else {
                            Swal.fire('Gagal', resp.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Koneksi server gagal.', 'error');
                    }
                });
            });
        });
    });
</script>

<?= $this->endSection(); ?>