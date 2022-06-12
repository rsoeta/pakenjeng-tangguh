<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url() ?>/dtks/pages/index" class="brand-link">
        <img src="<?= base_url('assets') ?>/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Opr NewDTKS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url() ?>/img/<?= session()->get('user_image'); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= session()->get('fullname'); ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-header">M E N U</li>
                <li class="nav-item">
                    <a href="/dtks/pages/index" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <?php if (session()->get('level') == 1) { ?>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Master Data
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-copy nav-icon"></i>
                                    <p>DTKS 2020</p>
                                    <i class="fas fa-angle-left right"></i>
                                </a>
                                <ul class="nav-treeview">
                                    <li class="nav-item">
                                        <a href="/dtks/pages/data" class="nav-link">
                                            <i class="fas fa-copy nav-icon"></i>
                                            <p>Data</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/dtks/pages/tables" class="nav-link">
                                            <i class="fas fa-copy nav-icon"></i>
                                            <p>Verivali</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/dtks/usulan/index" class="nav-link">
                                            <i class="fas fa-arrow-circle-up nav-icon"></i>
                                            <p>Usulan</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-copy nav-icon"></i>
                                    <p>DTKS 2021</p>
                                    <i class="fas fa-angle-left right"></i>
                                </a>
                                <ul class="nav-treeview">
                                    <li class="nav-item">
                                        <a href="/dtks/pages/tables" class="nav-link">
                                            <i class="fas fa-copy nav-icon"></i>
                                            <p>Data</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="far fa-copy nav-icon"></i>
                                            <p>Verivali</p>
                                            <i class="fas fa-angle-left right"></i>
                                        </a>
                                        <ul class="nav-treeview">
                                            <li class="nav-item">
                                                <a href="/dtks/vv06/table_dtks" class="nav-link">
                                                    <i class="fas fa-address-card nav-icon"></i>
                                                    <p>Full</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="/dtks/vv06" class="nav-link">
                                                    <i class="fas fa-address-card nav-icon"></i>
                                                    <p>By Address</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/dtks/usulan/index" class="nav-link">
                                            <i class="fas fa-arrow-circle-up nav-icon"></i>
                                            <p>Usulan</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-header">INTERFACE</li>
                    <li class="nav-item">
                        <a href="/dtks/wil/dsn" class="nav-link">
                            <i class="nav-icon fas fa-atlas"></i>
                            <p>
                                Wilayah
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/dtks/admin" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-stream nav-icon"></i>
                            <p>Properti</p>
                            <i class="fas fa-angle-left right"></i>
                        </a>
                        <ul class="nav-treeview">
                            <li class="nav-item">
                                <a href="/dtks/pages/status" class="nav-link">
                                    <i class="fas fa-toggle-off nav-icon"></i>
                                    <p>Status</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/dtks/pages/keterangan" class="nav-link">
                                    <i class="fas fa-toggle-on nav-icon"></i>
                                    <p>Keterangan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-header">LOG OUT</li>
                    <li class="nav-item">
                        <a href="<?= base_url(); ?>/dtks/auth/logout" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>

                <?php } elseif (session()->get('level') == 2) { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Master Data
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-copy nav-icon"></i>
                                    <p>Verivali</p>
                                    <i class="fas fa-angle-left right"></i>
                                </a>
                                <ul class="nav-treeview">
                                    <li class="nav-item">
                                        <a href="/dtks/vv06" class="nav-link">
                                            <i class="fas fa-address-card nav-icon"></i>
                                            <p>By Address</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/dtks/vv06/invalid" class="nav-link">
                                            <i class="fas fa-address-card nav-icon"></i>
                                            <p>Invalid</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/dtks/vv06/noaddress" class="nav-link">
                                            <i class="fas fa-exclamation-circle nav-icon"></i>
                                            <p>In-Address</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    <li class="nav-header">LOG OUT</li>
                    <li class="nav-item">
                        <a href="<?= base_url(); ?>/dtks/auth/logout" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>

                <?php } elseif (session()->get('level') >= '||' != 3) { ?>
                    <li class="nav-header">LOG OUT</li>
                    <li class="nav-item">
                        <a href="<?= base_url(); ?>/dtks/auth/logout" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>
                <?php } ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>