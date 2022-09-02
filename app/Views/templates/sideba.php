<?php

$user_image = session()->get('user_image');
$user = session()->get('role_id');

// connect to request uri
$request = \Config\Services::request();
$uri = $request->uri->getSegment(1);
$menus = menu()
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4" style="height: 100%;">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= nameApp() . ' ' . ucwords(strtolower(Profil_Admin()['namaKec'])); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- SidebarSearch Form -->
        <div class="form-inline mt-2" style="margin: auto; padding: 2%;">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search" spellcheck="false" data-ms-editor="true">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-search-results">
                <div class="list-group">
                    <a href="#" class="list-group-item">
                        <div class="search-title">
                            <b class="text-light"></b>N<b class="text-light"></b>o<b class="text-light"></b> <b class="text-light"></b>e<b class="text-light"></b>l<b class="text-light"></b>e<b class="text-light"></b>m<b class="text-light"></b>e<b class="text-light"></b>n<b class="text-light"></b>t<b class="text-light"></b> <b class="text-light"></b>f<b class="text-light"></b>o<b class="text-light"></b>u<b class="text-light"></b>n<b class="text-light"></b>d<b class="text-light"></b>!<b class="text-light"></b>
                        </div>
                        <div class="search-path"></div>
                    </a>
                </div>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php foreach ($menus as $menu) {
                    // make 5 level menu with tm_status as status = 1 and tm_grup_akses as grup_akses >= $user
                    if ($menu['tm_status'] == 1 && $menu['tm_grup_akses'] >= $user) {
                        if ($menu['tm_parent_id'] == 0) {
                            // if menu has child

                            if (menu_child($menu['tm_id']) != null) {
                                echo '<li class="nav-item has-treeview">';
                                echo '<a href="' . base_url($menu['tm_url']) . '" class="nav-link">';
                                echo '<i class="nav-icon ' . $menu['tm_icon'] . ' mr-1"></i>';
                                echo '<p>' . $menu['tm_nama'] . '<i class="right fas fa-angle-left"></i></p>';
                                echo '</a>';
                                echo '<ul class="nav nav-treeview nav-second-level">';
                                foreach (menu_child($menu['tm_id']) as $menu_child) {
                                    // make 4 level menu with tm_status as status = 1 and tm_grup_akses as grup_akses >= $user
                                    if ($menu_child['tm_status'] == 1 && $menu_child['tm_grup_akses'] >= $user) {
                                        if (menu_child_child($menu_child['tm_id']) != null) {
                                            echo '<li class="nav-item has-treeview">';
                                            echo '<a href="' . base_url($menu_child['tm_url']) . '" class="nav-link">';
                                            echo '<i class="nav-icon ' . $menu_child['tm_icon'] . ' mr-1"></i>';
                                            echo '<p>' . $menu_child['tm_nama'] . '<i class="right fas fa-angle-left"></i></p>';
                                            echo '</a>';
                                            echo '<ul class="nav nav-treeview nav-third-level">';
                                            foreach (menu_child_child($menu_child['tm_id']) as $menu_child_child) {
                                                // make 3 level menu with tm_status as status = 1 and tm_grup_akses as grup_akses >= $user
                                                if ($menu_child_child['tm_status'] == 1 && $menu_child_child['tm_grup_akses'] >= $user) {
                                                    if (menu_child_child_child($menu_child_child['tm_id']) != null) {
                                                        echo '<li class="nav-item has-treeview">';
                                                        echo '<a href="' . base_url($menu_child_child['tm_url']) . '" class="nav-link">';
                                                        echo '<i class="nav-icon ' . $menu_child_child['tm_icon'] . ' mr-1"></i>';
                                                        echo '<p>' . $menu_child_child['tm_nama'] . '<i class="right fas fa-angle-left"></i></p>';
                                                        echo '</a>';
                                                        echo '<ul class="nav nav-treeview nav-fourth-level">';
                                                        foreach (menu_child_child_child($menu_child_child['tm_id']) as $menu_child_child_child) {
                                                            // make 2 level menu with tm_status as status = 1 and tm_grup_akses as grup_akses >= $user
                                                            if ($menu_child_child_child['tm_status'] == 1 && $menu_child_child_child['tm_grup_akses'] >= $user) {
                                                                echo '<li class="nav-item">';
                                                                echo '<a href="' . base_url($menu_child_child_child['tm_url']) . '" class="nav-link">';
                                                                echo '<i class="nav-icon ' . $menu_child_child_child['tm_icon'] . ' mr-1"></i>';
                                                                echo '<p>' . $menu_child_child_child['tm_nama'] . '</p>';
                                                                echo '</a>';
                                                                echo '</li>';
                                                            }
                                                        }
                                                        echo '</ul>';
                                                        echo '</li>';
                                                    } else {
                                                        echo '<li class="nav-item">';
                                                        echo '<a href="' . base_url($menu_child_child['tm_url']) . '" class="nav-link">';
                                                        echo '<i class="nav-icon ' . $menu_child_child['tm_icon'] . ' mr-1"></i>';
                                                        echo '<p>' . $menu_child_child['tm_nama'] . '</p>';
                                                        echo '</a>';
                                                        echo '</li>';
                                                    }
                                                }
                                            }
                                            echo '</ul>';
                                            echo '</li>';
                                        } else {
                                            echo '<li class="nav-item">';
                                            echo '<a href="' . base_url($menu_child['tm_url']) . '" class="nav-link">';
                                            echo '<i class="nav-icon ' . $menu_child['tm_icon'] . ' mr-1"></i>';
                                            echo '<p>' . $menu_child['tm_nama'] . '</p>';
                                            echo '</a>';
                                            echo '</li>';
                                        }
                                    }
                                }
                                echo '</ul>';
                                echo '</li>';
                            } else {
                                echo '<li class="nav-item">';
                                echo '<a href="' . base_url($menu['tm_url']) . '" class="nav-link">';
                                echo '<i class="nav-icon ' . $menu['tm_icon'] . ' mr-1"></i>';
                                echo '<p>' . $menu['tm_nama'] . '</p>';
                                echo '</a>';
                                echo '</li>';
                            }
                        }
                    }
                } ?>
                <li class="nav-item" id="keluar">
                    <a href="/logout" class="nav-link">
                        <i class="nav-icon fa-fw fa fa-sign-out-alt"></i>
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