<?php
// Tangkap informasi sesi role di dalam modal
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
$editable = ($roleId <= 4);
?>
<div class="modal fade" id="modalAnggota" tabindex="-1" aria-labelledby="modalAnggotaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="formAnggota" autocomplete="off">
                <input type="hidden" id="id_kk" name="id_kk" value="<?= $payload['id_kk'] ?? '' ?>">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalAnggotaLabel">🧍‍♂️ Pembaruan Data Individu</h5>
                    <!-- 🚀 PERBAIKAN: Gunakan <button> agar fokus terlepas dengan benar saat ditutup -->
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y:auto;">
                    <ul class="nav nav-tabs" id="tabAnggotaTabs" role="tablist">
                        <li class="nav-item">
                            <a href="javascript:void(0)" class="nav-link active" id="tab-identitas-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-identitas" role="tab" aria-controls="tab-identitas"
                                aria-selected="true" style="display:block !important; text-decoration:none;">
                                📋 Data Pokok Individu <span class="badge bg-secondary ms-1" id="badgeIdentitas">⚠️</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="javascript:void(0)" class="nav-link" id="tab-pendidikan-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-pendidikan" role="tab" style="display:block !important; text-decoration:none;">
                                🎓 Pendidikan <span class="badge bg-secondary ms-1" id="badgePendidikan">⚠️</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="javascript:void(0)" class="nav-link" id="tab-kerja-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-kerja" role="tab" style="display:block !important; text-decoration:none;">
                                💼 Tenaga Kerja <span class="badge bg-secondary ms-1" id="badgeKerja">⚠️</span>
                            </a>
                        </li>
                        <!-- 🚀 TAB KEPEMILIKAN USAHA SUDAH DIHAPUS SESUAI INSTRUKSI -->
                        <li class="nav-item" role="presentation">
                            <a href="javascript:void(0)" class="nav-link" id="tab-kesehatan-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-kesehatan" role="tab" style="display:block !important; text-decoration:none;">
                                ❤️ Kesehatan <span class="badge bg-secondary ms-1" id="badgeKesehatan">⚠️</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">

                        <!-- ========================================== -->
                        <!-- 1. TAB IDENTITAS -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade show active" id="tab-identitas" role="tabpanel">
                            <div class="row g-4">
                                <div class="row align-items-center border-bottom pb-1 mt-3 mb-1">
                                    <label class="col-md-5 col-12 col-form-label fw-bold">Status Keberadaan</label>
                                    <div class="col-md-7 col-12">
                                        <select class="form-select required" name="status_keberadaan" id="status_keberadaan">
                                            <option value="">Pilih...</option>
                                            <option>Belum Ditentukan</option>
                                            <option>Tinggal Bersama Keluarga</option>
                                            <option>Meninggal</option>
                                            <option>Tidak Tinggal Bersama Keluarga/Pindah Ke Wilayah Lain</option>
                                            <option>Tidak Tinggal Bersama Keluarga/Pindah Ke Luar Negeri</option>
                                            <option>Tidak Ditemukan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nama Lengkap</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required upper" name="nama" id="nama">
                                                <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#nama" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">NIK</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required onlynum16" name="nik" id="nik" maxlength="16" inputmode="numeric" pattern="[0-9]*">
                                                <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#nik" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nomor KK</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required onlynum16" name="individu_no_kk" id="individu_no_kk" maxlength="16" inputmode="numeric" pattern="[0-9]*">
                                                <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#individu_no_kk" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 🚀 ELEMEN BARU BPS: NOMOR HANDPHONE -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nomor Handphone</label>
                                            <div class="input-group has-validation">
                                                <input type="text" class="form-control onlynum" name="no_hp" id="no_hp" minlength="10" maxlength="13" inputmode="numeric" pattern="[0-9]*" placeholder="Contoh: 08123456789">
                                                <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#no_hp" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <!-- Catatan: Untuk input-group, validasi perlu bantuan class 'has-validation' di container -->
                                                <div class="invalid-feedback small fw-bold w-100">Nomor Handphone harus 10-13 digit angka dan diawali 08</div>
                                            </div>
                                            <small class="text-muted fst-italic">Kosongkan bagian ini jika yang bersangkutan tidak memiliki nomor handphone.</small>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal Lahir</label>
                                            <input type="date" class="form-control required" name="tanggal_lahir" id="tanggal_lahir">
                                        </div> -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal Lahir</label>
                                            <div class="input-group has-validation">
                                                <input type="text" class="form-control required" id="tanggal_lahir_display" placeholder="DD-MM-YYYY" maxlength="10" inputmode="numeric">
                                                <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#tanggal_lahir_display" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <!-- 🚀 PESAN ERROR KALENDER -->
                                                <div class="invalid-feedback small fw-bold w-100">Format tanggal tidak valid atau tidak masuk akal.</div>
                                            </div>
                                            <input type="hidden" name="tanggal_lahir" id="tanggal_lahir">
                                            <small class="text-muted fst-italic">Ketik angka saja, strip otomatis (Contoh: 31121992)</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tempat Lahir</label>
                                            <input type="text" class="form-control required upper" name="tempat_lahir" id="tempat_lahir">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Hubungan dengan Kepala Keluarga</label>
                                            <select class="form-select required" name="hubungan" id="hubungan"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Status Kawin</label>
                                            <select class="form-select required" name="status_kawin" id="status_kawin"></select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nama Ibu Kandung</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required upper" name="ibu_kandung" id="ibu_kandung">
                                                <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#ibu_kandung" title="Salin">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Pendidikan Terakhir</label>
                                            <select class="form-select required" name="pendidikan_terakhir" id="pendidikan_terakhir"></select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Pekerjaan</label>
                                            <select class="form-select required" name="pekerjaan" id="pekerjaan"></select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Jenis Kelamin</label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" id="jkL">
                                                    <label class="form-check-label" for="jkL">Laki-laki</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" id="jkP">
                                                    <label class="form-check-label" for="jkP">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold border-bottom pb-1">Wilayah Capil (Sesuai Data Kependudukan)</label>
                                            <div class="row g-2 mt-1">
                                                <div class="col-md-3">
                                                    <label class="form-label small">Provinsi</label>
                                                    <select class="form-select required" id="ind_provinsi" name="provinsi"></select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Kabupaten/Kota</label>
                                                    <select class="form-select required" id="ind_kabupaten" name="kabupaten"></select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Kecamatan</label>
                                                    <select class="form-select required" id="ind_kecamatan" name="kecamatan"></select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Kel/Desa</label>
                                                    <select class="form-select required" id="ind_desa" name="desa"></select>
                                                </div>
                                            </div>
                                            <div class="mb-3 mt-3">
                                                <label class="form-label fw-semibold">Alamat Domisili</label>
                                                <div class="bg-light border rounded-3 p-3 small">
                                                    <div>
                                                        <strong>RW:</strong> <?= esc($payload['perumahan']['rw'] ?? '-') ?> |
                                                        <strong>RT:</strong> <?= esc($payload['perumahan']['rt'] ?? '-') ?> |
                                                        <?= esc($payload['perumahan']['alamat'] ?? '-') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 2. TAB PENDIDIKAN -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade" id="tab-pendidikan" role="tabpanel" aria-labelledby="tab-pendidikan-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Partisipasi Sekolah</label>
                                    <select class="form-select required" name="partisipasi_sekolah" id="partisipasi_sekolah">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Pernah Sekolah">Belum Pernah Sekolah</option>
                                        <option value="Masih Sekolah">Masih Sekolah</option>
                                        <option value="Tidak Bersekolah Lagi">Tidak Bersekolah Lagi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenjang & Jenis Pendidikan</label>
                                    <select class="form-select" name="jenjang_pendidikan" id="jenjang_pendidikan">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Ditentukan">Belum Ditentukan</option>
                                        <option value="Tidak Punya Ijazah SD">Tidak Punya Ijazah SD</option>
                                        <option value="Paket A">Paket A</option>
                                        <option value="SDLB">SDLB</option>
                                        <option value="SD">SD</option>
                                        <option value="MI">MI</option>
                                        <option value="SPM/PDF Ula">SPM/PDF Ula</option>
                                        <option value="Paket B">Paket B</option>
                                        <option value="SMP LB">SMP LB</option>
                                        <option value="SMP">SMP</option>
                                        <option value="MTS">MTS</option>
                                        <option value="SPM/PDF Wustha">SPM/PDF Wustha</option>
                                        <option value="Paket C">Paket C</option>
                                        <option value="SMLB">SMLB</option>
                                        <option value="SMA">SMA</option>
                                        <option value="MA">MA</option>
                                        <option value="SMK">SMK</option>
                                        <option value="MAK">MAK</option>
                                        <option value="SPM/PDF Ulya">SPM/PDF Ulya</option>
                                        <option value="DI/D2/D3">DI/D2/D3</option>
                                        <option value="D4/S1">D4/S1</option>
                                        <option value="Profesi">Profesi</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                    <!-- 🚀 ELEMEN ERROR REAL-TIME -->
                                    <div id="fb_jenjang" class="invalid-feedback small fw-bold"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kelas Tertinggi yang Pernah Ditamatkan</label>
                                    <select class="form-select" name="kelas_tertinggi" id="kelas_tertinggi">
                                        <option value="">Pilih...</option>
                                        <option value="Tidak Punya Ijazah">Tidak Punya Ijazah</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8 (Tamat & Lulus)">8 (Tamat & Lulus)</option>
                                    </select>
                                    <!-- 🚀 ELEMEN ERROR REAL-TIME -->
                                    <div id="fb_kelas" class="invalid-feedback small fw-bold"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ijazah/STTB Tertinggi</label>
                                    <select class="form-select" name="ijazah_tertinggi" id="ijazah_tertinggi">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Ditentukan">Belum Ditentukan</option>
                                        <option value="Tidak Punya Ijazah SD">Tidak Punya Ijazah SD</option>
                                        <option value="Paket A">Paket A</option>
                                        <option value="SDLB">SDLB</option>
                                        <option value="SD">SD</option>
                                        <option value="MI">MI</option>
                                        <option value="SPM/PDF Ula">SPM/PDF Ula</option>
                                        <option value="Paket B">Paket B</option>
                                        <option value="SMP LB">SMP LB</option>
                                        <option value="SMP">SMP</option>
                                        <option value="MTS">MTS</option>
                                        <option value="SPM/PDF Wustha">SPM/PDF Wustha</option>
                                        <option value="Paket C">Paket C</option>
                                        <option value="SMLB">SMLB</option>
                                        <option value="SMA">SMA</option>
                                        <option value="MA">MA</option>
                                        <option value="SMK">SMK</option>
                                        <option value="MAK">MAK</option>
                                        <option value="SPM/PDF Ulya">SPM/PDF Ulya</option>
                                        <option value="DI/D2/D3">DI/D2/D3</option>
                                        <option value="D4/S1">D4/S1</option>
                                        <option value="Profesi">Profesi</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                    <!-- 🚀 ELEMEN ERROR REAL-TIME -->
                                    <div id="fb_ijazah" class="invalid-feedback small fw-bold"></div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 3. TAB TENAGA KERJA -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade" id="tab-kerja" role="tabpanel" aria-labelledby="tab-kerja-tab">
                            <div class="row g-4">

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Jenis Lapangan Usaha / Profesi Pekerjaan Utama</label>
                                    <select class="form-select select2-init" name="lapangan_usaha" id="lapangan_usaha">
                                        <option value="">Pilih...</option>
                                        <?php
                                        // 🚀 185 OPSI PROFESI BPS
                                        $profesiList = [
                                            "Tidak Bekerja",
                                            "Agen Tenaga Kerja",
                                            "Ahli Sejarah dan Cagar budaya",
                                            "Akuntan",
                                            "Analis Keuangan",
                                            "Anggota DPD",
                                            "Anggota DPR RI/MPR RI",
                                            "Anggota DPRD Provinsi/ Anggota DPRD Kabupaten/Kota",
                                            "Apoteker",
                                            "Arsiparis",
                                            "Arsitek",
                                            "Asisten Apoteker",
                                            "Atase",
                                            "Atlet/Olahragawan",
                                            "Awak Kapal",
                                            "Bhikkhu",
                                            "Biarawan",
                                            "Biarawati",
                                            "Bidan",
                                            "Broker/Pialang Saham",
                                            "Bupati",
                                            "Buruh Angkut Barang",
                                            "Buruh Bangunan",
                                            "Buruh Industri",
                                            "Buruh Perikanan",
                                            "Buruh Pertambangan",
                                            "Buruh Pertanian/Kehutanan",
                                            "Buruh Peternakan",
                                            "Camat",
                                            "Chef",
                                            "Chief Executive Officer (CEO)",
                                            "Dokter Gigi",
                                            "Dokter Hewan",
                                            "Dokter Spesialis",
                                            "Dokter Umum",
                                            "Dosen",
                                            "Duta Besar",
                                            "Empu Keris",
                                            "Fotografer",
                                            "Gembala",
                                            "Gubernur",
                                            "Guru",
                                            "Hakim",
                                            "Hakim Agung",
                                            "Imam Masjid",
                                            "Jaksa",
                                            "Jaksa Agung",
                                            "Jiaosheng",
                                            "Juru Gambar Teknik/Drafter",
                                            "Kameramen",
                                            "Kapten Kapal",
                                            "Kasir",
                                            "Kepala Desa",
                                            "Ketua Adat",
                                            "Ketua Organisasi",
                                            "Konsultan",
                                            "Kreator Konten",
                                            "Kurator",
                                            "Kurir",
                                            "Lurah",
                                            "Makelar",
                                            "Manajer",
                                            "Masinis",
                                            "Mekanik",
                                            "Menteri/Kepala Badan (setingkat Menteri)/Wakil Menteri/Wakil Kepala Badan",
                                            "Nakhoda",
                                            "Nelayan",
                                            "Notaris",
                                            "Operator Layanan Pelanggan (Customer Service)",
                                            "Operator Mesin",
                                            "Pandita",
                                            "Panitera Pengadilan",
                                            "Paraji",
                                            "Paranormal",
                                            "Pastor",
                                            "Pedagang",
                                            "Pedagang Asongan/Keliling Makanan",
                                            "Pedagang Asongan/Keliling Nonmakanan",
                                            "Pedagang Online",
                                            "Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)",
                                            "Pekerja Garmen/Konveksi",
                                            "Pekerja Percetakan",
                                            "Pekerja Profesional Penjualan (agen asuransi, sales penjualan, dll)",
                                            "Pekerja Sosial",
                                            "Pelaku Ekosistem Musik",
                                            "Pelaku Ekosistem Perfilman",
                                            "Pelaku Ekosistem Seni Pertunjukan",
                                            "Pelaku Ekosistem Seni Rupa dan Kriya",
                                            "Pelatih/ Instruktur Olahraga",
                                            "Pelayan Toko",
                                            "Pembantu/Asisten Rumah Tangga",
                                            "Pemberi Pinjaman",
                                            "Pembuat Makanan/Juru Masak",
                                            "Pembuat Minuman (Barista, Bartender, dll)",
                                            "Pembuat Rokok/Cerutu/Tembakau Gulung",
                                            "Pembuat Sepatu dan Tas",
                                            "Pembudi Daya Ikan dan Biota Air Lainnya",
                                            "Pemulung",
                                            "Penagih Hutang (Debt Collector)",
                                            "Penasihat Spiritual",
                                            "Penata Busana",
                                            "Penata Rambut",
                                            "Penata Rias",
                                            "Penata Suara",
                                            "Pendeta",
                                            "Peneliti",
                                            "Penerjemah",
                                            "Pengacara",
                                            "Pengasuh Anak (Baby Sitter)",
                                            "Pengelola Gedung/Properti",
                                            "Pengemudi Ojek Online",
                                            "Pengemudi Ojek Pangkalan",
                                            "Pengepul",
                                            "Penjaga Keamanan/Satpam",
                                            "Penjahit",
                                            "Penulis",
                                            "Penyelenggara Acara (Event Organizer/EO)",
                                            "Penyiar Radio",
                                            "Penyiar Televisi",
                                            "Perajin Batu",
                                            "Perajin Kayu, Bambu, dan Anyaman",
                                            "Perajin Kulit dan Tekstil",
                                            "Perajin Logam",
                                            "Perajin Perhiasan",
                                            "Perajin Tembikar/Keramik",
                                            "Peramal",
                                            "Perancang Busana/Desainer",
                                            "Perangkat Desa",
                                            "Perawat",
                                            "Petani/Pekebun/Petani Hutan",
                                            "Peternak",
                                            "Petugas Pemadam Kebakaran",
                                            "Petugas Stasiun Pengisian Bahan Bakar",
                                            "Pilot",
                                            "Pinandita",
                                            "PNS Fungsional Tertentu",
                                            "PNS Fungsional Umum",
                                            "PNS Struktural",
                                            "Polisi",
                                            "Pramugara/i",
                                            "Pramusaji",
                                            "Presiden",
                                            "Programer",
                                            "Psikiater",
                                            "Psikolog",
                                            "Pustakawan",
                                            "Resepsionis",
                                            "Sekretaris",
                                            "Seniman/Artis",
                                            "Sopir",
                                            "Supervisor/Mandor",
                                            "Tabib",
                                            "Teknisi",
                                            "Teller Bank",
                                            "Tenaga Cuci",
                                            "Tenaga Humas",
                                            "Tenaga Kebersihan",
                                            "Tenaga Tata Usaha",
                                            "Tentara Nasional Indonesia (TNI)",
                                            "Tukang Bangunan",
                                            "Tukang Cat",
                                            "Tukang Cukur",
                                            "Tukang Fotokopi",
                                            "Tukang Gigi",
                                            "Tukang Kaca",
                                            "Tukang Kayu",
                                            "Tukang Kunci",
                                            "Tukang Las/Pandai Besi",
                                            "Tukang Listrik",
                                            "Tukang Pijat",
                                            "Tukang Pipa",
                                            "Tukang Sablon",
                                            "Tukang Sol Sepatu",
                                            "Tukang Tambal Ban",
                                            "Tukang Tebang Kayu",
                                            "Uskup",
                                            "Ustaz/Mubalig",
                                            "Wakil Bupati",
                                            "Wakil Gubernur",
                                            "Wakil Presiden",
                                            "Wakil Walikota",
                                            "Walikota",
                                            "Wartawan",
                                            "Wenshi",
                                            "Xueshi",
                                            "Lainnya"
                                        ];
                                        foreach ($profesiList as $p) {
                                            echo "<option value=\"$p\">$p</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- 🚀 BERI ID div_status_pekerjaan PADA BUNGKUS INI -->
                                <div class="col-md-12" id="div_status_pekerjaan">
                                    <label class="form-label fw-bold">Status dalam Pekerjaan Utama</label>
                                    <select class="form-select" name="status_pekerjaan" id="status_pekerjaan">
                                        <option value="">Pilih...</option>
                                        <option value="Berusaha sendiri">Berusaha sendiri</option>
                                        <option value="Berusaha dibantu buruh tidak tetap/tidak dibayar">Berusaha dibantu buruh tidak tetap/tidak dibayar</option>
                                        <option value="Berusaha dibantu buruh tetap/dibayar">Berusaha dibantu buruh tetap/dibayar</option>
                                        <option value="Buruh/karyawan/pegawai swasta">Buruh/karyawan/pegawai swasta</option>
                                        <option value="PNS/TNI/POLRI/BUMN/BUMD/Anggota Legislatif">PNS/TNI/POLRI/BUMN/BUMD/Anggota Legislatif</option>
                                        <option value="Pekerja bebas pertanian">Pekerja bebas pertanian</option>
                                        <option value="Pekerja bebas non-pertanian">Pekerja bebas non-pertanian</option>
                                        <option value="Pekerja keluarga/tidak dibayar">Pekerja keluarga/tidak dibayar</option>
                                    </select>
                                </div>

                                <!-- 🚀 ELEMEN BARU BPS: REKENING / DOMPET DIGITAL -->
                                <div class="col-md-12 border-top pt-3">
                                    <label class="form-label fw-bold text-primary">
                                        Apakah <span class="bg-warning text-dark px-1 rounded label-nama-anggota">Anggota Keluarga ini</span> memiliki rekening aktif atau dompet digital?
                                    </label>
                                    <div class="border border-primary border-opacity-50 rounded p-3 bg-light">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="rekening_aktif" id="rek_usaha" value="Ya untuk usaha">
                                            <label class="form-check-label" for="rek_usaha">Ya untuk usaha</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="rekening_aktif" id="rek_pribadi" value="Ya untuk pribadi">
                                            <label class="form-check-label" for="rek_pribadi">Ya untuk pribadi</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="rekening_aktif" id="rek_keduanya" value="Ya untuk usaha dan pribadi">
                                            <label class="form-check-label" for="rek_keduanya">Ya untuk usaha dan pribadi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="rekening_aktif" id="rek_tidak" value="Tidak ada">
                                            <label class="form-check-label" for="rek_tidak">Tidak ada</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- 4. TAB KESEHATAN -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade" id="tab-kesehatan" role="tabpanel" aria-labelledby="tab-kesehatan-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Hamil <span class="text-muted small">(hanya untuk perempuan)</span></label>
                                    <select class="form-select required" name="status_hamil" id="status_hamil">
                                        <option value="">Pilih...</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Penyandang Disabilitas</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y:auto;">
                                        <?php
                                        $disabilities = ['Fisik', 'Mental', 'Intelektual', 'Sensorik Netra', 'Sensorik Rungu', 'Sensorik Wicara', 'Sensorik Ganda/Multi'];
                                        foreach ($disabilities as $d) {
                                            echo "
                                                <div class='form-check form-check-inline'>
                                                    <input class='form-check-input disab-check' type='checkbox' name='disabilitas[]' value='$d' id='dis_$d'>
                                                    <label class='form-check-label small' for='dis_$d'>$d</label>
                                                </div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-bold">Keluhan Kesehatan Kronis / Menahun</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y:auto; background-color: #f8f9fa;">
                                        <?php
                                        $penyakitKronisList = ['Tidak Ada', 'Hipertensi (darah tinggi)', 'Rematik', 'Asma', 'Masalah jantung', 'Diabetes (kencing manis)', 'Tuberculosis (TBC)', 'Stroke', 'Kanker atau tumor ganas', 'Gagal ginjal', 'Haemophilia', 'HIV/AIDS', 'Kolesterol', 'Sirosis hati', 'Thalasimia', 'Leukimia', 'Alzheimer', 'Lainnya'];

                                        foreach ($penyakitKronisList as $idx => $pk) {
                                            // Menggunakan index ($idx) agar ID elemen aman dari spasi dan tanda kurung
                                            $safeId = 'kronis_' . $idx;
                                            echo "
                                                <div class='form-check mb-2'>
                                                    <input class='form-check-input kronis-check' type='checkbox' name='penyakit_kronis[]' value='$pk' id='$safeId'>
                                                    <label class='form-check-label small text-dark' for='$safeId'>$pk</label>
                                                </div>";
                                        }
                                        ?>
                                    </div>
                                    <small class="text-muted fst-italic mt-1 d-block">Pilih semua keluhan kesehatan yang dialami (bisa lebih dari satu).</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer sticky-bottom bg-light">
                    <?php if ($editable): ?>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan</button>
                    <?php else: ?>
                        <!-- 🚀 PERBAIKAN: Gunakan <button> alih-alih <a> -->
                        <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Tutup
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }

    .modal-body {
        overflow-y: auto;
    }
</style>

<script>
    // Script UI/UX untuk mengganti placeholder "Anggota Keluarga Ini" dengan Nama yang diketik
    document.addEventListener("DOMContentLoaded", function() {
        const inputNama = document.getElementById('nama');
        const labelsNama = document.querySelectorAll('.label-nama-anggota');

        if (inputNama) {
            inputNama.addEventListener('input', function() {
                const nama = this.value.trim().toUpperCase();
                labelsNama.forEach(lbl => {
                    lbl.textContent = nama !== '' ? nama : 'Anggota Keluarga Ini';
                });
            });
        }
    });

    // ==============================================================
    // 🚀 INISIALISASI SELECT2 UNTUK PROFESI PEKERJAAN (SEARCHABLE)
    // ==============================================================
    $(document).ready(function() {
        // Kita pasang event saat Modal selesai terbuka agar lebar Select2 dirender dengan sempurna
        $('#modalAnggota').on('shown.bs.modal', function() {
            $('#lapangan_usaha').select2({
                dropdownParent: $('#modalAnggota'), // ⚠️ Wajib agar search box tidak terhalang (bug z-index modal)
                width: '100%',
                placeholder: '-- Ketik untuk mencari profesi --'
            });
        });

        // Hancurkan Select2 saat modal ditutup untuk mencegah duplikasi/error jika dibuka lagi
        $('#modalAnggota').on('hidden.bs.modal', function() {
            if ($('#lapangan_usaha').hasClass("select2-hidden-accessible")) {
                $('#lapangan_usaha').select2('destroy');
            }
        });

        // ==========================================
        // 🚀 KAWALAN KETAT INPUT ANGKA & VALIDASI HP
        // ==========================================

        // 1. Sapu bersih karakter selain angka secara real-time
        $('#nik, #individu_no_kk, #no_hp').on('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // 2. Validasi Khusus Nomor Handphone (Format BPS)
        $('#no_hp').on('input change', function() {
            let val = $(this).val();

            // Jika dikosongkan, hapus error (karena field ini opsional)
            if (val === '') {
                $(this).removeClass('is-invalid');
                return;
            }

            // Rule: Harus diawali '08' DAN panjang 10 s/d 13 digit
            if (!val.startsWith('08') || val.length < 10 || val.length > 13) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // ==============================================================
        // 🚀 SMART CHECKBOX: PENYAKIT KRONIS (MUTUALLY EXCLUSIVE)
        // ==============================================================
        $('.kronis-check').on('change', function() {
            const val = $(this).val();
            const isChecked = $(this).prop('checked');

            if (val === 'Tidak Ada' && isChecked) {
                // 1. Jika "Tidak Ada" Dicentang -> Hapus centang semua penyakit lainnya
                $('.kronis-check').not(this).prop('checked', false);
            } else if (val !== 'Tidak Ada' && isChecked) {
                // 2. Jika "Penyakit Lain" Dicentang -> Otomatis hapus centang pada "Tidak Ada"
                $('.kronis-check[value="Tidak Ada"]').prop('checked', false);
            }
        });

        // =======================================================
        // 📋 FUNGSI SALIN KE CLIPBOARD (VERSI DELEGASI JQUERY)
        // =======================================================
        $(document).on('click', '.btn-copy-input', function(e) {
            e.preventDefault(); // Mencegah aksi submit form jika tombol dipicu tak sengaja

            const targetSelector = $(this).attr('data-target');
            const inputEl = $(targetSelector);

            if (inputEl.length && inputEl.val().trim() !== '') {
                navigator.clipboard.writeText(inputEl.val().trim()).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: 'Teks disalin!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            } else {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'warning',
                    title: 'Kolom masih kosong!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });

        // ==============================================================
        // 🚀 SMART DATE MASKING & VALIDATION: TANGGAL LAHIR (DD-MM-YYYY)
        // ==============================================================

        // Fungsi terpisah agar bisa dipanggil saat 'input' dan 'blur'
        function validateDateString(val) {
            if (val.length !== 10) return false;
            const parts = val.split('-');
            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10);
            const year = parseInt(parts[2], 10);

            const checkDate = new Date(year, month - 1, day);
            const isCalendarValid = (
                checkDate.getFullYear() === year &&
                checkDate.getMonth() === month - 1 &&
                checkDate.getDate() === day
            );

            const currentYear = new Date().getFullYear();
            const isYearReasonable = year >= 1900 && year <= currentYear;

            return isCalendarValid && isYearReasonable;
        }

        $('#tanggal_lahir_display').on('input', function() {
            let val = this.value.replace(/\D/g, '');

            if (val.length > 2 && val.length <= 4) {
                val = val.slice(0, 2) + '-' + val.slice(2);
            } else if (val.length > 4) {
                val = val.slice(0, 2) + '-' + val.slice(2, 4) + '-' + val.slice(4, 8);
            }
            this.value = val;

            const $feedback = $(this).siblings('.invalid-feedback');

            if (val.length === 10) {
                if (validateDateString(val)) {
                    const parts = val.split('-');
                    $('#tanggal_lahir').val(`${parts[2]}-${parts[1]}-${parts[0]}`);
                    $(this).removeClass('is-invalid');
                } else {
                    $('#tanggal_lahir').val('');
                    $(this).addClass('is-invalid');
                    $feedback.text('Format tanggal tidak valid (Misal: 30 Februari).');
                }
            } else {
                // Jangan merah saat operator masih asyik mengetik, tapi amankan Ghost Input
                $('#tanggal_lahir').val('');
                $(this).removeClass('is-invalid');
            }
        });

        // 🚀 TRIGGER KETAT SAAT PINDAH KOLOM (BLUR)
        $('#tanggal_lahir_display').on('blur', function() {
            let val = $(this).val();
            const $feedback = $(this).siblings('.invalid-feedback');

            // Jika kolom tidak kosong, tapi panjangnya belum 10 karakter (misal: 31-07-92)
            if (val.length > 0 && val.length < 10) {
                $(this).addClass('is-invalid');
                $feedback.text('Gunakan 8 digit angka lengkap dengan tahun (Contoh: 31071992).');
                $('#tanggal_lahir').val(''); // Pastikan database tidak menerima data ini
            }
        });

        // ==============================================================
        // 🚀 ANTI-BUG BACKDROP: Hapus Fokus Modal (Aria-Hidden Fix)
        // ==============================================================

        // 1. Paksa tombol close melepaskan fokus saat diklik
        $(document).on('click', '[data-bs-dismiss="modal"]', function() {
            $(this).blur();
        });

        // 2. Sabuk Pengaman Ekstra: Sapu bersih semua sisa fokus setiap kali ada modal apapun yang mulai ditutup
        $(document).on('hide.bs.modal', function() {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        // 🚀 TELEPORTASI MODAL ANGGOTA KE LUAR TAB-PANE
        $('#modalAnggota').appendTo('body');
    });
</script>