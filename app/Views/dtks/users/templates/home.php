<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->

        <div class="callout callout-info">
            <h5><i class="fas fa-info"></i> Note : </h5>
            <p>Selamat datang <strong>Bpk. <?= session()->get('fullname'); ?></strong>, Selamat Bekerja!</p>
        </div>

        <div class="callout callout-success">
            <div class="card-header">
                <h6 class="card-title"><?= $title; ?></h6>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <!-- /.col -->
                    <div class="col-md-12 col-12">
                        <p class="text-center">
                            <strong>Progres</strong>
                        </p>
                        
                        <?php foreach ($DataRekRw as $row) { ?>
                            <?php
                            $persentasi = round($row['Hsl'] / $row['Vv'] * 100, 2);
                            ?>
                            <div class="progress-group">
                                <a href="/dtks/vv06" style="text-decoration: none;"><strong>Data RW : <?= str_pad($row['rw'], 3, 0, STR_PAD_LEFT); ?></strong></a>
                                <span class="float-right"><a href="/dtks/vv06" style="text-decoration: none;"><b><?= $row['Hsl']; ?></b></a>/<?= $row['Vv']; ?></span>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: <?= $persentasi; ?>%"><a href="/dtks/vv06" style="text-decoration: none;"><?php echo $persentasi . "%"; ?></a></div>
                                </div>
                            </div>
                            <?php } ?>
                            
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                
                <div class="row">
                <p class="text-center">
                    <strong>Data Invalid</strong>
                </p>
                <?php foreach ($DataInvalid as $d) { ?>
                    <div class="col-lg-3 col-4">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4><?= $d['Inv']; ?></h4>

                                <p>Data Invalid <strong>RW : <?= str_pad($d['rw'], 3, 0, STR_PAD_LEFT); ?></strong></p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="/dtks/vv06/invalid" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                <?php } ?>
                <!-- ./col -->
            </div>
            <!-- ./card-body -->
            <div class="card-footer">
                <p class="text-center">
                    <strong>Perbaikan</strong>
                </p>
                <div class="row">
                    <?php foreach ($DataRekRw as $row) { ?>
                        <?php
                        $persentasi = round($row['Hsl'] / $row['Vv'] * 100, 2);
                        ?>
                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-primary"> <?= $persentasi; ?>%</span>
                                <h5 class="description-header"><?= $row['Hsl']; ?></h5>
                                <span class="description-text">RW : <?= str_pad($row['rw'], 3, 0, STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-footer -->
        </div>
    </section>

    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?= $this->endSection(); ?>