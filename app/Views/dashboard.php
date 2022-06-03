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
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <p class="m-0 mt-2">Selamat Datang <b>Bpk/Ibu. <?php echo  session()->get('fullname'); ?></b>!</p>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
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
                                                            <h3><?php echo number_format($row->Capaian); ?></h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-person"></i>
                                            </div>
                                            <a href="/usulan" class="small-box-footer">Lihat lebih lanjut <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <a href="usulan" class="btn btn-sm btn-primary float-right">Rincian</a>
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
                                                        <h3><?php echo number_format($jmlRecord->jml); ?></h3>
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
                                                            <?= number_format($total); ?>
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="col mt-4">
                                                    <div class="inner">
                                                        <h6>Jumlah Data Usulan DTKS</h6>
                                                        <h3><?php echo number_format($jumlah_semua_usulan->jml); ?></h3>
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
                                                        <h3><?php echo number_format($rekapPbi->jml); ?></h3>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="inner mt-4">
                                                        <?php foreach ($jmlPerbaikan as $row) { ?>
                                                            <h6>Jumlah Tervalidasi PBI-JKN</h6>
                                                            <h3><?php echo number_format($row['dataCapaianAll']); ?></h3>
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
                                                        <h3><?php echo number_format($persentasePerbaikan, 2); ?>%</h3>
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
                                            <b><?php echo ucfirst(strtolower($row['name'])); ?> - <?= number_format($persentase, 2); ?>%</b>
                                            <span class="float-right"><b><?= number_format($row['dataCapaian'], 0); ?></b>/<?= number_format($row['dataTarget'], 0); ?></span>
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
                                                        <td><?= number_format($row['aktif']); ?></td>
                                                        <td><?= number_format($row['meninggalDunia']); ?></td>
                                                        <td><?= number_format($row['ganda']); ?></td>
                                                        <td><?= number_format($row['pindah']); ?></td>
                                                        <td><?= number_format($row['tidakDitemukan']); ?></td>
                                                        <td><?= number_format($row['menolak']); ?></td>
                                                        <td><?= number_format($row['dataCapaian']); ?></td>
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

        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');

    });
</script>
<?= $this->endSection(); ?>