<?php
$user_image = session()->get('user_image');
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
                                Home
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
                                General
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
                                            BNBA KESOS
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fa-fw fas fa-box-open"></i>
                                        <p>
                                            BPNT / Sembako
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview nav-third-level">
                                        <li class="nav-item">
                                            <a href="/bpnt_data" class="nav-link">
                                                <i class="fas fa-caret-right"></i>
                                                <p>
                                                    Data
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/bpnt_data" class="nav-link">
                                                <i class="fas fa-caret-right"></i>
                                                <p>
                                                    Transaksi
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa-fw fa fa-credit-card"></i>
                                    <p>
                                        PBI-JKN
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview nav-third-level">
                                    <li class="nav-item">
                                        <a href="/pbi" class="nav-link">
                                            <i class="fas fa-caret-right"></i>
                                            <p>
                                                Data
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa-fw fas fa-address-card"></i>
                                    <p>
                                        KIP
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview nav-third-level">
                                    <li class="nav-item">
                                        <a href="/datakip" class="nav-link">
                                            <i class="fas fa-caret-right"></i>
                                            <p>
                                                Data
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa-fw fas fa-blind"></i>
                                    <p>
                                        Janda / Lansia
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview nav-third-level">
                                    <li class="nav-item">
                                        <a href="/janda_data" class="nav-link">
                                            <i class="fas fa-caret-right"></i>
                                            <p>
                                                Data
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa-fw fas fa-child"></i>
                                    <p>
                                        Yatim / Piatu
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview nav-third-level">
                                    <li class="nav-item">
                                        <a href="/yatim_data" class="nav-link">
                                            <i class="fas fa-caret-right"></i>
                                            <p>
                                                Data
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-edit"></i>
                            <p>
                                Verivali
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview nav-second-level">
                            <li class="nav-item">
                                <a href="/verivalipbi" class="nav-link">
                                    <i class="nav-icon fa-fw fa fa-credit-card"></i>
                                    <p>
                                        PBI-JKN
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa-fw fa fa-code-branch"></i>
                                    <p>
                                        DTKS
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview nav-third-level">
                                    <li class="nav-item">
                                        <a href="/verivaliAnomali" class="nav-link">
                                            <i class="nav-icon fa-fw fa fa-user-alt-slash"></i>
                                            <p>
                                                Anomali
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-upload"></i>
                            <p>
                                Usulan
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview nav-second-level">
                            <li class="nav-item">
                                <a href="/usulan" class="nav-link">
                                    <i class="nav-icon fa-fw fas fa-code-branch"></i>
                                    <p>
                                        DTKS
                                    </p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview nav-second-level">
                            <li class="nav-item">
                                <a href="/import_csv" class="nav-link">
                                    <i class="nav-icon fa-fw fas fa-file"></i>
                                    <p>
                                        Import
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </div>
                <div class="user-panel">
                    <li class="nav-header">SETTINGS</li>
                    <li class="nav-item">
                        <a href="/profil_user" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-user"></i>
                            <p>
                                Profil
                            </p>
                        </a>
                    </li>
                    <?php if (session()->get('role_id') <= 2) { ?>
                        <li class="nav-item">
                            <a href="/users" class="nav-link">
                                <i class="nav-icon fa fa-users"></i>
                                <p>
                                    Users
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fa-fw fa fa-cog"></i>
                                <p>
                                    Lanjutan
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview nav-second-level">
                                <li class="nav-item">
                                    <a href="wilayah" class="nav-link">
                                        <i class="nav-icon fa-fw fa fa-globe"></i>
                                        <p>
                                            Data Wilayah
                                        </p>
                                    </a>
                                </li>
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
                    <li class="nav-item" id="keluar">
                        <a href="/logout" class="nav-link">
                            <i class="nav-icon fa-fw fa fa-sign-out-alt"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>
                </div>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>