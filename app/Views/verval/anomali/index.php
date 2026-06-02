<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="fw-bold"><i class="fas fa-exclamation-triangle text-warning"></i> Data Anomali SIKS-NG</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item">Verval</li>
                        <li class="breadcrumb-item active">Anomali</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning card-outline shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title fw-bold text-secondary mb-0 mt-1">Daftar Temuan Anomali</h5>
                    <div class="card-tools">
                        <!-- batasi akses hanya untuk user dengan role <4 -->
                        <?= (session()->get('role_id') < 4) ?
                            '<button type="button" class="btn btn-warning btn-sm shadow-sm text-dark fw-bold" data-toggle="modal" data-target="#modalTambahAnomali">' . '<i class="fas fa-plus-circle"></i> Lapor Anomali Baru' . '</button>' : ''; ?>
                    </div>
                </div>
                <div class="card-body">

                    <!-- 🚀 AREA FILTER STATUS -->
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6">
                            <label class="small text-muted mb-1 fw-bold">Filter Status:</label>
                            <select id="filter_status" class="form-control form-control-sm shadow-sm">
                                <option value="" <?= $default_filter == '' ? 'selected' : '' ?>>Tampilkan Semua</option>
                                <option value="open" <?= $default_filter == 'open' ? 'selected' : '' ?>>⏳ Open</option>
                                <option value="draft" <?= $default_filter == 'draft' ? 'selected' : '' ?>>📝 Draft</option>
                                <option value="verified" <?= $default_filter == 'verified' ? 'selected' : '' ?>>✅ Verified</option>
                                <option value="rejected" <?= $default_filter == 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tabelAnomali" class="table table-bordered table-hover table-striped w-100 text-sm">
                            <thead class="bg-light text-secondary text-center text-nowrap">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Aksi</th>
                                    <th>Status</th>
                                    <th>NIK & Nama KPM</th>
                                    <th>Alamat / Wilayah</th>
                                    <!-- 🚀 Diubah agar universal -->
                                    <th>Dokumen Lampiran</th>
                                    <th>Tgl Lapor</th>
                                </tr>
                            </thead>
                            <!-- 🚀 Tbody Dikosongkan, diisi otomatis oleh AJAX -->
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- 🚀 MODAL LIGHTBOX UNTUK PREVIEW GAMBAR -->
                <div class="modal fade" id="modalLightbox" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-transparent border-0 shadow-none">
                            <div class="modal-body text-center position-relative p-0">
                                <!-- Tombol Close (Silang) di pojok atas -->
                                <button type="button" class="close text-white position-absolute shadow" style="top:-30px; right:0; font-size: 2rem; opacity: 1; z-index: 1050;" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <img id="lightboxImage" src="" class="img-fluid rounded shadow-lg" style="max-height: 85vh; border: 3px solid white;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL TAMBAH ANOMALI -->
