<!-- Modal Tambah Usulan DTSEN -->
<div class="modal fade" id="modalDtsenUsulan" tabindex="-1" role="dialog" aria-labelledby="modalDtsenUsulanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalDtsenUsulanLabel">Tambah Usulan DTSEN</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="progress" style="height: 8px;">
                <div id="formProgress" class="progress-bar bg-success" style="width: 25%;"></div>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="formDtsen" enctype="multipart/form-data">

                    <!-- Step 1: Data Rumah Tangga -->
                    <div class="step step-1">
                        <h5 class="mb-3 text-success">Langkah 1 dari 4 — Data Rumah Tangga</h5>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="alamat">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rt">RT</label>
                                <input type="number" class="form-control" id="rt" name="rt" placeholder="RT">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rw">RW</label>
                                <input type="number" class="form-control" id="rw" name="rw" placeholder="RW">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="kepemilikan_rumah">Kepemilikan Rumah</label>
                                <select class="form-control" id="kepemilikan_rumah" name="kepemilikan_rumah">
                                    <option value="">-- Pilih --</option>
                                    <option value="Milik Sendiri">Milik Sendiri</option>
                                    <option value="Sewa">Sewa</option>
                                    <option value="Menumpang">Menumpang</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sumber_air">Sumber Air</label>
                                <select class="form-control" id="sumber_air" name="sumber_air">
                                    <option value="">-- Pilih --</option>
                                    <option value="Sumur">Sumur</option>
                                    <option value="PAM">PAM</option>
                                    <option value="Air Sungai">Air Sungai</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="sanitasi">Sanitasi</label>
                                <select class="form-control" id="sanitasi" name="sanitasi">
                                    <option value="">-- Pilih --</option>
                                    <option value="Jamban Sendiri">Jamban Sendiri</option>
                                    <option value="Jamban Bersama">Jamban Bersama</option>
                                    <option value="Tidak Ada">Tidak Ada</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sumber_listrik">Sumber Listrik</label>
                                <select class="form-control" id="sumber_listrik" name="sumber_listrik">
                                    <option value="">-- Pilih --</option>
                                    <option value="PLN">PLN</option>
                                    <option value="Non-PLN">Non-PLN</option>
                                    <option value="Tidak Ada">Tidak Ada</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="foto_rumah">Foto Rumah</label>
                            <input type="file" class="form-control-file" id="foto_rumah" name="foto_rumah" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="foto_rumah_dalam">Foto Rumah Dalam</label>
                            <input type="file" class="form-control-file" id="foto_rumah_dalam" name="foto_rumah_dalam" accept="image/*">
                        </div>
                    </div>

                    <!-- Step Container Lainnya (KK, ART, SE) -->
                    <!-- Step 2: Data Kartu Keluarga -->
                    <div class="step step-2 d-none">
                        <h5 class="mb-3 text-success">Langkah 2 dari 4 — Data Kartu Keluarga</h5>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="no_kk">Nomor Kartu Keluarga</label>
                                <input type="text" class="form-control" id="no_kk" name="no_kk" placeholder="Masukkan nomor KK">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kepala_keluarga">Nama Kepala Keluarga</label>
                                <input type="text" class="form-control" id="kepala_keluarga" name="kepala_keluarga" placeholder="Nama lengkap kepala keluarga">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="status_kepemilikan_rumah">Status Kepemilikan Rumah</label>
                                <select class="form-control" id="status_kepemilikan_rumah" name="status_kepemilikan_rumah">
                                    <option value="">-- Pilih --</option>
                                    <option value="Milik Sendiri">Milik Sendiri</option>
                                    <option value="Sewa">Sewa</option>
                                    <option value="Menumpang">Menumpang</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="jumlah_anggota">Jumlah Anggota</label>
                                <input type="number" class="form-control" id="jumlah_anggota" name="jumlah_anggota" min="1" max="20" placeholder="Jumlah anggota keluarga">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="program_bansos">Program Bansos yang Diterima</label>
                            <select class="form-control" id="program_bansos" name="program_bansos" multiple>
                                <option value="PKH">PKH</option>
                                <option value="BPNT">BPNT</option>
                                <option value="BLT">BLT</option>
                                <option value="KIS">KIS</option>
                                <option value="KIP">KIP</option>
                                <option value="PBI">PBI</option>
                            </select>
                            <small class="text-muted">Gunakan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu.</small>
                        </div>

                        <div class="form-group">
                            <label for="foto_kk">Foto Kartu Keluarga</label>
                            <input type="file" class="form-control-file" id="foto_kk" name="foto_kk" accept="image/*">
                        </div>
                    </div>

                    <!-- Step 3: Data Anggota Rumah Tangga -->
                    <div class="step step-3 d-none">
                        <h5 class="mb-3 text-success">Langkah 3 dari 4 — Anggota Rumah Tangga</h5>

                        <div class="alert alert-info p-2">
                            Tambahkan anggota keluarga yang termasuk dalam KK ini.
                        </div>

                        <div id="artForm">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="art_nik">NIK</label>
                                    <input type="text" class="form-control form-control-sm" id="art_nik" placeholder="NIK">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="art_nama">Nama</label>
                                    <input type="text" class="form-control form-control-sm" id="art_nama" placeholder="Nama Lengkap">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="art_jk">Jenis Kelamin</label>
                                    <select class="form-control form-control-sm" id="art_jk">
                                        <option value="">-- Pilih --</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="art_tgllahir">Tgl Lahir</label>
                                    <input type="date" class="form-control form-control-sm" id="art_tgllahir">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="art_hubkk">Hub. KK</label>
                                    <select class="form-control form-control-sm" id="art_hubkk">
                                        <option value="">-- Pilih --</option>
                                        <option value="Kepala Keluarga">Kepala Keluarga</option>
                                        <option value="Istri">Istri</option>
                                        <option value="Anak">Anak</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="art_pendidikan">Pendidikan</label>
                                    <select class="form-control form-control-sm" id="art_pendidikan">
                                        <option value="">-- Pilih --</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="Diploma">Diploma</option>
                                        <option value="Sarjana">Sarjana</option>
                                        <option value="Tidak Sekolah">Tidak Sekolah</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="art_pekerjaan">Pekerjaan</label>
                                    <select class="form-control form-control-sm" id="art_pekerjaan">
                                        <option value="">-- Pilih --</option>
                                        <option value="Petani">Petani</option>
                                        <option value="Pedagang">Pedagang</option>
                                        <option value="PNS">PNS</option>
                                        <option value="Pelajar">Pelajar</option>
                                        <option value="IRT">Ibu Rumah Tangga</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-right mb-2">
                                <button type="button" id="btnAddArt" class="btn btn-sm btn-success">
                                    <i class="fas fa-user-plus"></i> Tambah Anggota
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="tabelArt">
                                    <thead class="bg-success text-white">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>JK</th>
                                            <th>Tgl Lahir</th>
                                            <th>Hub. KK</th>
                                            <th>Pendidikan</th>
                                            <th>Pekerjaan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Data Sosial Ekonomi -->
                    <div class="step step-4 d-none">
                        <h5 class="mb-3 text-success">Langkah 4 dari 4 — Kondisi Sosial Ekonomi</h5>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="penghasilan">Total Penghasilan Rumah Tangga (per bulan)</label>
                                <input type="number" class="form-control" id="penghasilan" name="penghasilan" placeholder="Masukkan total penghasilan">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="pengeluaran">Total Pengeluaran Rumah Tangga (per bulan)</label>
                                <input type="number" class="form-control" id="pengeluaran" name="pengeluaran" placeholder="Masukkan total pengeluaran">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bahan_bakar">Bahan Bakar untuk Memasak</label>
                                <select class="form-control" id="bahan_bakar" name="bahan_bakar">
                                    <option value="">-- Pilih --</option>
                                    <option value="Gas LPG">Gas LPG</option>
                                    <option value="Minyak Tanah">Minyak Tanah</option>
                                    <option value="Kayu Bakar">Kayu Bakar</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="aset">Kepemilikan Aset Utama</label>
                                <select multiple class="form-control" id="aset" name="aset[]">
                                    <option value="Sepeda Motor">Sepeda Motor</option>
                                    <option value="Mobil">Mobil</option>
                                    <option value="Televisi">Televisi</option>
                                    <option value="Kulkas">Kulkas</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <small class="text-muted">Gunakan Ctrl / Cmd untuk memilih lebih dari satu</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="status_bansos">Status Penerima Bantuan Sosial</label>
                                <select class="form-control" id="status_bansos" name="status_bansos">
                                    <option value="">-- Pilih --</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="catatan">Catatan Tambahan</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Keterangan tambahan (jika ada)"></textarea>
                            </div>
                        </div>

                        <div class="alert alert-secondary">
                            <i class="fas fa-info-circle"></i> Periksa kembali seluruh data sebelum disimpan.
                        </div>
                    </div>

                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="btnPrev" disabled>Kembali</button>
                <button type="button" class="btn btn-success" id="btnNext">Berikutnya</button>
            </div>
        </div>
    </div>
</div>