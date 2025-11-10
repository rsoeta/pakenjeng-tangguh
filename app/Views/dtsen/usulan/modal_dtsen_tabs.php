<div class="modal fade" id="modalDtsenForm" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">🧾 Formulir Usulan DTSEN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Progress Bar -->
                <div class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar bg-success" id="progress-bar" role="progressbar" style="width:0%">Langkah 1/7</div>
                </div>

                <form id="formDtsen" enctype="multipart/form-data">
                    <input type="hidden" id="usulan_id" name="usulan_id">
                    <!-- STEP 0️⃣ : Pencarian Awal -->
                    <div class="step-container" id="step-0" style="display:block;">
                        <div class="alert alert-info mb-3">
                            <b>🔍 Pencarian Awal</b><br>
                            Masukkan <b>Nomor KK</b> atau <b>NIK Kepala Keluarga</b> untuk memulai usulan pembaruan atau penambahan data baru.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="no_kk_cari">Nomor KK</label>
                                <input type="text" id="no_kk_cari" class="form-control" placeholder="Masukkan No. KK (16 digit)">
                            </div>
                            <div class="col-md-6">
                                <label for="nik_cari">NIK Kepala Keluarga</label>
                                <input type="text" id="nik_cari" class="form-control" placeholder="Masukkan NIK Kepala Keluarga">
                            </div>
                            <div class="col-md-12 mt-3 text-center">
                                <button type="button" class="btn btn-primary" id="btnCariKK">
                                    🔎 Cari Data
                                </button>
                            </div>
                        </div>

                        <hr>

                        <div id="hasilPencarian" style="display:none;">
                            <h6>Hasil Pencarian:</h6>
                            <div id="hasilDataKK" class="border p-3 rounded bg-light mb-3"></div>

                            <div class="text-center">
                                <button class="btn btn-success" id="btnLanjutkan" style="display:none;">Lanjutkan Pembaruan</button>
                                <button class="btn btn-secondary" id="btnIsiManual" style="display:none;">Isi Manual (Keluarga Baru)</button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 1️⃣ : Data Pokok -->
                    <div class="step-container" id="step-1">
                        <div class="alert alert-info">Isikan Data Pokok Penduduk / Individu</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label>Nomor KK</label>
                                <input type="text" name="no_kk" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Hubungan Dalam Keluarga</label>
                                <select name="hubungan" class="form-select">
                                    <option value="Kepala Keluarga">Kepala Keluarga</option>
                                    <option value="Istri">Istri</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Famili Lain">Famili Lain</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Sarjana">Sarjana</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Status Kawin</label>
                                <select name="status_kawin" class="form-select">
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Cerai Hidup">Cerai Hidup</option>
                                    <option value="Cerai Mati">Cerai Mati</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Nama Ibu Kandung</label>
                                <input type="text" name="nama_ibu_kandung" class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label>Alamat Lengkap</label>
                                <input type="text" name="alamat" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2️⃣ : Tenaga Kerja -->
                    <div class="step-container" id="step-2" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <b>🧑‍🏭 Tenaga Kerja:</b> Isikan informasi pekerjaan, pendapatan, dan keterampilan.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label>Apakah bekerja/membantu bekerja selama seminggu yang lalu?</label>
                                <select name="bekerja_minggu_ini" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label>Lapangan usaha di pekerjaan utama</label>
                                <select name="lapangan_usaha" class="form-select">
                                    <option value="">-- Pilih Lapangan Usaha --</option>
                                    <option value="PERTANIAN TANAMAN PADI & PALAWIJA">PERTANIAN TANAMAN PADI & PALAWIJA</option>
                                    <option value="HORTIKULTURA">HORTIKULTURA</option>
                                    <option value="PERKEBUNAN">PERKEBUNAN</option>
                                    <option value="PERIKANAN">PERIKANAN</option>
                                    <option value="PETERNAKAN">PETERNAKAN</option>
                                    <option value="KEHUTANAN & PERTANIAN LAINNYA">KEHUTANAN & PERTANIAN LAINNYA</option>
                                    <option value="INDUSTRI PENGOLAHAN">INDUSTRI PENGOLAHAN</option>
                                    <option value="PERDAGANGAN BESAR DAN ECERAN">PERDAGANGAN BESAR DAN ECERAN</option>
                                    <option value="KONSTRUKSI">KONSTRUKSI</option>
                                    <option value="PENDIDIKAN">PENDIDIKAN</option>
                                    <option value="AKTIVITAS KESEHATAN MANUSIA DAN SOSIAL">AKTIVITAS KESEHATAN MANUSIA DAN SOSIAL</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label>Status dalam pekerjaan utama</label>
                                <select name="status_pekerjaan" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="BERUSAHA SENDIRI">BERUSAHA SENDIRI</option>
                                    <option value="BURUH/KARYAWAN SWASTA">BURUH/KARYAWAN SWASTA</option>
                                    <option value="PNS/TNI/POLRI">PNS/TNI/POLRI</option>
                                    <option value="PEKERJA BEBAS">PEKERJA BEBAS</option>
                                    <option value="PEKERJA KELUARGA/TIDAK DIBAYAR">PEKERJA KELUARGA/TIDAK DIBAYAR</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label>Pendapatan Sebulan Terakhir</label>
                                <select name="pendapatan" class="form-select" required>
                                    <option value="">-- Pilih Pendapatan --</option>
                                    <option value="TIDAK ADA PENGHASILAN">TIDAK ADA PENGHASILAN</option>
                                    <option value="<1 JUTA PER BULAN">
                                        < 1 JUTA PER BULAN</option>
                                    <option value=">= 1 JT - < UMK">>= 1 JT - < UMK</option>
                                    <option value="UMK">UMK</option>
                                    <option value="> UMK - 10 JT">> UMK - 10 JT</option>
                                    <option value="> 10 JT PER BULAN">> 10 JT PER BULAN</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label>Keterampilan Khusus / Sertifikat Keahlian yang Dimiliki</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="PEMROGRAMAN"><label class="form-check-label">PEMROGRAMAN</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="DESAIN GRAFIS"><label class="form-check-label">DESAIN GRAFIS</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="FOTOGRAFI"><label class="form-check-label">FOTOGRAFI</label></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="PERTANIAN"><label class="form-check-label">PERTANIAN</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="MENJAHIT"><label class="form-check-label">MENJAHIT</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="MENGEMUDI"><label class="form-check-label">MENGEMUDI</label></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="KEUANGAN"><label class="form-check-label">KEUANGAN</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="KOMUNIKASI"><label class="form-check-label">KOMUNIKASI</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="skill[]" value="PELAYANAN PUBLIK"><label class="form-check-label">PELAYANAN PUBLIK</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- STEP 3️⃣ : Usaha / Lapangan Ekonomi -->
                    <div class="step-container" id="step-3" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <b>🏪 Usaha / Lapangan Ekonomi:</b> Isikan data tentang kepemilikan usaha atau kegiatan ekonomi keluarga.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label>Apakah memiliki atau menjalankan usaha sendiri?</label>
                                <select name="memiliki_usaha" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Nama Usaha (jika ada)</label>
                                <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Warung Bu Ana">
                            </div>

                            <div class="col-md-6">
                                <label>Bidang Usaha</label>
                                <select name="bidang_usaha" class="form-select">
                                    <option value="">-- Pilih Bidang --</option>
                                    <option value="PERDAGANGAN">PERDAGANGAN</option>
                                    <option value="JASA">JASA</option>
                                    <option value="PERTANIAN">PERTANIAN</option>
                                    <option value="PETERNAKAN">PETERNAKAN</option>
                                    <option value="PERIKANAN">PERIKANAN</option>
                                    <option value="INDUSTRI RUMAHAN">INDUSTRI RUMAHAN</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Skala Usaha</label>
                                <select name="skala_usaha" class="form-select">
                                    <option value="">-- Pilih Skala --</option>
                                    <option value="RUMAH TANGGA">RUMAH TANGGA</option>
                                    <option value="MIKRO">MIKRO</option>
                                    <option value="KECIL">KECIL</option>
                                    <option value="MENENGAH">MENENGAH</option>
                                    <option value="BESAR">BESAR</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Lama Usaha Berjalan</label>
                                <select name="lama_usaha" class="form-select">
                                    <option value="">-- Pilih Lama Usaha --</option>
                                    <option value="< 1 TAHUN">
                                        < 1 TAHUN</option>
                                    <option value="1 - 3 TAHUN">1 - 3 TAHUN</option>
                                    <option value="> 3 TAHUN">> 3 TAHUN</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jumlah Tenaga Kerja</label>
                                <select name="jumlah_tenaga_kerja" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="1 ORANG (SENDIRI)">1 ORANG (SENDIRI)</option>
                                    <option value="2-5 ORANG">2-5 ORANG</option>
                                    <option value="> 5 ORANG">> 5 ORANG</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Rata-rata Omzet per Bulan</label>
                                <select name="omzet_bulanan" class="form-select">
                                    <option value="">-- Pilih Omzet --</option>
                                    <option value="< 1 JUTA">
                                        < 1 JUTA</option>
                                    <option value="1 - 3 JUTA">1 - 3 JUTA</option>
                                    <option value="3 - 10 JUTA">3 - 10 JUTA</option>
                                    <option value="> 10 JUTA">> 10 JUTA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Memiliki Nomor Induk Berusaha (NIB)?</label>
                                <select name="nib_status" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Nomor NIB (jika ada)</label>
                                <input type="text" name="nomor_nib" class="form-control" placeholder="Contoh: 1234567890">
                            </div>

                            <div class="col-md-12">
                                <label>Alamat Lokasi Usaha</label>
                                <input type="text" name="alamat_usaha" class="form-control" placeholder="Contoh: Jl. Raya Pakenjeng No. 45">
                            </div>

                            <div class="col-md-12">
                                <label>Unggah Foto Usaha (Opsional)</label>
                                <input type="file" name="foto_usaha" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <!-- STEP 4️⃣ : Kondisi Rumah Tangga (Aset & Fasilitas) -->
                    <div class="step-container" id="step-4" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <b>🏠 Kondisi Rumah Tangga:</b> Isikan kondisi tempat tinggal dan fasilitas utama rumah tangga.
                        </div>

                        <div class="row g-3">
                            <!-- Kondisi Bangunan -->
                            <div class="col-md-6">
                                <label>Status Kepemilikan Rumah</label>
                                <select name="kepemilikan_rumah" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="MILIK SENDIRI">MILIK SENDIRI</option>
                                    <option value="KONTRAK / SEWA">KONTRAK / SEWA</option>
                                    <option value="MENUMPANG">MENUMPANG</option>
                                    <option value="DINAS">DINAS</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Luas Bangunan Rumah (m²)</label>
                                <input type="number" name="luas_bangunan" class="form-control" placeholder="Contoh: 36" min="0">
                            </div>

                            <div class="col-md-6">
                                <label>Jenis Lantai Terluas</label>
                                <select name="jenis_lantai" class="form-select">
                                    <option value="">-- Pilih Jenis Lantai --</option>
                                    <option value="TANAH">TANAH</option>
                                    <option value="SEMEN / PLASTER">SEMEN / PLASTER</option>
                                    <option value="KERAMIK">KERAMIK</option>
                                    <option value="KAYU">KAYU</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jenis Dinding Terluas</label>
                                <select name="jenis_dinding" class="form-select">
                                    <option value="">-- Pilih Jenis Dinding --</option>
                                    <option value="BAMBU">BAMBU</option>
                                    <option value="KAYU">KAYU</option>
                                    <option value="TANAH / ADOBE">TANAH / ADOBE</option>
                                    <option value="BATU BATA / TEMBOK">BATU BATA / TEMBOK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jenis Atap Terluas</label>
                                <select name="jenis_atap" class="form-select">
                                    <option value="">-- Pilih Jenis Atap --</option>
                                    <option value="IJUK / DAUN">IJUK / DAUN</option>
                                    <option value="SENG / ASBES">SENG / ASBES</option>
                                    <option value="GENTENG">GENTENG</option>
                                    <option value="BETON / SEMEN">BETON / SEMEN</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jumlah Kamar Tidur</label>
                                <input type="number" name="jumlah_kamar_tidur" class="form-control" placeholder="Contoh: 2" min="0">
                            </div>

                            <div class="col-md-6">
                                <label>Sumber Penerangan Utama</label>
                                <select name="sumber_penerangan" class="form-select">
                                    <option value="">-- Pilih Sumber Penerangan --</option>
                                    <option value="LISTRIK PLN">LISTRIK PLN</option>
                                    <option value="LISTRIK NON-PLN">LISTRIK NON-PLN</option>
                                    <option value="GENERATOR / SOLAR CELL">GENERATOR / SOLAR CELL</option>
                                    <option value="LAMPU MINYAK">LAMPU MINYAK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Sumber Air Minum</label>
                                <select name="sumber_air_minum" class="form-select">
                                    <option value="">-- Pilih Sumber Air --</option>
                                    <option value="SUMUR GALI">SUMUR GALI</option>
                                    <option value="SUMUR POMPA / BOR">SUMUR POMPA / BOR</option>
                                    <option value="PDAM / AIR LEDENG">PDAM / AIR LEDENG</option>
                                    <option value="AIR HUJAN">AIR HUJAN</option>
                                    <option value="SUNGAI / MATA AIR">SUNGAI / MATA AIR</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Sumber Energi Memasak</label>
                                <select name="sumber_energi_memasak" class="form-select">
                                    <option value="">-- Pilih Sumber Energi --</option>
                                    <option value="KAYU BAKAR">KAYU BAKAR</option>
                                    <option value="MINYAK TANAH">MINYAK TANAH</option>
                                    <option value="GAS LPG">GAS LPG</option>
                                    <option value="LISTRIK">LISTRIK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Fasilitas Tempat Buang Air Besar</label>
                                <select name="fasilitas_bab" class="form-select">
                                    <option value="">-- Pilih Fasilitas --</option>
                                    <option value="TIDAK ADA">TIDAK ADA</option>
                                    <option value="SENDIRI">SENDIRI</option>
                                    <option value="BERSAMA">BERSAMA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Pembuangan Akhir Tinja</label>
                                <select name="pembuangan_tinja" class="form-select">
                                    <option value="">-- Pilih Cara Pembuangan --</option>
                                    <option value="TIDAK ADA">TIDAK ADA</option>
                                    <option value="SEPTIC TANK">SEPTIC TANK</option>
                                    <option value="SUNGAI / LAHAN">SUNGAI / LAHAN</option>
                                </select>
                            </div>

                            <!-- Aset Rumah Tangga -->
                            <div class="col-md-12">
                                <label>Aset Rumah Tangga (Boleh Pilih Lebih dari 1)</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="TELEVISI"><label class="form-check-label">TELEVISI</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="KULKAS"><label class="form-check-label">KULKAS</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="MESIN CUCI"><label class="form-check-label">MESIN CUCI</label></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="SEPEDA MOTOR"><label class="form-check-label">SEPEDA MOTOR</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="MOBIL"><label class="form-check-label">MOBIL</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="KOMPUTER / LAPTOP"><label class="form-check-label">KOMPUTER / LAPTOP</label></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="PERHIASAN / TABUNGAN"><label class="form-check-label">PERHIASAN / TABUNGAN</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="TANAH / LADANG"><label class="form-check-label">TANAH / LADANG</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="aset[]" value="TERNAK"><label class="form-check-label">TERNAK</label></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label>Unggah Foto Rumah (Opsional)</label>
                                <input type="file" name="foto_rumah" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <!-- STEP 5️⃣ : Sosial (Pendidikan & Kesehatan) -->
                    <div class="step-container" id="step-5" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <b>🎓🏥 Data Sosial: Pendidikan & Kesehatan</b>
                            Lengkapi informasi pendidikan, kondisi kesehatan, dan keikutsertaan jaminan sosial keluarga.
                        </div>

                        <div class="row g-3">
                            <!-- Pendidikan -->
                            <div class="col-md-6">
                                <label>Pendidikan Tertinggi Kepala Keluarga</label>
                                <select name="pendidikan_kk" class="form-select" required>
                                    <option value="">-- Pilih Pendidikan --</option>
                                    <option value="TIDAK SEKOLAH">TIDAK SEKOLAH</option>
                                    <option value="SD / SEDERAJAT">SD / SEDERAJAT</option>
                                    <option value="SMP / SEDERAJAT">SMP / SEDERAJAT</option>
                                    <option value="SMA / SEDERAJAT">SMA / SEDERAJAT</option>
                                    <option value="DIPLOMA">DIPLOMA</option>
                                    <option value="SARJANA">SARJANA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jumlah Anak Sekolah Aktif</label>
                                <input type="number" name="anak_sekolah_aktif" class="form-control" min="0" value="0">
                            </div>

                            <div class="col-md-6">
                                <label>Apakah ada anggota keluarga putus sekolah?</label>
                                <select name="putus_sekolah" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Fasilitas Pendidikan Terdekat</label>
                                <select name="fasilitas_pendidikan" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="PAUD / TK">PAUD / TK</option>
                                    <option value="SD / MI">SD / MI</option>
                                    <option value="SMP / MTS">SMP / MTS</option>
                                    <option value="SMA / SMK / MA">SMA / SMK / MA</option>
                                    <option value="KULIAH / PERGURUAN TINGGI">KULIAH / PERGURUAN TINGGI</option>
                                </select>
                            </div>

                            <!-- Kesehatan -->
                            <div class="col-md-6">
                                <label>Apakah terdapat anggota keluarga yang memiliki penyakit kronis / menahun?</label>
                                <select name="penyakit_kronis" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Jenis Penyakit (jika ada)</label>
                                <select name="jenis_penyakit" class="form-select">
                                    <option value="">-- Pilih Jenis Penyakit --</option>
                                    <option value="HIPERTENSI (DARAH TINGGI)">HIPERTENSI (DARAH TINGGI)</option>
                                    <option value="DIABETES (KENCING MANIS)">DIABETES (KENCING MANIS)</option>
                                    <option value="JANTUNG">JANTUNG</option>
                                    <option value="TBC">TBC</option>
                                    <option value="STROKE">STROKE</option>
                                    <option value="ASMA">ASMA</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Apakah terdapat anggota disabilitas?</label>
                                <select name="disabilitas" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="TIDAK ADA">TIDAK ADA</option>
                                    <option value="FISIK">DISABILITAS FISIK</option>
                                    <option value="MENTAL">DISABILITAS MENTAL</option>
                                    <option value="INTELEKTUAL">DISABILITAS INTELEKTUAL</option>
                                    <option value="GABUNGAN / MULTI">GABUNGAN / MULTI</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Apakah seluruh anggota keluarga terdaftar BPJS / JKN?</label>
                                <select name="kepesertaan_bpjs" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="SEMUA TERDAFTAR">SEMUA TERDAFTAR</option>
                                    <option value="SEBAGIAN TERDAFTAR">SEBAGIAN TERDAFTAR</option>
                                    <option value="TIDAK TERDAFTAR">TIDAK TERDAFTAR</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Apakah ada ibu hamil di rumah tangga ini?</label>
                                <select name="ibu_hamil" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Apakah ada balita (< 5 tahun)?</label>
                                        <select name="ada_balita" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            <option value="YA">YA</option>
                                            <option value="TIDAK">TIDAK</option>
                                        </select>
                            </div>

                            <div class="col-md-12">
                                <label>Kunjungan ke Fasilitas Kesehatan Terakhir</label>
                                <select name="kunjungan_faskes" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="DALAM 1 BULAN TERAKHIR">DALAM 1 BULAN TERAKHIR</option>
                                    <option value="DALAM 3 BULAN TERAKHIR">DALAM 3 BULAN TERAKHIR</option>
                                    <option value="> 3 BULAN TERAKHIR">> 3 BULAN TERAKHIR</option>
                                    <option value="BELUM PERNAH">BELUM PERNAH</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- STEP 6️⃣ : Bantuan Sosial / Program -->
                    <div class="step-container" id="step-6" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <b>💸 Bantuan Sosial / Program Pemerintah</b>
                            Lengkapi informasi keikutsertaan keluarga dalam program bantuan sosial atau subsidi pemerintah.
                        </div>

                        <div class="row g-3">
                            <!-- Jenis Program Bansos -->
                            <div class="col-md-12">
                                <label>Pilih Program Bantuan yang Pernah atau Sedang Diterima</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="PKH" id="bansos_pkh">
                                            <label class="form-check-label" for="bansos_pkh">Program Keluarga Harapan (PKH)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="BPNT" id="bansos_bpnt">
                                            <label class="form-check-label" for="bansos_bpnt">Bantuan Pangan Non Tunai (BPNT)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="BLT DANA DESA" id="bansos_bltdd">
                                            <label class="form-check-label" for="bansos_bltdd">BLT Dana Desa</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="PBI / KIS" id="bansos_pbi">
                                            <label class="form-check-label" for="bansos_pbi">PBI / KIS (Jaminan Kesehatan)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="KARTU PRAKERJA" id="bansos_prakerja">
                                            <label class="form-check-label" for="bansos_prakerja">Kartu Prakerja</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="RUTILAHU" id="bansos_rutilahu">
                                            <label class="form-check-label" for="bansos_rutilahu">RUTILAHU / BSPS</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="BANTUAN UMKM" id="bansos_umkm">
                                            <label class="form-check-label" for="bansos_umkm">Bantuan UMKM</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bansos[]" value="BANSOS LAINNYA" id="bansos_lainnya">
                                            <label class="form-check-label" for="bansos_lainnya">Bansos Lainnya</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Keikutsertaan -->
                            <div class="col-md-6">
                                <label>Status Keikutsertaan Program (Terkini)</label>
                                <select name="status_keikutsertaan" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="MASIH MENERIMA">MASIH MENERIMA</option>
                                    <option value="SUDAH BERHENTI">SUDAH BERHENTI</option>
                                    <option value="BELUM PERNAH MENERIMA">BELUM PERNAH MENERIMA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Alasan Tidak / Berhenti Menerima</label>
                                <select name="alasan_berhenti" class="form-select">
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="TIDAK LAYAK / SUDAH MAMPU">TIDAK LAYAK / SUDAH MAMPU</option>
                                    <option value="DATA TIDAK VALID">DATA TIDAK VALID</option>
                                    <option value="MENINGGAL / PINDAH DOMISILI">MENINGGAL / PINDAH DOMISILI</option>
                                    <option value="PROGRAM BERAKHIR">PROGRAM BERAKHIR</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Nomor Kartu / ID Peserta (jika ada)</label>
                                <input type="text" name="no_kartu_bansos" class="form-control" placeholder="Contoh: 532xxxxx">
                            </div>

                            <div class="col-md-6">
                                <label>Nama Program Lain (jika memilih Bansos Lainnya)</label>
                                <input type="text" name="nama_bansos_lain" class="form-control" placeholder="Contoh: Program Pangan Lokal">
                            </div>

                            <div class="col-md-12">
                                <label>Keterangan Tambahan</label>
                                <textarea name="keterangan_bansos" class="form-control" rows="3" placeholder="Contoh: Masih menerima PKH tahap 4, sedang diverifikasi untuk RUTILAHU."></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- STEP 7️⃣ : Verifikasi & Ringkasan -->
                    <div class="step-container" id="step-7" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <b>🧾 Verifikasi & Ringkasan Data</b><br>
                            Periksa kembali seluruh data sebelum disimpan ke sistem. Pastikan tidak ada kesalahan.
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2">📋 Ringkasan Isian Form</h6>
                                <div id="ringkasanData">
                                    <p class="text-muted">Data dari langkah 1–6 akan dimuat secara otomatis...</p>
                                </div>
                                <button type="button" id="btnRefreshRingkasan" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-sync"></i> Perbarui Ringkasan
                                </button>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2">✍️ Verifikasi Petugas Lapangan</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Nama Petugas</label>
                                        <input type="text" name="verifikator_nama" id="verifikator_nama" class="form-control"
                                            value="<?= session()->get('nama') ?? ''; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>NIK Petugas</label>
                                        <input type="text" name="verifikator_nik" id="verifikator_nik" class="form-control"
                                            value="<?= session()->get('nik') ?? ''; ?>" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label>Catatan Verifikasi</label>
                                        <textarea name="catatan_verifikasi" id="catatan_verifikasi" class="form-control"
                                            rows="3" placeholder="Contoh: Sudah diverifikasi, sesuai kondisi lapangan."></textarea>
                                    </div>
                                </div>

                                <hr>

                                <div class="text-center">
                                    <label class="form-label d-block mb-2">Tanda Tangan Petugas</label>
                                    <canvas id="signature-pad" width="280" height="160"
                                        style="border:1px solid #ccc; border-radius:8px;"></canvas><br>
                                    <button type="button" id="clear-signature" class="btn btn-sm btn-outline-secondary mt-2">Hapus</button>
                                </div>

                                <hr>

                                <div class="text-center mt-3">
                                    <button type="button" id="btnSubmitFinal" class="btn btn-success px-4 py-2">
                                        💾 Simpan Akhir & Kirim Usulan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" id="btnPrev">← Kembali</button>
                        <button type="button" class="btn btn-success" id="btnNext">Berikutnya →</button>
                        <button type="button" class="btn btn-primary" id="btnSaveFinal" style="display:none;">💾 Simpan & Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/dtsen_form.js'); ?>"></script>