<div class="modal fade" id="modalTambahAnomali" tabindex="-1" role="dialog" aria-labelledby="modalTambahAnomaliLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-warning" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahAnomaliLabel"><i class="fas fa-search"></i> Cari & Lapor Anomali</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">

                <!-- Form Pencarian NIK -->
                <div class="form-group mb-4">
                    <label class="fw-bold small text-secondary">Pencarian NIK KPM (Tabel DTSEN) <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <input type="number" id="search_nik" class="form-control form-control-lg text-center fw-bold" placeholder="Masukkan 16 Digit NIK..." autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" id="btnCariNik" class="btn btn-primary px-4 fw-bold">
                                <i class="fas fa-search"></i> CARI
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary opacity-25">

                <!-- Form Data KPM (Readonly & Auto-fill) -->
                <form id="form_simpan_anomali" method="POST" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <!-- Hidden field untuk kebutuhan ID Petugas (Contoh) -->
                    <input type="hidden" name="petugas_entri_id" id="val_petugas_entri_id" value="0">

                    <!-- 🚀 Hidden Input untuk Menyimpan Kode ID -->
                    <input type="hidden" name="jenis_kelamin" id="val_jenis_kelamin">
                    <input type="hidden" name="status_kawin" id="val_status_kawin">
                    <input type="hidden" name="pekerjaan" id="val_pekerjaan">
                    <input type="hidden" name="shdk" id="val_shdk">
                    <input type="hidden" name="desa" id="val_desa">
                    <input type="hidden" name="kecamatan" id="val_kecamatan">
                    <input type="hidden" name="kabupaten" id="val_kabupaten">
                    <input type="hidden" name="provinsi" id="val_provinsi">

                    <div class="row" id="area_hasil_pencarian" style="display: none;">
                        <div class="col-12 mb-3">
                            <div class="alert alert-success py-2 mb-0 small text-center fw-bold">
                                <i class="fas fa-check-circle"></i> Data Penduduk Ditemukan! Silakan lengkapi bukti Anomali.
                            </div>
                        </div>

                        <!-- Data Identitas Utama -->
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">NIK</label>
                            <input type="text" name="nik" id="val_nik" class="form-control form-control-sm font-weight-bold" readonly required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">No. Kartu Keluarga</label>
                            <input type="text" name="no_kk" id="val_no_kk" class="form-control form-control-sm font-weight-bold" readonly>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="val_nama_lengkap" class="form-control form-control-sm font-weight-bold" readonly required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Nama Ibu Kandung</label>
                            <input type="text" name="ibu_kandung" id="val_ibu_kandung" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="val_tempat_lahir" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="val_tanggal_lahir" class="form-control form-control-sm" readonly>
                        </div>

                        <!-- Data Status & Lainnya -->
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Jenis Kelamin</label>
                            <!-- name="jenis_kelamin" dihapus, id diubah menjadi val_jenis_kelamin_nama -->
                            <input type="text" id="val_jenis_kelamin_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Status Kawin</label>
                            <input type="text" id="val_status_kawin_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Pekerjaan</label>
                            <input type="text" id="val_pekerjaan_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <!-- Contoh Perubahan -->
                        <div class="col-md-12 form-group mb-2">
                            <label class="small text-muted mb-0">SHDK (Hubungan Keluarga)</label>
                            <!-- name="shdk" dihapus, id diubah menjadi val_shdk_nama -->
                            <input type="text" id="val_shdk_nama" class="form-control form-control-sm" readonly>
                        </div>

                        <!-- Data Alamat -->
                        <div class="col-md-12 form-group mb-2 mt-2">
                            <h6 class="border-bottom pb-1 fw-bold text-secondary">Data Wilayah (Alamat)</h6>
                        </div>
                        <div class="col-md-12 form-group mb-2">
                            <label class="small text-muted mb-0">Alamat Lengkap</label>
                            <textarea name="alamat" id="val_alamat" class="form-control form-control-sm" rows="2" readonly></textarea>
                        </div>
                        <div class="col-md-3 form-group mb-2">
                            <label class="small text-muted mb-0">RT</label>
                            <input type="text" name="rt" id="val_rt" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3 form-group mb-2">
                            <label class="small text-muted mb-0">RW</label>
                            <input type="text" name="rw" id="val_rw" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Desa / Kelurahan</label>
                            <input type="text" id="val_desa_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Kecamatan</label>
                            <input type="text" id="val_kecamatan_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Kabupaten/Kota</label>
                            <input type="text" id="val_kabupaten_nama" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Provinsi</label>
                            <input type="text" id="val_provinsi_nama" class="form-control form-control-sm" readonly>
                        </div>

                        <!-- Upload File Screenshot SIKS-NG -->
                        <div class="col-md-12 form-group mb-3 mt-3">
                            <div class="card bg-warning-light border-warning shadow-none mb-0">
                                <div class="card-body p-3">
                                    <!-- 🚀 TAMBAHAN: Area Preview Gambar (Awalnya Disembunyikan) -->
                                    <div id="preview_area" class="mt-3 text-center" style="display: none;">
                                        <img id="preview_bukti" src="" alt="Preview Bukti" class="img-fluid img-thumbnail rounded shadow-sm" style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <div class="mt-2">
                                            <button type="button" id="btnHapusPreview" class="btn btn-sm btn-danger shadow-sm fw-bold">
                                                <i class="fas fa-trash-alt"></i> Hapus Foto
                                            </button>
                                        </div>
                                    </div>
                                    <label class="fw-bold text-dark mb-1"><i class="fas fa-camera"></i> Screenshot Bukti SIKS-NG <span class="text-danger">*</span></label>
                                    <p class="text-muted small mb-2">Lampirkan tangkapan layar (screenshot) bukti ketidakpadanan data dari portal SIKS-NG.</p>
                                    <input type="file" name="bukti_siksng" id="bukti_siksng" class="form-control-file" accept="image/png, image/jpeg, image/jpg" required>
                                </div>
                            </div>
                        </div>

                    </div> <!-- End Row Hasil -->
            </div>
            <div class="modal-footer bg-light justify-content-between" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; display: none;" id="modal_footer_anomali">
                <button type="button" class="btn btn-secondary btn-sm shadow-sm" data-dismiss="modal">Batal</button>
                <button type="submit" id="btnSubmitAnomali" class="btn btn-success btn-sm shadow-sm px-4 fw-bold">
                    <i class="fas fa-paper-plane"></i> Kirim ke Petugas Entri
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- 🚀 MODAL TINDAK LANJUT PETUGAS ENTRI -->
<div class="modal fade" id="modalTindakLanjut" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-info text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Tindak Lanjut Anomali</h5>
                <!-- Tombol Close Atas -->
                <button type="button" class="close text-white btn-tutup-modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_update_petugas" method="POST" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_anomali" id="edit_id_anomali">

                <div class="modal-body p-4 bg-light">
                    <!-- Catatan Penolakan (Jika ada) -->
                    <div id="area_catatan_penolakan" class="alert alert-danger py-2 small mb-3" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i> <b>Catatan Operator:</b><br><span id="text_catatan_penolakan"></span>
                    </div>

                    <div class="row">
                        <!-- Data Identitas Utama -->
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">NIK <span class="text-danger">*</span></label>
                            <!-- 🚀 Kunci NIK menggunakan readonly -->
                            <input type="text" name="nik" id="edit_nik" class="form-control form-control-sm fw-bold bg-white" readonly required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">No. Kartu Keluarga <span class="text-danger">*</span></label>
                            <input type="text" name="no_kk" id="edit_no_kk" class="form-control form-control-sm fw-bold" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="form-control form-control-sm text-uppercase fw-bold" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Nama Ibu Kandung</label>
                            <input type="text" name="ibu_kandung" id="edit_ibu_kandung" class="form-control form-control-sm text-uppercase">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="form-control form-control-sm text-uppercase">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="form-control form-control-sm">
                        </div>

                        <!-- Data Status & Lainnya -->
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-control form-control-sm">
                                <option value="L">LAKI-LAKI</option>
                                <option value="P">PEREMPUAN</option>
                            </select>
                        </div>
                        <!-- 🚀 Ubah Status Kawin, Pekerjaan, SHDK menjadi Select -->
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Status Kawin</label>
                            <select name="status_kawin" id="edit_status_kawin" class="form-control form-control-sm" required></select>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="small text-muted mb-0">Pekerjaan</label>
                            <select name="pekerjaan" id="edit_pekerjaan" class="form-control form-control-sm" required></select>
                        </div>
                        <div class="col-md-12 form-group mb-2">
                            <label class="small text-muted mb-0">SHDK (Hubungan Keluarga)</label>
                            <select name="shdk" id="edit_shdk" class="form-control form-control-sm" required></select>
                        </div>

                        <!-- Data Alamat & Wilayah Bertingkat -->
                        <div class="col-md-12 form-group mb-2 mt-2">
                            <h6 class="border-bottom pb-1 fw-bold text-secondary">Data Wilayah (Dropdown Bertingkat)</h6>
                        </div>
                        <div class="col-md-12 form-group mb-2">
                            <label class="small text-muted mb-0">Alamat Lengkap</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">RT</label>
                            <input type="text" name="rt" id="edit_rt" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">RW</label>
                            <input type="text" name="rw" id="edit_rw" class="form-control form-control-sm">
                        </div>

                        <!-- 🚀 4 Kolom Dropdown AJAX -->
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Provinsi <span class="text-danger">*</span></label>
                            <select name="provinsi" id="edit_provinsi" class="form-control form-control-sm" required>
                                <option value="">- Pilih Provinsi -</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Kabupaten/Kota <span class="text-danger">*</span></label>
                            <select name="kabupaten" id="edit_kabupaten" class="form-control form-control-sm" required>
                                <option value="">- Pilih Kab/Kota -</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Kecamatan <span class="text-danger">*</span></label>
                            <select name="kecamatan" id="edit_kecamatan" class="form-control form-control-sm" required>
                                <option value="">- Pilih Kecamatan -</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="small text-muted mb-0">Desa/Kelurahan <span class="text-danger">*</span></label>
                            <select name="desa" id="edit_desa" class="form-control form-control-sm" required>
                                <option value="">- Pilih Desa -</option>
                            </select>
                        </div>

                        <!-- Upload File Bukti KK -->
                        <div class="col-md-12 form-group mb-3 mt-3">
                            <div class="card bg-info-light border-info shadow-none mb-0">
                                <div class="card-body p-3">
                                    <div id="preview_area_kk" class="mt-3 text-center" style="display: none;">
                                        <img id="preview_kk" src="" class="img-fluid img-thumbnail rounded shadow-sm" style="max-height: 200px; width: 100%; object-fit: cover;">
                                    </div>
                                    <label class="fw-bold text-dark mb-1"><i class="fas fa-id-card"></i> Upload KK Terbaru <span class="text-danger">*</span></label>
                                    <p class="text-muted small mb-2">Lampirkan foto Kartu Keluarga terbaru yang sudah valid.</p>
                                    <input type="file" name="foto_kk_baru" id="foto_kk_baru" class="form-control-file" accept="image/png, image/jpeg, image/jpg" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light justify-content-between" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <!-- 🚀 Tambahkan class btn-tutup-modal -->
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm btn-tutup-modal">Batal</button>
                    <button type="submit" id="btnSubmitPetugas" class="btn btn-info btn-sm shadow-sm px-4 fw-bold">
                        <i class="fas fa-save"></i> Simpan & Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🚀 MODAL VERIFIKASI OPERATOR DESA -->
