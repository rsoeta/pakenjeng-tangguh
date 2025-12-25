<!-- app/Views/dtsen/pembaruan/modal_anggota.php -->
<div class="modal fade" id="modalAnggota" tabindex="-1" aria-labelledby="modalAnggotaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="formAnggota" autocomplete="off">
                <input type="hidden" id="id_kk" name="id_kk" value="<?= $payload['id_kk'] ?? '' ?>">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalAnggotaLabel">🧍‍♂️ Pembaruan Data Individu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y:auto;">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="tabAnggotaTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-identitas-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-identitas" type="button" role="tab" aria-controls="tab-identitas"
                                aria-selected="true">
                                📋 Data Pokok Individu <span class="badge bg-secondary ms-1" id="badgeIdentitas">⚠️</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-pendidikan-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-pendidikan" type="button" role="tab">
                                🎓 Pendidikan <span class="badge bg-secondary ms-1" id="badgePendidikan">⚠️</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-kerja-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-kerja" type="button" role="tab">
                                💼 Tenaga Kerja <span class="badge bg-secondary ms-1" id="badgeKerja">⚠️</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-usaha-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-usaha" type="button" role="tab">
                                🏢 Kepemilikan Usaha <span class="badge bg-secondary ms-1" id="badgeUsaha">⚠️</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-kesehatan-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-kesehatan" type="button" role="tab">
                                ❤️ Kesehatan <span class="badge bg-secondary ms-1" id="badgeKesehatan">⚠️</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        <!-- TAB 1: IDENTITAS -->
                        <div class="tab-pane fade show active" id="tab-identitas" role="tabpanel">
                            <div class="row g-4">
                                <!-- ===================== ROW ATAS ===================== -->
                                <div class="row align-items-center border-bottom pb-1 mt-3 mb-1">
                                    <label class="col-md-5 col-12 col-form-label fw-bold">
                                        Status Keberadaan
                                    </label>

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
                                <!-- <hr> -->

                                <!-- ===================== KIRI ===================== -->
                                <div class="col-lg-6">

                                    <div class="row g-3">

                                        <!-- Nama Lengkap -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nama Lengkap</label>
                                            <input type="text" class="form-control required upper" name="nama" id="nama">
                                        </div>

                                        <!-- NIK -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">NIK</label>
                                            <input type="text" class="form-control required onlynum16" name="nik" id="nik" maxlength="16">
                                        </div>

                                        <!-- NKK -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nomor KK</label>
                                            <input type="text" class="form-control required onlynum16" name="individu_no_kk" id="individu_no_kk" maxlength="16">
                                        </div>

                                        <!-- TTL -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal Lahir</label>
                                            <input type="date" class="form-control required" name="tanggal_lahir" id="tanggal_lahir">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tempat Lahir</label>
                                            <input type="text" class="form-control required upper" name="tempat_lahir" id="tempat_lahir">
                                        </div>

                                        <!-- Hubungan -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Hubungan dengan Kepala Keluarga</label>
                                            <select class="form-select required" name="hubungan" id="hubungan"></select>
                                        </div>

                                        <!-- Status Kawin -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Status Kawin</label>
                                            <select class="form-select required" name="status_kawin" id="status_kawin"></select>
                                        </div>

                                        <!-- Ibu Kandung -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nama Ibu Kandung</label>
                                            <input type="text" class="form-control required upper" name="ibu_kandung" id="ibu_kandung">
                                        </div>

                                    </div>

                                </div>

                                <!-- ===================== KANAN ===================== -->
                                <div class="col-lg-6">

                                    <div class="row g-3">

                                        <!-- Pendidikan -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Pendidikan Terakhir</label>
                                            <select class="form-select required" name="pendidikan_terakhir" id="pendidikan_terakhir"></select>
                                        </div>

                                        <!-- Pekerjaan -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Pekerjaan</label>
                                            <select class="form-select required" name="pekerjaan" id="pekerjaan"></select>
                                        </div>

                                        <!-- Jenis Kelamin -->
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

                                        <!-- Wilayah Capil -->
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
                                        </div>

                                    </div>

                                </div>


                            </div>
                        </div>

                        <!-- TAB 2: Pendidikan -->
                        <div class="tab-pane fade" id="tab-pendidikan" role="tabpanel" aria-labelledby="tab-pendidikan-tab">
                            <div class="row g-3">

                                <!-- Partisipasi Sekolah -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Partisipasi Sekolah</label>
                                    <select class="form-select required" name="partisipasi_sekolah" id="partisipasi_sekolah">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Pernah Sekolah">Belum Pernah Sekolah</option>
                                        <option value="Masih Sekolah">Masih Sekolah</option>
                                        <option value="Tidak Bersekolah Lagi">Tidak Bersekolah Lagi</option>
                                    </select>
                                </div>

                                <!-- Jenjang & Jenis Pendidikan -->
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
                                </div>

                                <!-- Kelas Tertinggi -->
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
                                </div>

                                <!-- Ijazah/STTB -->
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
                                </div>
                            </div>
                        </div>
                        <!-- TAB 3: Tenaga Kerja -->
                        <div class="tab-pane fade" id="tab-kerja" role="tabpanel" aria-labelledby="tab-kerja-tab">
                            <div class="row g-3">

                                <!-- Bekerja seminggu terakhir -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Apakah bekerja/membantu bekerja selama seminggu terakhir?</label>
                                    <select class="form-select required" name="bekerja_seminggu" id="bekerja_seminggu">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Ditentukan">Belum Ditentukan</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                </div>

                                <!-- Lapangan Usaha -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenis Lapangan Usaha Pekerjaan Utama</label>
                                    <select class="form-select" name="lapangan_usaha" id="lapangan_usaha">
                                        <option value="">Pilih...</option>
                                        <option value="Pertanian tanaman padi & palawija">Pertanian tanaman padi & palawija</option>
                                        <option value="Hortikultura">Hortikultura</option>
                                        <option value="Perkebunan">Perkebunan</option>
                                        <option value="Perikanan">Perikanan</option>
                                        <option value="Peternakan">Peternakan</option>
                                        <option value="Kehutanan & pertanian lainnya">Kehutanan & pertanian lainnya</option>
                                        <option value="Pertambangan/penggalian">Pertambangan/penggalian</option>
                                        <option value="Industri pengolahan">Industri pengolahan</option>
                                        <option value="Pengolahan, listrik, gas, uap/air panas dan udara dingin">Pengolahan, listrik, gas, uap/air panas dan udara dingin</option>
                                        <option value="Pengolahan air, air limbah, pengelolaan dan daur ulang sampah">Pengolahan air, air limbah, pengelolaan dan daur ulang sampah</option>
                                        <option value="Konstruksi">Konstruksi</option>
                                        <option value="Perdagangan besar & eceran, reparasi/perawatan kendaraan">Perdagangan besar & eceran, reparasi/perawatan kendaraan</option>
                                        <option value="Pengangkutan dan pergudangan">Pengangkutan dan pergudangan</option>
                                        <option value="Penyediaan akomodasi & makan minum">Penyediaan akomodasi & makan minum</option>
                                        <option value="Informasi & komunikasi">Informasi & komunikasi</option>
                                        <option value="Keuangan & asuransi">Keuangan & asuransi</option>
                                        <option value="Real estate">Real estate</option>
                                        <option value="Aktivitas profesional, ilmiah & teknis">Aktivitas profesional, ilmiah & teknis</option>
                                        <option value="Aktivitas penyewaan, agen perjalanan, dan penunjang usaha lainnya">Aktivitas penyewaan, agen perjalanan, dan penunjang usaha lainnya</option>
                                        <option value="Administrasi pemerintahan, pertahanan, dan jaminan sosial wajib">Administrasi pemerintahan, pertahanan, dan jaminan sosial wajib</option>
                                        <option value="Pendidikan">Pendidikan</option>
                                        <option value="Aktivitas kesehatan manusia & sosial">Aktivitas kesehatan manusia & sosial</option>
                                        <option value="Kesenian, hiburan & rekreasi">Kesenian, hiburan & rekreasi</option>
                                        <option value="Aktivitas jasa lainnya">Aktivitas jasa lainnya</option>
                                        <option value="Aktivitas keluarga sebagai pemberi kerja">Aktivitas keluarga sebagai pemberi kerja</option>
                                        <option value="Aktivitas badan internasional & ekstra internasional lainnya">Aktivitas badan internasional & ekstra internasional lainnya</option>
                                    </select>
                                </div>

                                <!-- Status Pekerjaan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status dalam Pekerjaan Utama</label>
                                    <select class="form-select" name="status_pekerjaan" id="status_pekerjaan">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Ditentukan">Belum Ditentukan</option>
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

                                <!-- Pendapatan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pendapatan Sebulan Terakhir</label>
                                    <select class="form-select" name="pendapatan" id="pendapatan">
                                        <option value="" selected disabled>-- Pilih Pendapatan --</option>
                                        <option value="Tidak Ada Penghasilan">Tidak Ada Penghasilan</option>
                                        <option value="<1 Juta Per Bulan">&lt;1 Juta Per Bulan</option>
                                        <option value=">=1 Jt Per Bulan -<UMK">&gt;=1 Jt Per Bulan - &lt; UMK</option>
                                        <option value="UMK">UMK</option>
                                        <option value=">UMK - 10 Jt Per Bulan">&gt; UMK - 10 Jt Per Bulan</option>
                                        <option value=">10 Jt Per Bulan">&gt; 10 Jt Per Bulan</option>
                                    </select>
                                </div>

                                <!-- Keterampilan -->
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Keterampilan Khusus / Sertifikat Keahlian yang Dimiliki</label>
                                    <div class="border rounded p-2" style="max-height: 250px; overflow-y:auto;">
                                        <?php
                                        $skills = [
                                            "Pemrograman dan pengembangan perangkat lunak",
                                            "Keamanan cyber",
                                            "Jaringan dan administrasi sistem",
                                            "Data science dan analisis data",
                                            "Manajemen proyek",
                                            "Pemasaran",
                                            "Keuangan dan akuntansi",
                                            "Sumber daya manusia",
                                            "Desain grafis",
                                            "Desain interior",
                                            "Fotografi dan videografi",
                                            "Seni rupa dan ilustrasi",
                                            "Penerjemahan dan interpretasi",
                                            "Penulisan kreatif dan jurnalistik",
                                            "Public speaking dan presentasi",
                                            "Komunikasi pemasaran",
                                            "Keperawatan dan medis",
                                            "Kesehatan mental dan konseling",
                                            "Kebugaran dan pelatihan personal",
                                            "Nutrisi dan dietetik",
                                            "Teknik elektro dan elektronik",
                                            "Teknik sipil dan arsitektur",
                                            "Teknik mesin (bengkel dll)",
                                            "Energi dan lingkungan",
                                            "Pengajaran dan pembelajaran",
                                            "Pengembangan kurikulum",
                                            "Pendidikan anak usia dini",
                                            "Pelatihan dan pengembangan profesional",
                                            "Kuliner",
                                            "Pertukangan",
                                            "Menjahit",
                                            "Mengajar",
                                            "Mengasuh anak",
                                            "Mengemudi roda 2",
                                            "Mengemudi roda 4",
                                            "Mengemudi kendaraan besar/berat",
                                            "Bertani"
                                        ];
                                        foreach ($skills as $s) {
                                            echo "
                                        <div class='form-check form-check-inline'>
                                            <input class='form-check-input skill-check' type='checkbox' name='keterampilan[]' value='$s' id='skill_" . md5($s) . "'>
                                            <label class='form-check-label small' for='skill_" . md5($s) . "'>$s</label>
                                        </div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- TAB 4: Kepemilikan Usaha -->
                        <div class="tab-pane fade" id="tab-usaha" role="tabpanel" aria-labelledby="tab-usaha-tab">
                            <div class="row g-3">

                                <!-- Pertanyaan utama -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Apakah memiliki usaha sendiri atau bersama keluarga?</label>
                                    <select class="form-select required" name="memiliki_usaha" id="memiliki_usaha">
                                        <option value="">Pilih...</option>
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>

                                <!-- Form tambahan (hanya muncul bila 'Ya') -->
                                <div id="form_usaha_detail" style="display: none;">
                                    <div class="col-md-6 mt-3">
                                        <label class="form-label fw-bold">Jumlah usaha sendiri/bersama yang dimiliki</label>
                                        <input type="number" class="form-control required-if-ya" name="jumlah_usaha" id="jumlah_usaha" placeholder="Contoh: 1">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jumlah pekerja yang dibayar pada usaha utama</label>
                                        <input type="number" class="form-control required-if-ya" name="pekerja_dibayar" id="pekerja_dibayar" placeholder="Contoh: 2">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jumlah pekerja yang tidak dibayar pada usaha utama</label>
                                        <input type="number" class="form-control required-if-ya" name="pekerja_tidak_dibayar" id="pekerja_tidak_dibayar" placeholder="Contoh: 1">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Omzet per Bulan</label>
                                        <select class="form-select required-if-ya" name="omzet_bulanan" id="omzet_bulanan">
                                            <option value="">Pilih...</option>
                                            <option value="Belum Ditentukan">Belum Ditentukan</option>
                                            <option value="< 5 Juta (Ultra Mikro)">&lt; 5 Juta (Ultra Mikro)</option>
                                            <option value="5 < 15 Juta (Ultra Mikro)">5 - &lt;15 Juta (Ultra Mikro)</option>
                                            <option value="15 < 25 Juta (Ultra Mikro)">15 - &lt;25 Juta (Ultra Mikro)</option>
                                            <option value="25 < 167 Juta (Mikro)">25 - &lt;167 Juta (Mikro)</option>
                                            <option value="167 < 1.250 Juta (Kecil)">167 - &lt;1.250 Juta (Kecil)</option>
                                            <option value="1.250 < 4.167 Juta (Menengah)">1.250 - &lt;4.167 Juta (Menengah)</option>
                                            <option value=">=4.167 Juta (Besar)">≥ 4.167 Juta (Besar)</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- TAB 5: Kesehatan -->
                        <div class="tab-pane fade" id="tab-kesehatan" role="tabpanel" aria-labelledby="tab-kesehatan-tab">
                            <div class="row g-3">

                                <!-- Status Hamil -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Hamil <span class="text-muted small">(hanya untuk perempuan)</span></label>
                                    <select class="form-select" name="status_hamil" id="status_hamil">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Ditentukan">Belum Ditentukan</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                </div>

                                <!-- Disabilitas -->
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Penyandang Disabilitas</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y:auto;">
                                        <?php
                                        $disabilities = [
                                            'Fisik',
                                            'Mental',
                                            'Intelektual',
                                            'Sensorik Netra',
                                            'Sensorik Rungu',
                                            'Sensorik Wicara',
                                            'Sensorik Ganda/Multi'
                                        ];
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

                                <!-- Keluhan Kesehatan Kronis / Menahun -->
                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-bold">Keluhan Kesehatan Kronis / Menahun</label>
                                    <select class="form-select required" name="penyakit_kronis" id="penyakit_kronis">
                                        <option value="">Pilih...</option>
                                        <option value="Belum Ditentukan">Belum Ditentukan</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Hipertensi (darah tinggi)">Hipertensi (Darah Tinggi)</option>
                                        <option value="Rematik">Rematik</option>
                                        <option value="Asma">Asma</option>
                                        <option value="Masalah jantung">Masalah Jantung</option>
                                        <option value="Diabetes (kencing manis)">Diabetes (Kencing Manis)</option>
                                        <option value="Tuberculosis (TBC)">Tuberculosis (TBC)</option>
                                        <option value="Stroke">Stroke</option>
                                        <option value="Kanker atau tumor ganas">Kanker atau Tumor Ganas</option>
                                        <option value="Gagal ginjal">Gagal Ginjal</option>
                                        <option value="Haemophilia">Haemophilia</option>
                                        <option value="HIV/AIDS">HIV/AIDS</option>
                                        <option value="Kolesterol">Kolesterol</option>
                                        <option value="Sirosis hati">Sirosis Hati</option>
                                        <option value="Thalasimia">Thalasimia</option>
                                        <option value="Leukimia">Leukimia</option>
                                        <option value="Alzheimer">Alzheimer</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer sticky-bottom bg-light">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan</button>
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