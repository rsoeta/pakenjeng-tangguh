<?php
$roleIdMigration = (int) (session()->get('role_id') ?? ($user_login['role_id'] ?? 99));
if ($roleIdMigration >= 3) {
    return;
}
?>
<div class="card-body">
    <div class="tab-content" id="wa-tabContent">
        <div class="alert alert-light border mb-3" id="migrationStatusBox">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong class="text-dark"><i class="fas fa-database text-primary mr-1"></i> Status Skema Database:</strong>
                    <span id="migrationStatusBadge" class="badge bg-secondary ms-1">Memeriksa...</span>
                </div>
                <small class="text-muted" id="migrationMetaText">Memuat informasi batch migrasi...</small>
            </div>
            <div id="migrationPendingWrap" class="mt-2 small d-none">
                <div class="fw-bold text-warning mb-1"><i class="fas fa-clock mr-1"></i> Daftar migrasi belum dijalankan:</div>
                <ul id="migrationPendingList" class="mb-0 pl-3 text-dark"></ul>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" id="btnMigration" class="btn btn-primary">
                <i class="fas fa-play-circle mr-1"></i> Jalankan Migrasi DB
            </button>

            <button type="button" id="btnDownloadDb" class="btn btn-success">
                <i class="fas fa-download mr-1"></i> Unduh Database (.sql)
            </button>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    (function() {
        function escHtmlMig(str) {
            return String(str ?? '').replace(/[&<>"']/g, function(m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
            });
        }

        function refreshMigrationStatus() {
            const $badge = $('#migrationStatusBadge');
            const $meta  = $('#migrationMetaText');
            const $wrap  = $('#migrationPendingWrap');
            const $list  = $('#migrationPendingList');

            $.getJSON("<?= base_url('admin/migrate/status') ?>")
                .done(function(res) {
                    if (!res || res.status !== 'success') {
                        $badge.attr('class', 'badge bg-danger ms-1').text('Gagal memuat');
                        return;
                    }

                    $meta.text(`Terpasang: ${res.total_applied} | Batch terakhir: ${res.latest_batch} (${res.latest_version || '-'})`);

                    if (res.is_up_to_date) {
                        $badge.attr('class', 'badge bg-success ms-1').text('Up-to-date');
                        $wrap.addClass('d-none');
                        $list.empty();
                    } else {
                        $badge.attr('class', 'badge bg-warning text-dark ms-1').text(`${res.pending_count} Migrasi Tertunda`);
                        $list.empty();
                        (res.pending || []).forEach(function(item) {
                            $list.append(`<li><code>${escHtmlMig(item)}</code></li>`);
                        });
                        $wrap.removeClass('d-none');
                    }
                })
                .fail(function(xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memeriksa status';
                    $badge.attr('class', 'badge bg-danger ms-1').text(msg);
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            refreshMigrationStatus();

            const btnMig = document.getElementById('btnMigration');
            const btnDl  = document.getElementById('btnDownloadDb');

            if (btnMig) {
                btnMig.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Jalankan Migrasi Database?',
                        text: 'Sistem akan mengecek dan menerapkan file migrasi baru yang belum dijalankan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, jalankan!',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        Swal.fire({
                            title: 'Menjalankan Migrasi...',
                            text: 'Mohon tunggu hingga proses selesai.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });

                        const csrfName = $('meta[name="csrf-token-name"]').attr('content') || '<?= csrf_token() ?>';
                        const csrfHash = $('meta[name="csrf-token-value"]').attr('content') || '<?= csrf_hash() ?>';
                        const payload  = {};
                        payload[csrfName] = csrfHash;

                        $.ajax({
                            url: "<?= base_url('admin/migrate') ?>",
                            type: 'POST',
                            dataType: 'json',
                            data: payload
                        }).done(function(res) {
                            if (res && res.csrfHash) {
                                $('meta[name="csrf-token-value"]').attr('content', res.csrfHash);
                            }

                            refreshMigrationStatus();

                            if (res && res.up_to_date) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Database Sudah Up-to-Date',
                                    html: `<p class="mb-2">${escHtmlMig(res.message)}</p>
                                           <small class="text-muted">Total terpasang: <b>${res.total_applied}</b> | Batch terakhir: <b>${res.latest_batch}</b> (${escHtmlMig(res.latest_version)})</small>`
                                });
                            } else {
                                const listItems = (res.executed || []).map(item => `<li><code>${escHtmlMig(item)}</code></li>`).join('');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Migrasi Berhasil!',
                                    html: `<p class="mb-2">${escHtmlMig(res.message || 'Migrasi selesai dijalankan.')}</p>
                                           <ul class="text-start small bg-light p-2 border rounded mb-2">${listItems}</ul>
                                           <small class="text-muted">Batch terakhir: <b>${res.latest_batch}</b> (${escHtmlMig(res.latest_version)})</small>`
                                });
                            }
                        }).fail(function(xhr) {
                            let errMsg = 'Terjadi kesalahan saat menjalankan migrasi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            } else if (xhr.status) {
                                errMsg = `HTTP ${xhr.status}: ${xhr.statusText || 'Gagal menjalankan migrasi.'}`;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Migrasi Gagal!',
                                text: errMsg
                            });
                        });
                    });
                });
            }

            if (btnDl) {
                btnDl.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Unduh Backup Database?',
                        text: 'File backup akan diunduh dalam format .sql',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Unduh Sekarang',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "<?= base_url('admin/download-db') ?>";
                        }
                    });
                });
            }
        });
    })();
</script>

<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: <?= json_encode((string) session()->getFlashdata('success'), JSON_UNESCAPED_UNICODE) ?>
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: <?= json_encode((string) session()->getFlashdata('error'), JSON_UNESCAPED_UNICODE) ?>
        });
    </script>
<?php endif; ?>
