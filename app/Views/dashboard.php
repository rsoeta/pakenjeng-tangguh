<?php

use App\Controllers\Profil\Profil_User;
?>
<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>


<!-- jQuery Library -->
<script src="<?= base_url(); ?>/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= base_url(); ?>/assets/dist/js/jquery/3.6.0/jquery-3.6.0.min.js"></script>
<script src="<?= base_url(); ?>/assets/dist/js/jquery/datatables/1.10.19/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.9/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script><!-- Content Wrapper. Contains page content -->

<style>
    #bg-orange {
        background-color: orange;
    }

    .gradient-custom {
        /* fallback for old browsers */
        background: #f6d365;

        /* Chrome 10-25, Safari 5.1-6 */
        background: -webkit-linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1));

        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        background: linear-gradient(to right, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1))
    }
</style>

<!-- div crumb -->

<div class="content-wrapper" style="padding-top: 10px;">
    <section class="content-header">
        <div class="container-fluid">
            <i class="fas fa-quote-left fa-lg text-warning me-2"></i>
            <span class="font-italic mb-2">
                <strong>Assalamualaikum... Selamat <?= Salam(); ?>, Bapak <?= ucwords(strtolower(session()->get('fullname'))); ?></strong>
            </span>
        </div>
    </section>
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content mt-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Rincian Verivali KKPM
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-5">
                            <div class="card-body" style="padding-right: 0px;">
                                <div class="col-12">
                                    <canvas id="chart_bnba" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-7">
                            <div class="card-body table-responsive" style="padding-left: 0px;">
                                <table class="table table-sm table-hover table-bordered compact" style="width: 100%;">
                                    <thead class="table-dark">
                                        <tr>
                                            <th rowspan="2">NO.</th>
                                            <th rowspan="2">NAMA DESA</th>
                                            <th colspan="7">KRITERIA</th>
                                            <th rowspan="2">JUMLAH</th>

                                        </tr>
                                        <tr>
                                            <?php
                                            foreach ($dtks_status as $dtks_statu) : ?>
                                                <th><?= $dtks_statu->jenis_status; ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        <?php foreach ($countStatusBnba as $count) : ?>
                                            <tr>
                                                <td style="text-align: center;"><?= $i++; ?></td>
                                                <td><?= $count->nama_desa; ?></td>
                                                <?php foreach ($dtks_status as $count2) : ?>
                                                    <td style="text-align: right;"><?= number_format($count->{$count2->jenis_status}, '0', ',', '.'); ?></td>
                                                <?php endforeach; ?>
                                                <td style="text-align: right;"><?= number_format($count->Capaian, '0', ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-primary">
                                        <tr>
                                            <th colspan="2" style="text-align: center;">TOTAL</th>
                                            <?php foreach ($dtks_status as $c => $d) : ?>
                                                <?php $grandTotal = 0;
                                                foreach ($countStatusBnba as $count) {
                                                    $grandTotal += $count->{$d->jenis_status};
                                                } ?>
                                                <th style="text-align: right;"><?= number_format($grandTotal, '0', ',', '.'); ?></th>
                                            <?php endforeach; ?>
                                            <?php foreach ($dtks_status as $c => $d) : ?>
                                                <?php $grandTotalAll = 0;
                                                foreach ($countStatusBnba as $count) {
                                                    $grandTotalAll += $count->Capaian;
                                                } ?>
                                            <?php endforeach; ?>
                                            <th style="text-align: right;"><?= number_format($grandTotalAll, '0', ',', '.'); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                <a href="/verivalibnba" class="btn btn-sm btn-primary float-right">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="row">
            <div class="col-12 col-sm-2 col-md-2">
                <div class="card">
                    <div class="card-header">
                        <img src="<?= base_url('icon-dtks.png'); ?>" class="brand-image rounded-circle mb-lg-0 shadow-2" alt="icon app" width="40" height="40">
                        <span><strong> Verivali PDTT</strong></span>
                        <div class="card-tools">
                            <a href="/geotagging" type="button" class="btn btn-sm btn-tool" title="Lebih lanjut">
                                <i class="fas fa-question"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Inner -->
                    <div class="card-body">
                        <!-- Single item -->
                        <div class="row d-flex justify-content-center">
                            <div class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-11">
                                <div class="d-flex">
                                    <div class="flex-grow-1 ms-1 ps-1">
                                        <!-- <figure> -->
                                        <!-- <blockquote class="blockquote mb-4"> -->
                                        <p>
                                            <a href="<?= base_url('/data/general/Adobe Scan 26 Jun 2022_1.jpg'); ?>" data-lightbox="image-1" data-title="Surat Undangan PDTT"><img id="myImg" src="<?= base_url('/data/general/Adobe Scan 26 Jun 2022_1.jpg'); ?>" style="width: 100%;"></a>
                                        </p>
                                        <!-- </blockquote> -->
                                        <figcaption class="blockquote-footer">
                                            <small>Presented by: <cite title="Opr NewDTKS"><small>Opr NewDTKS</small></cite></small>
                                        </figcaption>
                                        <!-- </figure> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-10 col-md-10">
                <div class="card">
                    <div class="card-header">
                        <img src="<?= base_url('icon-dtks.png'); ?>" class="brand-image rounded-circle mb-lg-0 shadow-2" alt="icon app" width="40" height="40">
                        <span><strong> Perbaikan Anomali DTKS</strong></span>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Inner -->
                    <div class="card-body">
                        <!-- Single item -->
                        <div class="row d-flex justify-content-center">
                            <div class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-11">
                                <div class="d-flex">
                                    <div class="flex-grow-1 ms-1 ps-1">
                                        <figure>
                                            <blockquote class="blockquote mb-4">
                                                <p>
                                                    <i class="fas fa-quote-left fa-lg text-warning me-2"></i>
                                                    <span class="font-italic mb-2">
                                                        <strong>Assalamualaikum Selamat <?= Salam(); ?> bapak/ibu</strong>
                                                    </span>
                                                    <br>
                                                    <span>
                                                        Ijin memberikan informasi perihal <a href="/verivaliAnomali"><strong>Perbaikan Data Anomali</strong></a> sudah dapat diakses, pastikan ketika pengerjaan data anomali data kependudukan sudah <strong>ONLINE dengan KEMENDAGRI</strong>.
                                                        <br>
                                                        Selain anomali pekerjaan juga ada anomali sebagai berikut :
                                                        <br>
                                                        <strong>- NIK ganda berbeda nama</strong> : silakan perbaiki sesuai dengan data lapangan, silakan periksa data kependudukan di CAPIL atau Kecamatan untuk memastikan yang bersangkutan NIKnya
                                                        <br>
                                                        <strong>- NIK nonaktif oleh DUKCAPIL</strong> : silakan hubungi DISDUKCAPIL atau KECAMATAN perihal tersebut barangkali yang bersangkutan belum perekaman E-KTP
                                                        <br>
                                                        <strong>- NIK Tidak ada di DATABASE Dukcapil</strong> : silakan hubungi DISDUKCAPIL atau Kecamatan untuk melakukan pengecekan NIK
                                                        <br>
                                                        <strong>- Pekerjaan PNS/TNI/POLRI (dll)</strong> : Silakan pastikan terlebih dahulu yang pekerjaan bersangkutan apakah PNS/TNI/POLRI (dll) apakah benar atau bukan, apabila bukan silakan perbaiki data kependudukannya di terlebih dahulu CAPIL
                                                        <br>
                                                        <strong>- RR Ulang NIK Padan Nama Simil dibawah 80 Persen</strong> : perbaiki nama berdasarkan DISDUKCAPIL/KEMENDAGRI yang dapat dilihat di KTP atau KK dari KPM yang bersangkutan sesuai dengan data lapangan (Bukan nama panggilan)
                                                    </span>
                                                </p>
                                            </blockquote>
                                            <figcaption class="blockquote-footer">
                                                <small>Presented by: <cite title="Opr NewDTKS"><small>Opr NewDTKS</small></cite></small>
                                            </figcaption>
                                        </figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="/verivaliAnomali" class="btn btn-sm btn-primary float-right">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Rekap Usulan DTKS Per-Desa dibulan ini</strong></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($rekapUsulan as $row) { ?>
                                <div class="col-sm-2 col-md-2 col-6">
                                    <div class="small-box bg-info">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col mt-4">
                                                    <div class="inner">
                                                        <h6><?= $row->namaDesa; ?></h6>
                                                        <h3><?php echo number_format($row->Capaian, '0', ',', '.'); ?></h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-person"></i>
                                        </div>
                                        <a href="/usulan" class="small-box-footer">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <!-- /.col -->
                        <div class="row">
                            <div class="col-12 col-md-3 mb-4">
                                <h4>PERSENTASE</h4>
                                <?php
                                $total = 0;
                                foreach ($rekapUsulan as $row) {
                                    $nama_desa = $row->namaDesa;
                                    $capaian = $row->Capaian;
                                    $total += $capaian;
                                    $persentase = ($capaian / $total) * 100;
                                ?>
                                    <div class="progress-group">
                                        <span class="progress-text"><b><?= $nama_desa; ?></b></span>
                                        <span class="progress-number float-right"><?php echo number_format($persentase, 2); ?>%</span>
                                        <div class="progress sm">
                                            <div class="progress-bar bg-info" style="width: <?php echo number_format($persentase, 2); ?>%"></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-9">
                                <h4>RINCIAN</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">NO.</th>
                                                <th rowspan="2">NAMA DESA</th>
                                                <th colspan="5">KRITERIA</th>
                                                <th rowspan="2">JUMLAH TOTAL</th>

                                            </tr>
                                            <tr>
                                                <?php
                                                foreach ($bansos as $row) {
                                                    echo '<th>' . $row['dbj_nama_bansos'] . '</th>';
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1 ?>
                                            <?php
                                            foreach ($rekapUsulan as $row) {
                                                echo '<tr>';
                                                echo '<td style="text-align:center">' . $no . '</td>';
                                                echo '<td>' . $row->namaDesa . '</td>';
                                                foreach ($bansos as $row2) {
                                                    echo '<td style="text-align:right">' . number_format($row->{$row2['dbj_nama_bansos']}, '0', ',', '.') . '</td>';
                                                }
                                                echo '<td style="text-align:right">' . number_format($row->Capaian, '0', ',', '.') . '</td>';
                                                echo '</tr>';
                                                $no++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="/usulan" class="btn btn-sm btn-primary float-right">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong>Rekap VeriVali Bansos BPNT</strong></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div> -->
                <!-- /.col -->
                <!-- <div class="card-body">
                            <div class="row">
                                <div class="col-sm-8 col-12">
                                    <div class="row"> -->
                <?php // foreach ($getDataGrup as $row) : 
                ?>
                <!-- <div class="col-sm-3 col-6"> -->
                <?php
                //                                                if ($row['jenis_keterangan'] == 'Belum Cek') {
                //                                                  $background = 'bg-warning';
                //                                                $icon = 'ion-load-a';
                // } else if ($row['jenis_keterangan'] == 'Invalid') {
                //     $background = 'bg-danger';
                //     $icon = 'ion-close-round';
                // } else if ($row['jenis_keterangan'] == 'NIK Padan Beda Nama') {
                //     $background = 'bg-info';
                //     $icon = 'ion-alert';
                // } else if ($row['jenis_keterangan'] == 'Valid') {
                //     $background = 'bg-success';
                //     $icon = 'ion-ion-checkmark-round';
                // } else if ($row['jenis_keterangan'] == 'Di Hapus - Meninggal') {
                //     $background = 'bg-warning';
                //     $icon = 'ion-minus-circled';
                // } else if ($row['jenis_keterangan'] == 'Di Hapus - NIK Sudah Terdaftar') {
                //     $background = 'bg-warning';
                //     $icon = 'ion-minus-circled';
                // } else if ($row['jenis_keterangan'] == 'Tidak Memiliki E-KTP') {
                //     $background = 'bg-secondary';
                //     $icon = 'ion-help';
                // }
                // 
                ?>
                <!-- <div class="small-box">  -->
                <?php // echo $background 
                ?>
                <!-- <div class="inner"> -->
                <!-- <h3><sup>%</sup></h3> -->
                <!-- <h3> -->
                <?php // echo  number_format($row['total_data']);
                ?>
                <!-- </h3> -->

                <!-- <p> -->
                <?php // echo  $row['jenis_keterangan']; 
                ?>
                <!-- </p> -->
                <!-- </div> -->
                <!-- <div class="icon"> -->
                <!-- <i class="ion">  -->
                <?php
                //echo  $icon;
                ?>
                <!-- </i> -->
                <!-- </div> -->
                <!-- <a href="#" class="small-box-footer">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a> -->
                <!-- </div> -->
                <!-- </div> -->
                <?php // endforeach; 
                ?>
                <!-- </div>
                                </div> -->
                <!-- <div class="col-sm-4 col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">NO.</th>
                                                    <th rowspan="2">NAMA DESA</th>
                                                    <th>JENIS USULAN</th>
                                                    <th rowspan="2">JUMLAH TOTAL</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php // $no = 1 
                                                ?>
                                                <?php // foreach ($rekapUsulan as $row) :
                                                ?>
                                                    <tr style="text-align: right;">
                                                        <td style="text-align: center;"><?php // $no; 
                                                                                        ?></td>
                                                        <td style="text-align: left;"><?php // $row['NamaDesa']; 
                                                                                        ?></td>
                                                        <td style="text-align: left;"><?php // $row['NamaBansos']; 
                                                                                        ?></td>
                                                        <td><?php // $row['Total']; 
                                                            ?></td>
                                                    </tr>
                                                    <?php // $no++ 
                                                    ?>
                                                <?php // endforeach; 
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> -->
                <!-- </div>
                        </div>
                        <div class="card-footer clearfix">
                            <a href="#" class="btn btn-sm btn-secondary float-right">Rincian</a>
                        </div> -->
                <!-- </div> -->

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Rekap Verivali dan Usulan</strong></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-12">
                                <div class="small-box bg-primary">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col mt-4">
                                                <div class="inner">
                                                    <h6>Jumlah Data Verivali DTKS</h6>
                                                    <h3><?php echo number_format($jmlRecord->jml, 0, ',', '.'); ?></h3>
                                                </div>
                                            </div>
                                            <div class="col mt-4">
                                                <div class="inner">
                                                    <h6>Jumlah Data Usulan DTKS Bulan ini</h6>
                                                    <h3>
                                                        <?php
                                                        $total = 0;
                                                        foreach ($rekapUsulan as $row) {
                                                            $jumlah = $row->Capaian;
                                                            $total += $jumlah;
                                                        } ?>
                                                        <?php
                                                        ?>
                                                        <?= number_format($total, 0, ',', '.'); ?>
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="col mt-4">
                                                <div class="inner">
                                                    <h6>Jumlah Data Usulan DTKS</h6>
                                                    <h3><?php echo number_format($jumlah_semua_usulan->jml, 0, ',', '.'); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-leaf"></i>
                                    </div>
                                    <a href="
                                        <?php
                                        foreach ($percentages as $row) {
                                            if (session()->get('kode_desa') == $row['desa_kode']) {
                                                $persentase = $row['percentage'];
                                            } else {
                                                $persentase = 100;
                                            }
                                        }
                                        // echo $persentase <= 95 ? '#' : 'usulan';
                                        ?>
                                        " class="small-box-footer">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                            <div class="col-sm-6 col-12">
                                <div class="small-box bg-success">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col">
                                                <div class="inner mt-4">
                                                    <h6>Jumlah Data Verivali PBI-JKN</h6>
                                                    <h3><?php echo number_format($rekapPbi->jml, 0, ',', '.'); ?></h3>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="inner mt-4">
                                                    <?php foreach ($jmlPerbaikan as $row) { ?>
                                                        <h6>Jumlah Tervalidasi PBI-JKN</h6>
                                                        <h3><?php echo number_format($row['dataCapaianAll'], 0, ',', '.'); ?></h3>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="inner mt-4">
                                                    <?php foreach ($jmlPerbaikan as $row) {
                                                    } ?>
                                                    <?php
                                                    if ($row['dataCapaianAll'] == 0) {
                                                        $persentasePerbaikan = 0;
                                                    } else {
                                                        $persentasePerbaikan = ($row['dataCapaianAll'] / $rekapPbi->jml * 100);
                                                    } ?>
                                                    <h6>Persentase</h6>
                                                    <h3><?php echo number_format($persentasePerbaikan, 0, ',', '.'); ?>%</h3>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-umbrella"></i>
                                    </div>
                                    <a href="verivalipbi" class="small-box-footer">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info boxes -->
            <div class="col-sm-9 col-md-9 col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Progres Verifikasi dan Validasi PBI-JKN</strong></h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3 mb-4">
                                <h4>PERSENTASE</h4>
                                <?php foreach ($jml_persentase as $row) { ?>
                                    <?php
                                    $persentase = $row['percentage'];
                                    ?>
                                    <div class="progress-group">
                                        <b><?php echo ucfirst(strtolower($row['name'])); ?> - <?= number_format($persentase, 2, ',', '.'); ?>%</b>
                                        <span class="float-right"><b><?= number_format($row['dataCapaian'], '0', ',', '.'); ?></b>/<?= number_format($row['dataTarget'], '0', ',', '.'); ?></span>
                                        <div class="progress progress-sm" style="height:20px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: <?= number_format($persentase, 2); ?>%"></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>


                            <div class="col-12 col-md-9">
                                <h4>RINCIAN</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">NO.</th>
                                                <th rowspan="2">NAMA DESA</th>
                                                <th colspan="6">KRITERIA VERIVALI</th>
                                                <th rowspan="2">JUMLAH TOTAL</th>

                                            </tr>
                                            <tr>
                                                <th>AKTIF</th>
                                                <th>MENINGGAL DUNIA</th>
                                                <th>GANDA / DOUBLE</th>
                                                <th>PINDAH</th>
                                                <th>TIDAK DITEMUKAN</th>
                                                <th>SUDAH MAMPU / MENOLAK</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1 ?>
                                            <?php foreach ($jml_persentase as $row) :
                                            ?>
                                                <tr style="text-align: right;">
                                                    <td style="text-align: center;"><?= $no; ?></td>
                                                    <td style="text-align: left;"><?= $row['name']; ?></td>
                                                    <td><?= number_format($row['aktif'], '0', ',', '.'); ?></td>
                                                    <td><?= number_format($row['meninggalDunia'], '0', ',', '.'); ?></td>
                                                    <td><?= number_format($row['ganda'], '0', ',', '.'); ?></td>
                                                    <td><?= number_format($row['pindah'], '0', ',', '.'); ?></td>
                                                    <td><?= number_format($row['tidakDitemukan'], '0', ',', '.'); ?></td>
                                                    <td><?= number_format($row['menolak'], '0', ',', '.'); ?></td>
                                                    <td><?= number_format($row['dataCapaian'], '0', ',', '.'); ?></td>
                                                </tr>
                                                <?php $no++ ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="verivalipbi" class="btn btn-sm btn-secondary float-right">Rincian</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12">
                <!-- DIRECT CHAT SUCCESS -->
                <div class="card card-success card-outline direct-chat direct-chat-success shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Direct Chat</h3>

                        <div class="card-tools">
                            <span title="3 New Messages" class="badge bg-success">3</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                                <i class="fas fa-comments"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">
                            <!-- Message. Default to the left -->
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="<?= base_url('/assets/dist/img/user1-128x128.jpg'); ?>" alt="Message User Image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    Is this template really for free? That's unbelievable!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message to the right -->
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="<?= base_url('/assets/dist/img/user3-128x128.jpg'); ?>" alt="Message User Image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    You better believe it!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="<?= base_url(); ?>/assets/dist/img/user1-128x128.jpg" alt="Message User Image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    Is this template really for free? That's unbelievable!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message to the right -->
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="<?= base_url(); ?>/assets/dist/img/user3-128x128.jpg" alt="Message User Image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    You better believe it!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->
                        </div>
                        <!--/.direct-chat-messages-->

                        <!-- Contacts are loaded here -->
                        <div class="direct-chat-contacts">
                            <ul class="contacts-list">
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="<?= base_url(); ?>/assets/dist/img/user1-128x128.jpg" alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Count Dracula
                                                <small class="contacts-list-date float-right">2/28/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">How have you been? I was...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                            </ul>
                            <!-- /.contatcts-list -->
                        </div>
                        <!-- /.direct-chat-pane -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <form action="#" method="post">
                            <div class="input-group">
                                <input type="text" name="message" placeholder="Type Message ..." class="form-control" spellcheck="false" data-ms-editor="true">
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-footer-->
                </div>
                <!--/.direct-chat -->
            </div>
        </div>

    </section>
</div>
<!-- The Modal -->
<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');

    });

    const ctx = document.getElementById('chart_bnba').getContext('2d');
    const chart_bnba = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                // get nama_desa from $countStatusBnba
                <?php foreach ($countStatusBnba as $key => $value) : ?> '<?= $value->nama_desa; ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Diagram BNBA - KPM Aktif',
                data: [
                    // get DataTarget from $countStatusBnba
                    <?php foreach ($countStatusBnba as $key => $value) : ?> '<?= $value->Aktif; ?>',
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(42, 245, 39, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(40, 41, 42, 0.2)',
                    'rgba(52, 99, 114, 0.2)',
                    'rgba(247, 62, 235, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(42, 245, 39, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(40, 41, 42, 0.8)',
                    'rgba(52, 99, 114, 0.8)',
                    'rgba(247, 62, 235, 0.8)',

                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?= $this->endSection(); ?>