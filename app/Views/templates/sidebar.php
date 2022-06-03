<?php
$user = session()->get('role_id');
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">DTKS <strong>Kec. Pakenjeng</strong></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-1 pb-1 mb-1 d-flex">
            <div class="image">
                <img src="/assets/dist/img/profile/default.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= ucwords(strtolower(session()->get('fullname'))); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                        with font-awesome or any other icon font library -->
                <div class="user-panel">
                    <li class="nav-item menu-open">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon fa-fw fas fa-home"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                </div>
                <div class="user-panel">
                    <li class="nav-header">MASTER DATA</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-fw fas fa-code-branch"></i>
                            <p>
                                DTKS
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview nav-second-level">
                            <?php if ($user <= 4) { ?>
                                <li class="nav-item">
                                    <a href="
                                <?php
                                // foreach ($percentages as $row) {
                                //     if (session()->get('kode_desa') == $row['desa_kode']) {
                                //         $persentase = $row['percentage'];
                                //     } else {
                                //         $persentase = 100;
                                //     }
                                // }
                                echo 'bnba';
                                ?>
                                " class="nav-link">
                                        <i class="nav-icon fa-fw fa fa-clipboard-list"></i>
                                        <p>
                                            BNBA
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fa-fw fa fa-edit"></i>
                                        <p>
                                            VERIVALI
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview nav-third-level">
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                <i class="nav-icon fa-fw fas fa-credit-card"></i>
                                                <p>
                                                    BPNT/SEMBAKO
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="
                                <?php
                                // foreach ($percentages as $row) {
                                //     if (session()->get('kode_desa') == $row['desa_kode']) {
                                //         $persentase = $row['percentage'];
                                //     }
                                // }
                                echo 'usulan';
                                ?>
                                " class="nav-link">
                                        <i class="nav-icon fa-fw fas fa-upload"></i>
                                        <p>
                                            USULAN
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fa-fw fas fa-heartbeat"></i>
                                        <p>
                                            PBI-JKN
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview nav-third-level">
                                        <li class="nav-item">
                                            <a href="/verivalipbi" class="nav-link">
                                                <i class="nav-icon fa-fw fa fa-clipboard-list"></i>
                                                <p>
                                                    Verivali 2021
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="nav-item">
                                <a href="/datakip" class="nav-link">
                                    <i class="nav-icon fa-fw fas fa-copy"></i>
                                    <p>
                                        KIP
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>

                </div>
                <?php if (session()->get('role_id') == 1) { ?>
                    <li class="nav-header">SETTINGS</li>
                    <li class="nav-item">
                        <a href="wilayah" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-globe"></i>
                            <p>
                                Data Wilayah
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="chart_desa" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-chart-pie"></i>
                            <p>
                                Chart Desa
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-cog"></i>
                            <p>
                                General
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview nav-second-level">
                            <li class="nav-item">
                                <a href="/ketVervalPbi" class="nav-link">
                                    <i class="nav-icon fa-fw fa fa-check"></i>
                                    <p>
                                        Ket. Verivali PBI
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <li class="nav-header">USER</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-user"></i>
                        <p>
                            Profil
                        </p>
                    </a>
                </li>
                <?php if (session()->get('role_id') == 1) { ?>
                    <li class="nav-item">
                        <a href="/users" class="nav-link">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item" id="keluar">
                    <a href="/logout" class="nav-link">
                        <i class="nav-icon fa fa-sign-out-alt"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>