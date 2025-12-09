<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .fab-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        font-size: 24px;
        cursor: pointer;
        z-index: 999;
        transition: all 0.25s ease;
    }

    .fab-btn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    #btnReloadDraft,
    #btnReloadVerified {
        padding: 4px 10px;
    }

    /* Countdown Digit Styles */
    /* Divider tipis */
    .countdown-divider {
        width: 1px;
        height: 36px;
        background: rgba(255, 255, 255, 0.35);
        margin: 0 14px;
    }

    .countdown-header {
        margin-left: auto;
        display: flex;
        flex-direction: column;
        /* tengah secara vertical */
        justify-content: center;
        /* sedikit turun */
        transform: translateY(3px);
        text-align: right;
        align-items: flex-end;
        line-height: 1;
    }

    .countdown-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 1px;
    }

    /* Wrapper futuristik */
    .countdown-future {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Digit futuristik */
    .countdown-future .digit {
        font-size: clamp(18px, 4vw, 26px);
        font-family: "Orbitron", sans-serif;
        font-weight: 700;
        color: #ffffff;
        text-shadow:
            0 0 6px rgba(255, 255, 255, .4),
            0 0 12px rgba(0, 0, 0, .3);
        padding: 0 2px;
        transition: transform .15s ease-out;
    }

    .countdown-future .digit.animate {
        transform: scale(1.20);
    }

    /* Desktop: rapikan spacing supaya tidak menjauh dari kanan */
    @media (min-width: 992px) {
        .countdown-header {
            padding-right: 8px;
            /* lebih precise di desktop */
            transform: translateY(2px);
        }

        .countdown-divider {
            height: 32px;
        }

        .countdown-future {
            gap: 10px;
        }

        .countdown-label {
            font-size: 14px;
        }
    }

    /* Mobile tetap responsive */
    @media (max-width: 576px) {
        .countdown-header {
            transform: translateY(1px);
        }

        .countdown-future {
            gap: 4px;
        }

        .countdown-divider {
            display: none;
        }

        .countdown-label {
            font-size: 11px;
        }
    }

    @media (max-width: 500px) {
        .countdown-label {
            font-size: 11px;
        }

        .countdown-future {
            gap: 4px;
        }
    }
</style>

<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-hand-holding-heart"></i> <?= esc($title); ?></h5>
            <ol class="breadcrumb float-right mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= esc($title); ?></li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="card mt-2">
            <div class="card-header bg-primary text-white d-flex align-items-center">

                <h5 class="mb-0">Usulan Bantuan Sosial</h5>

                <div class="countdown-divider"></div>

                <div class="countdown-header text-end">
                    <div class="countdown-label">Sisa Waktu</div>
                    <div id="countdownWrap" class="countdown-future">
                        <span class="digit" id="cdDays">0</span>d
                        <span class="digit" id="cdHours">00</span>h
                        <span class="digit" id="cdMinutes">00</span>m
                        <span class="digit" id="cdSeconds">00</span>s
                    </div>
                </div>

            </div>

            <div class="card-body">
                <button id="btnTambahUsulan" class="fab-btn" data-toggle="tooltip" title="Tambah Usulan Bansos">
                    <i class="fas fa-hand-holding-heart"></i>
                </button>

                <!-- 🔹 Tab Navigation -->
                <ul class="nav nav-tabs" id="usulanTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tabDraft-tab" data-bs-toggle="tab" data-bs-target="#tabDraft" type="button" role="tab">
                            🟡 Draft
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabVerified-tab" data-bs-toggle="tab" data-bs-target="#tabVerified" type="button" role="tab">
                            ✅ Diverifikasi
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <!-- 🟡 Draft -->
                    <div class="tab-pane fade show active" id="tabDraft" role="tabpanel">
                        <div class="d-flex justify-content-end mb-2">
                            <button id="btnReloadDraft" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-sync-alt"></i> Reload
                            </button>
                        </div>
                        <!-- CSRF Token untuk AJAX -->
                        <input type="hidden"
                            id="csrfToken"
                            name="<?= csrf_token() ?>"
                            value="<?= csrf_hash() ?>" />
                        <table id="tableUsulanBansosDraft" class="table table-striped table-bordered w-100"></table>
                    </div>

                    <!-- ✅ Diverifikasi -->
                    <div class="tab-pane fade" id="tabVerified" role="tabpanel">
                        <div class="d-flex justify-content-end mb-2">
                            <button id="btnReloadVerified" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-sync-alt"></i> Reload
                            </button>
                        </div>

                        <table id="tableUsulanBansosVerified" class="table table-striped table-bordered w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('dtsen/usulan_bansos/form_usulan_bansos'); ?>

<!-- tambahkan script datatable datatables.config.js -->
<script src="<?= base_url('assets/js/datatables.config.js'); ?>"></script>

<script>
    window.userRole = <?= session()->get('role_id') ?? 99 ?>;
    window.userNik = "<?= session()->get('nik') ?>";
    window.userName = "<?= session()->get('fullname') ?>";
</script>

<script src="<?= base_url('assets/js/usulan_bansos/usulan_bansos.deadline.js'); ?>"></script>
<script src="<?= base_url('assets/js/usulan_bansos/usulan_bansos.table.js'); ?>"></script>
<script src="<?= base_url('assets/js/usulan_bansos/usulan_bansos.form.js'); ?>"></script>

<?= $this->endSection(); ?>