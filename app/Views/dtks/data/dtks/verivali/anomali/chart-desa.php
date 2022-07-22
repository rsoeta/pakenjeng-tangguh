<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="card-header" style="text-align: center;">
            <h3 class="card-title"><?= $title; ?></h3>
        </div>
        <div class="table-responsive">
            <br />
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>KODE DESA</th>
                        <th>NAMA DESA</th>
                        <th>BELUM CEK</th>
                        <th>TIDAK VALID</th>
                        <th>MENINGGAL / DI HAPUS</th>
                        <th>SUDAH VALID</th>
                        <th>TIDAK MEMILIKI E-KTP</th>
                        <th>PEMBATALAN SUDAH DIPERIKSA</th>
                        <!-- <th>AKSI</th> -->
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</div>
<!-- /.container-fluid -->
<script>
    $(document).ready(function() {

        function thousands_separators(num) {
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return num_parts.join(".");
        }
        // load semua data
        function load_data() {
            $.ajax({
                url: "<?= base_url(); ?>/load_data",
                dataType: "JSON",
                success: function(data) {
                    // buat kolom inputan
                    var html = '<tr>';
                    html += '<td id="KodeDesa" contenteditable placeholder="Masukkan Kode"></td>';
                    html += '<td id="nama_desa" contenteditable placeholder="Masukkan Nama"></td>';
                    html += '<td id="BelumCek" contenteditable placeholder="Jml Belum Cek"></td>';
                    html += '<td id="TidakValid" contenteditable placeholder="Jml Tidak Valid"></td>';
                    html += '<td id="MeninggalDihapus" contenteditable placeholder="Jml Meninggal / Di hapus"></td>';
                    html += '<td id="SudahValid" contenteditable placeholder="Jml Sudah Valid"></td>';
                    html += '<td id="TidakMemilikiEktp" contenteditable placeholder="Jml Tidak Memiliki e-KTP"></td>';
                    html += '<td id="Pembatalan Diperiksa" contenteditable placeholder="Jml Pembatalan Diperiksa"></td>';
                    // html += '<td><button type="button" name="btn_add" id="btn_add" class="btn btn-sm btn-primary"><span class="fa fa-plus"></span> Tambah</td></tr>';

                    //data dalam bentuk json di looping disini
                    for (var count = 0; count < data.length; count++) {
                        html += '<tr>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="KodeDesa" contenteditable>' + data[count].KodeDesa + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="nama_desa" contenteditable>' + data[count].nama_desa + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="BelumCek" contenteditable>' + thousands_separators(data[count].BelumCek) + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="TidakValid" contenteditable>' + thousands_separators(data[count].TidakValid) + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="MeninggalDihapus" contenteditable>' + thousands_separators(data[count].MeninggalDihapus) + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="SudahValid" contenteditable>' + thousands_separators(data[count].SudahValid) + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="TidakMemilikiEktp" contenteditable>' + thousands_separators(data[count].TidakMemilikiEktp) + '</td>';
                        html += '<td class="table_data" data-row_id="' + data[count].id_desa + '" data-column_name="PembatalanDiperiksa" contenteditable>' + thousands_separators(data[count].PembatalanDiperiksa) + '</td>';

                        // html += '<td><button type="button" name="delete_btn" id="' + data[count].id_desa + '" class="btn btn-sm btn-danger btn_delete"><span class="fa fa-trash"></span></button></td></tr>';
                    }

                    // hasil looping masukan kesini
                    $('tbody').html(html);
                }
            });
        }
        load_data(); // panggil fungsi load data

        // simpan data
        $(document).on('click', '#btn_add', function() {
            var kode = $('#kode').text(); // ambil text dr id kode
            var nama = $('#nama').text(); // ambil text dr id nama
            var tahun_lulus = $('#tahun_lulus').text(); // ambil text dr id tahun_lulus

            // cek jika inputan kosong
            if (kode == '') {
                alert('masukkan kode');
                return false;
            }
            if (nama == '') {
                alert('masukkan nama');
                return false;
            }

            // jika inputan ada isinya kirim request dengan ajax
            $.ajax({
                url: '<?= base_url(); ?>/insert_data',
                method: 'POST',
                // data yg dikirim (name : value)
                data: {
                    kode: kode,
                    nama: nama,
                    tahun_lulus
                },
                // callback jika data berhasil disimpan
                success: function(data) {
                    //panggil fungsi
                    alert('data berhasil ditambah');
                    load_data();
                }
            });

        });

        // update data
        $(document).on('blur', '.table_data', function() {
            var id_desa = $(this).data('row_id'); // ambil nilai attribut data row_id dari class table_data
            var table_column = $(this).data('column_name'); // ambil nilai attribut data column_name dari class table_data
            var value = $(this).text(); // ambil value text dari class table_data

            $.ajax({
                url: '<?= base_url(); ?>/update_data',
                method: 'POST',
                // data yg dikirim ke server untuk update data (name:value)
                data: {
                    id_desa: id_desa,
                    table_column: table_column,
                    value: value
                },
                success: function(data) {
                    load_data(); // panggil fungsi load data jika berhasil update
                }
            });
        });

        // delete data
        $(document).on('click', '.btn_delete', function() {
            var id = $(this).attr('id'); // ambil nilai dr attribut id

            if (confirm("apakah kamu yakin hapus data ini?")) {
                $.ajax({
                    url: "<?= base_url(); ?>livetable/delete_data",
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        load_data();
                    }
                });
            }
        });
    });
</script>


<?= $this->endSection(); ?>