<div class="modal fade" id="modalVerifikasiOperator" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-success text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold"><i class="fas fa-check-double"></i> Verifikasi Data Anomali</h5>
                <button type="button" class="close text-white btn-tutup-verifikasi" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="verif_id_anomali">

                <!-- Area Preview Dokumen -->
                <div class="row mb-3">
                    <div class="col-md-6 text-center">
                        <label class="fw-bold text-secondary small">Bukti SIKS-NG (Awal)</label><br>
                        <img id="verif_img_siksng" src="" class="img-thumbnail shadow-sm btn-lightbox" style="max-height: 150px; cursor: pointer;">
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="fw-bold text-info small">Foto KK (Perbaikan Petugas)</label><br>
                        <img id="verif_img_kk" src="" class="img-thumbnail shadow-sm btn-lightbox border-info" style="max-height: 150px; cursor: pointer;">
                    </div>
                </div>
                <hr class="border-secondary opacity-25">

                <div class="row">
                    <!-- Data Identitas Utama -->
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">NIK <span class="text-danger">*</span></label>
                        <input type="text" id="verif_nik" class="form-control form-control-sm fw-bold bg-white" readonly>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">No. Kartu Keluarga <span class="text-danger">*</span></label>
                        <input type="text" id="verif_no_kk" class="form-control form-control-sm fw-bold bg-white" readonly>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="verif_nama_lengkap" class="form-control form-control-sm text-uppercase fw-bold bg-white" readonly>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Nama Ibu Kandung</label>
                        <input type="text" id="verif_ibu_kandung" class="form-control form-control-sm text-uppercase bg-white" readonly>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Tempat Lahir</label>
                        <input type="text" id="verif_tempat_lahir" class="form-control form-control-sm text-uppercase bg-white" readonly>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Tanggal Lahir</label>
                        <input type="date" id="verif_tanggal_lahir" class="form-control form-control-sm bg-white" readonly>
                    </div>

                    <!-- Data Status & Lainnya -->
                    <div class="col-md-4 form-group mb-2">
                        <label class="small text-muted mb-0">Jenis Kelamin</label>
                        <select id="verif_jenis_kelamin" class="form-control form-control-sm bg-white" disabled>
                            <option value="L">LAKI-LAKI</option>
                            <option value="P">PEREMPUAN</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group mb-2">
                        <label class="small text-muted mb-0">Status Kawin</label>
                        <select id="verif_status_kawin" class="form-control form-control-sm bg-white" disabled></select>
                    </div>
                    <div class="col-md-4 form-group mb-2">
                        <label class="small text-muted mb-0">Pekerjaan</label>
                        <select id="verif_pekerjaan" class="form-control form-control-sm bg-white" disabled></select>
                    </div>
                    <div class="col-md-12 form-group mb-2">
                        <label class="small text-muted mb-0">SHDK (Hubungan Keluarga)</label>
                        <select id="verif_shdk" class="form-control form-control-sm bg-white" disabled></select>
                    </div>

                    <!-- Data Alamat & Wilayah Bertingkat -->
                    <div class="col-md-12 form-group mb-2 mt-2">
                        <h6 class="border-bottom pb-1 fw-bold text-secondary">Data Wilayah</h6>
                    </div>
                    <div class="col-md-12 form-group mb-2">
                        <label class="small text-muted mb-0">Alamat Lengkap</label>
                        <textarea id="verif_alamat" class="form-control form-control-sm bg-white" rows="2" readonly></textarea>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">RT</label>
                        <input type="text" id="verif_rt" class="form-control form-control-sm bg-white" readonly>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">RW</label>
                        <input type="text" id="verif_rw" class="form-control form-control-sm bg-white" readonly>
                    </div>

                    <!-- Dropdown Wilayah AJAX (Disabled) -->
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Provinsi</label>
                        <select id="verif_provinsi" class="form-control form-control-sm bg-white" disabled></select>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Kabupaten/Kota</label>
                        <select id="verif_kabupaten" class="form-control form-control-sm bg-white" disabled></select>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Kecamatan</label>
                        <select id="verif_kecamatan" class="form-control form-control-sm bg-white" disabled></select>
                    </div>
                    <div class="col-md-6 form-group mb-2">
                        <label class="small text-muted mb-0">Desa/Kelurahan</label>
                        <select id="verif_desa" class="form-control form-control-sm bg-white" disabled></select>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light justify-content-between" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <button type="button" class="btn btn-secondary btn-sm shadow-sm btn-tutup-verifikasi">Batal</button>
                <div>
                    <button type="button" id="btnAksiTolak" class="btn btn-danger btn-sm shadow-sm px-3 fw-bold mr-2">
                        <i class="fas fa-times"></i> Tolak Data
                    </button>
                    <button type="button" id="btnAksiVerifikasi" class="btn btn-success btn-sm shadow-sm px-3 fw-bold">
                        <i class="fas fa-check-double"></i> Verifikasi Valid
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        // --------------------------------------------------------
        // 🚀 INISIALISASI DATATABLES AJAX & FILTER
        // --------------------------------------------------------
        var tableAnomali = $('#tabelAnomali').DataTable({
            "responsive": true,
            "autoWidth": false,
            "processing": true,
            "ajax": {
                "url": "<?= base_url('verval/anomali/get_data_ajax') ?>",
                "type": "GET",
                "data": function(d) {
                    // Kirim status filter yang sedang aktif
                    d.status = $('#filter_status').val();
                }
            },
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json",
                "search": "Cari Tabel:",
                "processing": '<i class="fas fa-spinner fa-spin fa-2x text-warning"></i>'
            }
        });

        // Otomatis reload tabel saat dropdown filter diubah
        $('#filter_status').on('change', function() {
            tableAnomali.ajax.reload();
        });

        // --------------------------------------------------------
        // 🚀 LOGIKA LIGHTBOX (PREVIEW GAMBAR BUKTI) - UPDATE FIXED
        // --------------------------------------------------------
        $(document).on('click', '.btn-lightbox', function() {
            var imgSrc = $(this).data('img');
            $('#lightboxImage').attr('src', imgSrc);
            $('#modalLightbox').modal('show');
        });

        // 🔥 KUNCI: Naikkan kasta z-index Lightbox agar berada di depan modal verifikasi
        $('#modalLightbox').on('show.bs.modal', function() {
            // Paksa z-index modal lightbox ke angka 1060 (di atas modal standar 1050)
            $(this).css('z-index', 1060);
        });

        $('#modalLightbox').on('shown.bs.modal', function() {
            // Paksa latar hitam (backdrop) milik lightbox naik ke 1055 agar tidak menutupi modal verifikasi
            $('.modal-backdrop:last').css('z-index', 1055);
        });

        // Konfigurasi Standar SweetAlert2 Mungil untuk kenyamanan Mobile
        const Toast = Swal.mixin({
            width: '320px',
            customClass: {
                title: 'fs-5',
                content: 'fs-6'
            }
        });

        // Reset Modal saat ditutup
        $('#modalTambahAnomali').on('hidden.bs.modal', function() {
            $('#form_simpan_anomali')[0].reset();
            $('#area_hasil_pencarian').hide();
            $('#modal_footer_anomali').hide();
            $('#search_nik').val('');
        });

        // Eksekusi Pencarian NIK
        $('#btnCariNik').on('click', function() {
            var nikInput = $('#search_nik').val().trim();
            var btn = $(this);
            var originalBtnText = btn.html();

            if (nikInput.length === 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Kolom NIK tidak boleh kosong!'
                });
                return;
            }

            // Loading state
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                type: "POST",
                url: "<?= base_url('verval/anomali/search_nik_ajax') ?>",
                data: {
                    nik: nikInput,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalBtnText);

                    if (res.status === 'success') {
                        // Tampilkan area form
                        $('#area_hasil_pencarian').fadeIn();
                        $('#modal_footer_anomali').fadeIn();

                        // Distribusi data ke masing-masing input
                        var d = res.data;
                        $('#val_nik').val(d.nik);
                        $('#val_no_kk').val(d.no_kk);
                        $('#val_nama_lengkap').val(d.nama_lengkap);
                        $('#val_ibu_kandung').val(d.ibu_kandung);
                        $('#val_tempat_lahir').val(d.tempat_lahir);
                        $('#val_tanggal_lahir').val(d.tanggal_lahir);
                        // 🚀 Simpan Kode Asli (L/P) ke Hidden Input
                        $('#val_jenis_kelamin').val(d.jenis_kelamin);

                        // 🚀 Tampilkan Label (Laki-Laki/Perempuan) ke Input Text Readonly
                        $('#val_jenis_kelamin_nama').val(d.jenis_kelamin === 'L' ? 'LAKI-LAKI' : (d.jenis_kelamin === 'P' ? 'PEREMPUAN' : '-'));
                        // Simpan Kode Asli ke Hidden Input
                        $('#val_status_kawin').val(d.status_kawin);
                        $('#val_pekerjaan').val(d.pekerjaan);
                        $('#val_shdk').val(d.shdk);
                        $('#val_desa').val(d.desa);
                        $('#val_kecamatan').val(d.kecamatan);
                        $('#val_kabupaten').val(d.kabupaten);
                        $('#val_provinsi').val(d.provinsi);

                        // 🚀 Set ID Petugas yang Ditemukan
                        $('#val_petugas_entri_id').val(d.petugas_entri_id);

                        // Tampilkan Nama/Label ke Input Text Readonly
                        $('#val_status_kawin_nama').val(d.status_kawin_nama);
                        $('#val_pekerjaan_nama').val(d.pekerjaan_nama);
                        $('#val_shdk_nama').val(d.shdk_nama);
                        $('#val_desa_nama').val(d.desa_nama);
                        $('#val_kecamatan_nama').val(d.kecamatan_nama);
                        $('#val_kabupaten_nama').val(d.kabupaten_nama);
                        $('#val_provinsi_nama').val(d.provinsi_nama);


                        $('#val_alamat').val(d.alamat);
                        $('#val_rt').val(d.rt);
                        $('#val_rw').val(d.rw);

                        // Optional: Set ID Petugas Entri
                        // Di sini Kang Rian bisa menyisipkan logika penentuan ID petugas
                        // $('#val_petugas_entri_id').val(...);

                    } else {
                        // Sembunyikan area form jika gagal
                        $('#area_hasil_pencarian').hide();
                        $('#modal_footer_anomali').hide();

                        Toast.fire({
                            icon: 'error',
                            title: 'Tidak Ditemukan',
                            text: res.message
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalBtnText);
                    console.error(xhr.responseText);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Gagal terhubung ke server.'
                    });
                }
            });
        });

        // Eksekusi Submit Form (Simpan Anomali + Upload)
        $('#form_simpan_anomali').on('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);
            var btn = $('#btnSubmitAnomali');
            var originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                type: "POST",
                url: "<?= base_url('verval/anomali/simpan') ?>",
                data: formData,
                processData: false, // Wajib false untuk file upload
                contentType: false, // Wajib false untuk file upload
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);

                    if (res.status === 'success') {
                        // 🚀 JURUS PAMUNGKAS: Klik paksa tombol yang punya atribut data-dismiss="modal"
                        $('#modalTambahAnomali').find('[data-dismiss="modal"]').trigger('click');

                        Toast.fire({
                            icon: 'success',
                            title: 'Sukses! 🎉',
                            text: res.message,
                            timer: 2500,
                            showConfirmButton: false
                        });

                        // Reload tabel secara diam-diam
                        if (typeof tableAnomali !== 'undefined') {
                            tableAnomali.ajax.reload(null, false);
                        }
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    console.error(xhr.responseText);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Terjadi kesalahan sistem saat menyimpan.'
                    });
                }
            });
        });

        // --------------------------------------------------------
        // 🚀 FITUR PREVIEW GAMBAR
        // --------------------------------------------------------
        $('#bukti_siksng').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                // Validasi ukuran max 2MB (Opsional tapi direkomendasikan)
                if (file.size > 2097152) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Maksimal ukuran file adalah 2MB!'
                    });
                    $(this).val('');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_bukti').attr('src', e.target.result);
                    $('#preview_area').slideDown('fast'); // Munculkan dengan animasi mulus
                }
                reader.readAsDataURL(file);
            }
        });

        // 🚀 FITUR HAPUS PREVIEW
        $('#btnHapusPreview').on('click', function() {
            $('#bukti_siksng').val(''); // Kosongkan input file asli
            $('#preview_area').slideUp('fast', function() {
                $('#preview_bukti').attr('src', ''); // Bersihkan source gambar
            });
        });

        // 🚀 UPDATE: Reset Modal (Tambahkan baris reset preview ini ke fungsi hidden.bs.modal yang sudah ada)
        $('#modalTambahAnomali').on('hidden.bs.modal', function() {
            $('#form_simpan_anomali')[0].reset();
            $('#area_hasil_pencarian').hide();
            $('#modal_footer_anomali').hide();
            $('#search_nik').val('');

            // Baris tambahan untuk mereset gambar saat modal ditutup (Batal)
            $('#preview_area').hide();
            $('#preview_bukti').attr('src', '');
        });

        // --------------------------------------------------------
        // 🚀 LOGIKA TINDAK LANJUT PETUGAS ENTRI
        // --------------------------------------------------------

        // 1. Klik Tombol Perbaiki (Buka Modal)
        $(document).on('click', '.btn-tindak-lanjut', function() {
            var id_anomali = $(this).data('id');
            var btn = $(this);
            var originalBtn = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: "<?= base_url('verval/anomali/get_detail_ajax/') ?>" + id_anomali,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalBtn);
                    if (res.status === 'success') {
                        // Isi form hidden & teks informasi
                        $('#edit_id_anomali').val(res.data.id_anomali);
                        $('#text_edit_nik').text(': ' + res.data.nik);
                        $('#text_edit_nama').text(': ' + res.data.nama_lengkap);

                        // Tampilkan catatan jika ditolak
                        if (res.data.status_anomali === 'rejected' && res.data.catatan_penolakan) {
                            $('#text_catatan_penolakan').text(res.data.catatan_penolakan);
                            $('#area_catatan_penolakan').show();
                        } else {
                            $('#area_catatan_penolakan').hide();
                        }

                        $('#modalTindakLanjut').modal('show');
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(originalBtn);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil data.'
                    });
                }
            });
        });

        // 2. Preview Gambar KK Baru
        $('#foto_kk_baru').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                if (file.size > 2097152) { // Max 2MB
                    Toast.fire({
                        icon: 'error',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Maksimal ukuran file adalah 2MB!'
                    });
                    $(this).val('');
                    return;
                }
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_kk').attr('src', e.target.result);
                    $('#preview_area_kk').slideDown('fast');
                }
                reader.readAsDataURL(file);
            }
        });

        // 3. Reset Modal saat ditutup
        $('#modalTindakLanjut').on('hidden.bs.modal', function() {
            $('#form_update_petugas')[0].reset();
            $('#preview_area_kk').hide();
            $('#preview_kk').attr('src', '');
            $('#area_catatan_penolakan').hide();
        });

        // 4. Submit Form Update Petugas
        $('#form_update_petugas').on('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);
            var btn = $('#btnSubmitPetugas');
            var originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                type: "POST",
                url: "<?= base_url('verval/anomali/update_petugas') ?>",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);

                    if (res.status === 'success') {
                        $('#modalTindakLanjut').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: 'Terkirim! 🎉',
                            text: res.message,
                            timer: 2500,
                            showConfirmButton: false
                        });

                        tableAnomali.ajax.reload(null, false);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Terjadi kesalahan saat menyimpan.'
                    });
                }
            });
        });

        // --------------------------------------------------------
        // 🚀 FUNGSI HELPER UNTUK LOAD DROPDOWN AJAX
        // --------------------------------------------------------
        function loadDropdownWilayah(url, targetElement, selectedValue, defaultText) {
            return $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    let html = `<option value="">- ${defaultText} -</option>`;
                    res.forEach(item => {
                        let sel = (item.id == selectedValue) ? 'selected' : '';
                        html += `<option value="${item.id}" ${sel}>${item.name}</option>`;
                    });
                    $(targetElement).html(html);
                }
            });
        }

        // --------------------------------------------------------
        // 🚀 FUNGSI HELPER UNTUK LOAD SEMUA DROPDOWN AJAX
        // --------------------------------------------------------
        function loadDropdownAjax(url, targetElement, selectedValue, defaultText) {
            return $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    let html = `<option value="">- ${defaultText} -</option>`;
                    res.forEach(item => {
                        let sel = (item.id == selectedValue) ? 'selected' : '';
                        html += `<option value="${item.id}" ${sel}>${item.name}</option>`;
                    });
                    $(targetElement).html(html);
                }
            });
        }

        // 🚀 PAKSA TUTUP MODAL JIKA ATRIBUT BAWAAN MACET
        $('.btn-tutup-modal').on('click', function() {
            $('#modalTindakLanjut').modal('hide');
        });

        // --------------------------------------------------------
        // 🚀 BUKA MODAL & AUTO-FILL DATA (REFERENSI + WILAYAH)
        // --------------------------------------------------------
        $(document).on('click', '.btn-tindak-lanjut', function() {
            var id_anomali = $(this).data('id');
            var btn = $(this);
            var originalBtn = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: "<?= base_url('verval/anomali/get_detail_ajax/') ?>" + id_anomali,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalBtn);
                    if (res.status === 'success') {
                        let d = res.data;

                        // 1. Isi Form Text
                        $('#edit_id_anomali').val(d.id_anomali);
                        $('#edit_nik').val(d.nik);
                        $('#edit_no_kk').val(d.no_kk);
                        $('#edit_nama_lengkap').val(d.nama_lengkap);
                        $('#edit_ibu_kandung').val(d.ibu_kandung);
                        $('#edit_tempat_lahir').val(d.tempat_lahir);
                        $('#edit_tanggal_lahir').val(d.tanggal_lahir);
                        $('#edit_jenis_kelamin').val(d.jenis_kelamin);
                        $('#edit_alamat').val(d.alamat);
                        $('#edit_rt').val(d.rt);
                        $('#edit_rw').val(d.rw);

                        if (d.status_anomali === 'rejected' && d.catatan_penolakan) {
                            $('#text_catatan_penolakan').text(d.catatan_penolakan);
                            $('#area_catatan_penolakan').show();
                        } else {
                            $('#area_catatan_penolakan').hide();
                        }

                        // 2. 🚀 EKSEKUSI DROPDOWN AJAX SECARA BERURUTAN
                        // Load Referensi Dulu
                        loadDropdownAjax("<?= base_url('verval/anomali/get_referensi/shdk') ?>", '#edit_shdk', d.shdk, 'Pilih SHDK')
                            .then(() => loadDropdownAjax("<?= base_url('verval/anomali/get_referensi/status_kawin') ?>", '#edit_status_kawin', d.status_kawin, 'Pilih Status'))
                            .then(() => loadDropdownAjax("<?= base_url('verval/anomali/get_referensi/pekerjaan') ?>", '#edit_pekerjaan', d.pekerjaan, 'Pilih Pekerjaan'))

                            // Lanjut Load Wilayah
                            .then(() => loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/provinsi') ?>", '#edit_provinsi', d.provinsi, 'Pilih Provinsi'))
                            .then(() => {
                                if (d.provinsi) return loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/kabupaten/') ?>" + d.provinsi, '#edit_kabupaten', d.kabupaten, 'Pilih Kab/Kota');
                            })
                            .then(() => {
                                if (d.kabupaten) return loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/kecamatan/') ?>" + d.kabupaten, '#edit_kecamatan', d.kecamatan, 'Pilih Kecamatan');
                            })
                            .then(() => {
                                if (d.kecamatan) return loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/desa/') ?>" + d.kecamatan, '#edit_desa', d.desa, 'Pilih Desa');
                            })
                            .then(() => {
                                // Tampilkan modal setelah SEMUA dropdown selesai ditarik dari database
                                $('#modalTindakLanjut').modal('show');
                            });

                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(originalBtn);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil data.'
                    });
                }
            });
        });

        // Sesuaikan sedikit Helper Event Change Wilayah yang kemarin menggunakan 'loadDropdownWilayah' menjadi 'loadDropdownAjax'
        $('#edit_provinsi').on('change', function() {
            var id = $(this).val();
            $('#edit_kabupaten').html('<option value="">- Pilih Kab/Kota -</option>');
            $('#edit_kecamatan').html('<option value="">- Pilih Kecamatan -</option>');
            $('#edit_desa').html('<option value="">- Pilih Desa -</option>');
            if (id) loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/kabupaten/') ?>" + id, '#edit_kabupaten', '', 'Pilih Kab/Kota');
        });

        $('#edit_kabupaten').on('change', function() {
            var id = $(this).val();
            $('#edit_kecamatan').html('<option value="">- Pilih Kecamatan -</option>');
            $('#edit_desa').html('<option value="">- Pilih Desa -</option>');
            if (id) loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/kecamatan/') ?>" + id, '#edit_kecamatan', '', 'Pilih Kecamatan');
        });

        $('#edit_kecamatan').on('change', function() {
            var id = $(this).val();
            $('#edit_desa').html('<option value="">- Pilih Desa -</option>');
            if (id) loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/desa/') ?>" + id, '#edit_desa', '', 'Pilih Desa');
        });

        // 🚀 PAKSA TUTUP MODAL VERIFIKASI JIKA MACET
        $('.btn-tutup-verifikasi').on('click', function() {
            $('#modalVerifikasiOperator').modal('hide');
        });

        // --------------------------------------------------------
        // 🚀 BUKA MODAL VERIFIKASI (ROLE < 4)
        // --------------------------------------------------------
        $(document).on('click', '.btn-verifikasi', function() {
            var id_anomali = $(this).data('id');
            var btn = $(this);
            var originalBtn = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: "<?= base_url('verval/anomali/get_detail_ajax/') ?>" + id_anomali,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalBtn);
                    if (res.status === 'success') {
                        let d = res.data;

                        // 1. Isi Form Hidden & Preview Gambar
                        $('#verif_id_anomali').val(d.id_anomali);
                        $('#verif_img_siksng').attr('src', "<?= base_url('uploads/anomali/') ?>" + d.bukti_siksng);
                        $('#verif_img_siksng').attr('data-img', "<?= base_url('uploads/anomali/') ?>" + d.bukti_siksng);

                        if (d.foto_kk_baru) {
                            $('#verif_img_kk').attr('src', "<?= base_url('uploads/anomali/') ?>" + d.foto_kk_baru);
                            $('#verif_img_kk').attr('data-img', "<?= base_url('uploads/anomali/') ?>" + d.foto_kk_baru);
                            $('#verif_img_kk').show();
                        } else {
                            $('#verif_img_kk').hide();
                        }

                        // 2. Isi Form Text (Data Asli Utuh tanpa Masking di Modal)
                        $('#verif_nik').val(d.nik);
                        $('#verif_no_kk').val(d.no_kk);

                        // Isi data biasa
                        $('#verif_nama_lengkap').val(d.nama_lengkap);
                        $('#verif_ibu_kandung').val(d.ibu_kandung);
                        $('#verif_tempat_lahir').val(d.tempat_lahir);
                        // ... (lanjutannya tetap sama)
                        $('#verif_tanggal_lahir').val(d.tanggal_lahir);
                        $('#verif_jenis_kelamin').val(d.jenis_kelamin);
                        $('#verif_alamat').val(d.alamat);
                        $('#verif_rt').val(d.rt);
                        $('#verif_rw').val(d.rw);

                        // 3. 🚀 EKSEKUSI DROPDOWN AJAX SECARA BERURUTAN (Disabled)
                        loadDropdownAjax("<?= base_url('verval/anomali/get_referensi/shdk') ?>", '#verif_shdk', d.shdk, 'Pilih SHDK')
                            .then(() => loadDropdownAjax("<?= base_url('verval/anomali/get_referensi/status_kawin') ?>", '#verif_status_kawin', d.status_kawin, 'Pilih Status'))
                            .then(() => loadDropdownAjax("<?= base_url('verval/anomali/get_referensi/pekerjaan') ?>", '#verif_pekerjaan', d.pekerjaan, 'Pilih Pekerjaan'))
                            .then(() => loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/provinsi') ?>", '#verif_provinsi', d.provinsi, 'Pilih Provinsi'))
                            .then(() => {
                                if (d.provinsi) return loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/kabupaten/') ?>" + d.provinsi, '#verif_kabupaten', d.kabupaten, 'Pilih Kab/Kota');
                            })
                            .then(() => {
                                if (d.kabupaten) return loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/kecamatan/') ?>" + d.kabupaten, '#verif_kecamatan', d.kecamatan, 'Pilih Kecamatan');
                            })
                            .then(() => {
                                if (d.kecamatan) return loadDropdownAjax("<?= base_url('verval/anomali/get_wilayah/desa/') ?>" + d.kecamatan, '#verif_desa', d.desa, 'Pilih Desa');
                            })
                            .then(() => {
                                // Tampilkan modal verifikasi
                                $('#modalVerifikasiOperator').modal('show');
                            });

                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(originalBtn);
                    Toast.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil data.'
                    });
                }
            });
        });

        // --------------------------------------------------------
        // 🚀 AKSI TOMBOL DI DALAM MODAL VERIFIKASI
        // --------------------------------------------------------

        // Tombol SETUJU (Verifikasi Valid)
        $('#btnAksiVerifikasi').on('click', function() {
            var id_anomali = $('#verif_id_anomali').val();

            Swal.fire({
                title: 'Konfirmasi Verifikasi',
                text: 'Apakah Anda yakin data ini sudah padan dan valid?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-check"></i> Ya, Verifikasi!',
                cancelButtonText: 'Batal',
                width: '350px' // Mobile friendly
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modalVerifikasiOperator').modal('hide');
                    setTimeout(() => kirimVerifikasiAjax(id_anomali, 'verified', ''), 300);
                }
            });
        });

        // Tombol TOLAK (Minta Alasan)
        $('#btnAksiTolak').on('click', function() {
            var id_anomali = $('#verif_id_anomali').val();

            Swal.fire({
                title: 'Alasan Penolakan',
                input: 'textarea',
                inputPlaceholder: 'Tuliskan bagian mana yang salah agar Petugas bisa memperbaikinya...',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Kirim Penolakan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                width: '350px', // Mobile friendly
                preConfirm: (text) => {
                    if (!text || text.trim() === '') {
                        Swal.showValidationMessage('Catatan penolakan wajib diisi!');
                    }
                    return text;
                }
            }).then((resTolak) => {
                if (resTolak.isConfirmed) {
                    $('#modalVerifikasiOperator').modal('hide');
                    setTimeout(() => kirimVerifikasiAjax(id_anomali, 'rejected', resTolak.value), 300);
                }
            });
        });

        // --------------------------------------------------------
        // 🚀 FUNGSI HELPER UNTUK KIRIM AJAX VERIFIKASI KE BACKEND
        // --------------------------------------------------------
        function kirimVerifikasiAjax(id, status, catatan) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                width: '320px',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: "<?= base_url('verval/anomali/proses_verifikasi') ?>",
                data: {
                    id_anomali: id,
                    status: status,
                    catatan_penolakan: catatan,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        // Notifikasi sukses mungil di pojok
                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reload DataTables secara diam-diam
                        if (typeof tableAnomali !== 'undefined') {
                            tableAnomali.ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                            width: '320px'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Gagal menghubungi server.',
                        width: '320px'
                    });
                }
            });
        }

        // --------------------------------------------------------
        // 🚀 FITUR REVEAL DATA SENSITIF (HOVER & CLICK/TAP)
        // --------------------------------------------------------
        $(document).on('mouseenter click', '.sensitive-data', function() {
            // Tampilkan data asli saat disentuh/diklik
            $(this).text($(this).data('original'));
        }).on('mouseleave', '.sensitive-data', function() {
            // Kembalikan ke bintang-bintang saat kursor pergi
            $(this).text($(this).data('masked'));
        });

    });
</script>
<?= $this->endSection(); ?>