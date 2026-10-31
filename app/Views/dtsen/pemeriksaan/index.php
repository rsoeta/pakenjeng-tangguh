<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper pemeriksaan-workspace">
    <section class="content-header pb-2">
        <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Pemeriksaan Data ART &amp; KK</h1>
                <p class="text-muted mb-0">Workspace terpadu untuk meninjau, mengarsipkan, dan memulihkan data keluarga.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2 mt-md-0" id="btnRefreshWorkspace">
                <i class="fas fa-sync-alt mr-1"></i> Segarkan
            </button>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-6 col-xl-3 mb-2">
                    <div class="workspace-stat">
                        <span class="workspace-stat-icon bg-primary"><i class="fas fa-house-user"></i></span>
                        <div><small>KK Aktif</small><strong id="stat_total_kk">—</strong></div>
                    </div>
                </div>
                <div class="col-6 col-xl-3 mb-2">
                    <div class="workspace-stat">
                        <span class="workspace-stat-icon bg-info"><i class="fas fa-users"></i></span>
                        <div><small>ART Aktif</small><strong id="stat_total_art">—</strong></div>
                    </div>
                </div>
                <div class="col-6 col-xl-3 mb-2">
                    <div class="workspace-stat">
                        <span class="workspace-stat-icon bg-warning"><i class="fas fa-archive"></i></span>
                        <div><small>KK Diarsipkan</small><strong id="stat_archived_kk">—</strong></div>
                    </div>
                </div>
                <div class="col-6 col-xl-3 mb-2">
                    <div class="workspace-stat">
                        <span class="workspace-stat-icon bg-secondary"><i class="fas fa-user-slash"></i></span>
                        <div><small>ART Diarsipkan</small><strong id="stat_archived_art">—</strong></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm workspace-card">
                <div class="row no-gutters">
                    <aside class="col-lg-4 workspace-sidebar">
                        <div class="workspace-panel-heading">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="h6 mb-1">Keluarga</h2>
                                    <small class="text-muted" id="kkListSummary">Memuat daftar...</small>
                                </div>
                                <button type="button" class="btn btn-light btn-sm" id="btnExportKK" title="Ekspor daftar keluarga">
                                    <i class="fas fa-file-excel text-success"></i>
                                </button>
                            </div>
                            <div class="input-group input-group-sm mt-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                                <input type="search" class="form-control border-left-0" id="workspaceKKSearch" placeholder="Cari No. KK, kepala keluarga, alamat..." autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnKKSearchClear" aria-label="Hapus pencarian"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="btn-group btn-group-sm flex-grow-1" role="group" aria-label="Status arsip keluarga">
                                    <button type="button" class="btn btn-primary js-kk-status" data-status="active">Aktif</button>
                                    <button type="button" class="btn btn-outline-secondary js-kk-status" data-status="archive">Arsip</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-warning ml-2" id="btnKKNoFoto" title="Tampilkan KK tanpa foto">
                                    <i class="fas fa-image mr-1"></i> Tanpa foto
                                </button>
                            </div>
                        </div>
                        <div class="workspace-list-wrap">
                            <div id="kkListLoading" class="workspace-empty"><span class="spinner-border spinner-border-sm text-primary"></span><span>Memuat keluarga...</span></div>
                            <div id="kkListEmpty" class="workspace-empty d-none">
                                <i class="far fa-folder-open"></i><span>Tidak ada keluarga pada filter ini.</span>
                            </div>
                            <div id="kkList" class="workspace-kk-list" role="list"></div>
                        </div>
                        <div class="workspace-sidebar-footer">
                            <div class="w-100">
                                <div id="kkBulkToolbar" class="d-none d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted" id="kkSelectionSummary">0 dipilih</small>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnBulkKK">
                                        <i class="fas fa-archive mr-1"></i><span>Arsipkan pilihan</span>
                                    </button>
                                </div>
                                <nav aria-label="Pagination daftar keluarga">
                                    <ul class="pagination pagination-sm justify-content-center mb-0" id="kkPagination"></ul>
                                </nav>
                            </div>
                        </div>
                    </aside>

                    <main class="col-lg-8 workspace-main">
                        <div class="workspace-panel-heading workspace-main-heading">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="mb-2 mb-md-0">
                                    <h2 class="h6 mb-1" id="panelARTTitle">Pilih keluarga untuk melihat anggota</h2>
                                    <small class="text-muted" id="panelKKMeta">Daftar anggota tampil sesuai status aktif atau arsip.</small>
                                </div>
                                <button type="button" class="btn btn-outline-success btn-sm" id="btnExportART" disabled>
                                    <i class="fas fa-file-excel mr-1"></i> Ekspor ART
                                </button>
                            </div>
                            <div class="input-group input-group-sm mt-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                                <input type="search" class="form-control border-left-0" id="workspaceARTSearch" placeholder="Cari NIK atau nama anggota..." autocomplete="off" disabled>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnARTSearchClear" aria-label="Hapus pencarian"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="workspace-art-wrap">
                            <div id="artListLoading" class="workspace-empty d-none"><span class="spinner-border spinner-border-sm text-primary"></span><span>Memuat anggota...</span></div>
                            <div id="artListEmpty" class="workspace-empty">
                                <i class="fas fa-mouse-pointer"></i><span>Pilih satu keluarga di panel kiri.</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0 workspace-art-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><input type="checkbox" id="checkAllART" aria-label="Pilih semua ART yang tampil"></th>
                                            <th>Nama / NIK</th>
                                            <th>Hubungan</th>
                                            <th>JK / Umur</th>
                                            <th>Pekerjaan</th>
                                            <th>Status</th>
                                            <th class="text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="artList"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="workspace-main-footer">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                <span id="artListSummary" class="text-muted small">Belum ada data dipilih.</span>
                                <div id="artBulkToolbar" class="d-none d-flex align-items-center" style="gap: 8px;">
                                    <small class="text-muted" id="artSelectionSummary">0 dipilih</small>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnBulkART">
                                        <i class="fas fa-archive mr-1"></i><span>Arsipkan ART</span>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLoadMoreART" disabled>Muat berikutnya</button>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalEditKK" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditKKWorkspace">
                <input type="hidden" name="id_kk">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Data KK</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editNoKK">Nomor KK</label>
                        <input id="editNoKK" type="text" name="no_kk" class="form-control" maxlength="20" required>
                    </div>
                    <div class="form-group">
                        <label for="editKepalaKeluarga">Kepala Keluarga</label>
                        <input id="editKepalaKeluarga" type="text" name="kepala_keluarga" class="form-control" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="editAlamatKK">Alamat</label>
                        <textarea id="editAlamatKK" name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="editProgramKK">Program Bansos</label>
                            <select id="editProgramKK" name="program_bansos" class="form-control">
                                <option value="">Tidak ada</option>
                                <option value="2">BPNT</option>
                                <option value="1">PKH</option>
                                <option value="5">PBI</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editKategoriAdat">Kategori Adat</label>
                            <select id="editKategoriAdat" name="kategori_adat" class="form-control">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditART" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditARTWorkspace">
                <input type="hidden" name="id_art">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Edit Data ART</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="editNIK">NIK</label>
                            <input id="editNIK" type="text" name="nik" class="form-control" inputmode="numeric" maxlength="16">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editNamaART">Nama Lengkap</label>
                            <input id="editNamaART" type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="editShdkWorkspace">Hubungan Keluarga</label>
                            <select id="editShdkWorkspace" name="shdk" class="form-control"></select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="editJenisKelamin">Jenis Kelamin</label>
                            <select id="editJenisKelamin" name="jenis_kelamin" class="form-control">
                                <option value="">Pilih...</option><option value="L">Laki-laki</option><option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="editTanggalLahir">Tanggal Lahir</label>
                            <input id="editTanggalLahir" type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editPendidikanWorkspace">Pendidikan Terakhir</label>
                            <select id="editPendidikanWorkspace" name="pendidikan_terakhir" class="form-control"></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editPekerjaanWorkspace">Pekerjaan</label>
                            <select id="editPekerjaanWorkspace" name="pekerjaan" class="form-control"></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editDisabilitasWorkspace">Disabilitas</label>
                            <select id="editDisabilitasWorkspace" name="disabilitas" class="form-control"></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editStatusHamil">Status Hamil</label>
                            <select id="editStatusHamil" name="status_hamil" class="form-control">
                                <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editIbuKandung">Ibu Kandung</label>
                            <input id="editIbuKandung" type="text" name="ibu_kandung" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nomor KK</label>
                            <input type="text" id="editARTNoKK" class="form-control" readonly>
                        </div>
                        <div class="form-group col-12" id="reasonNikGroup">
                            <label for="editReasonNik">Alasan perubahan NIK</label>
                            <textarea id="editReasonNik" name="reason_nik" class="form-control" rows="2"></textarea>
                            <small class="form-text text-muted">Wajib diisi jika NIK diubah.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .pemeriksaan-workspace .content-header h1 { font-weight: 700; color: #172b4d; }
    .workspace-stat { display: flex; align-items: center; gap: 14px; min-height: 82px; padding: 14px 16px; background: #fff; border: 1px solid #e8edf4; border-radius: 12px; box-shadow: 0 2px 8px rgba(25, 42, 70, .04); }
    .workspace-stat-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; color: #fff; border-radius: 11px; }
    .workspace-stat small, .workspace-stat strong { display: block; }
    .workspace-stat small { color: #718096; font-size: .75rem; }
    .workspace-stat strong { color: #1a2b49; font-size: 1.25rem; line-height: 1.35; }
    .workspace-card { overflow: hidden; border-radius: 12px; }
    .workspace-sidebar { display: flex; flex-direction: column; min-height: 600px; border-right: 1px solid #e8edf4; background: #f8fafc; }
    .workspace-main { display: flex; flex-direction: column; min-height: 600px; background: #fff; }
    .workspace-panel-heading { padding: 18px; border-bottom: 1px solid #e8edf4; background: #fff; }
    .workspace-list-wrap, .workspace-art-wrap { position: relative; flex: 1; min-height: 250px; overflow: auto; }
    .workspace-sidebar-footer, .workspace-main-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; border-top: 1px solid #e8edf4; background: #fff; }
    .workspace-kk-item { display: block; width: 100%; padding: 14px 16px; border: 0; border-bottom: 1px solid #edf1f6; background: transparent; color: inherit; text-align: left; cursor: pointer; }
    .workspace-kk-item:hover, .workspace-kk-item.is-selected { background: #eef5ff; }
    .workspace-kk-item.is-selected { box-shadow: inset 3px 0 #2878d0; }
    .workspace-kk-title { display: flex; justify-content: space-between; gap: 8px; font-weight: 600; color: #243b5a; }
    .workspace-kk-meta { display: block; margin-top: 4px; color: #6b778c; font-size: .78rem; }
    .workspace-kk-actions { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 8px; }
    .workspace-empty { display: flex; min-height: 180px; align-items: center; justify-content: center; flex-direction: column; gap: 8px; color: #77849a; text-align: center; }
    .workspace-empty i { font-size: 1.4rem; color: #a7b2c2; }
    .workspace-art-table { min-width: 720px; }
    .workspace-art-table thead th { position: sticky; top: 0; z-index: 1; padding: 11px 10px; border-top: 0; background: #f8fafc; color: #66758a; font-size: .72rem; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .workspace-art-table td { vertical-align: middle; padding: 11px 10px; }
    .workspace-art-name { display: block; color: #243b5a; font-weight: 600; }
    .workspace-art-sub { display: block; color: #78869a; font-size: .75rem; }
    @media (max-width: 991.98px) {
        .workspace-sidebar { min-height: 420px; border-right: 0; border-bottom: 1px solid #e8edf4; }
        .workspace-main { min-height: 450px; }
    }
</style>

<script>
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";
    window.csrfName = "<?= csrf_token() ?>";
    window.csrfHash = "<?= csrf_hash() ?>";
</script>
<script src="<?= base_url('assets/js/pemeriksaan.js?v=3.0.1') ?>"></script>

<?= $this->endSection() ?>
