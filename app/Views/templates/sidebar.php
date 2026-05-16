<?php

$user_image = session()->get('user_image');
$user = session()->get('role_id');

// connect to request uri
$request = \Config\Services::request();
$uri = $request->getUri()->getSegment(1);
$menus = menu()
?>

<aside class="main-sidebar sidebar-sinden elevation-4">

    <a href="/pages" class="brand-link border-bottom border-secondary">
        <img src="<?= logoApp(); ?>" alt="Logo SINDEN" class="brand-image img-circle elevation-3" style="opacity: .8">
    </a>

    <div class="sidebar">

        <div class="mt-3 pb-3 mb-3 border-bottom border-secondary text-center">
            <div class="d-flex flex-column align-items-center">
                <span class="text-white font-weight-bold text-uppercase" style="font-size: 0.95rem; letter-spacing: 1.5px;">
                    <?= nameApp(); ?>
                </span>
                <span class="text-muted mt-1" style="font-size: 0.75rem;">
                    <?= titleApp(); ?>
                </span>
            </div>
        </div>

        <div class="px-3 mb-2">
            <span class="text-uppercase text-muted text-xs font-weight-bold">Menu Utama</span>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php foreach ($menus as $menu) : ?>

                    <?php
                    // Validasi akses menu
                    if ($menu['tm_status'] != 1 || $menu['tm_grup_akses'] < $user) continue;

                    // 👇 TAMBAHKAN BARIS INI UNTUK MENYEMBUNYIKANNYA DARI SIDEBAR 👇
                    if ($menu['tm_url'] === 'pembaruan-keluarga/pemulihan') continue;

                    // Apakah menu ini punya child?
                    $children = menu_child($menu['tm_id']);
                    $hasChild = !empty($children);

                    // Tentukan apakah parent harus terbuka
                    $isMenuOpen = $hasChild && menu_is_open($children);

                    // Tentukan apakah menu ini aktif
                    $isActive = menu_is_active($menu['tm_url']);
                    ?>

                    <?php if ($menu['tm_parent_id'] == 0) : ?>

                        <!-- MENU LEVEL 1 -->
                        <li class="nav-item <?= $isMenuOpen ? 'menu-open' : ''; ?>">

                            <a href="<?= $hasChild ? '#' : menu_url($menu['tm_url']); ?>"
                                class="nav-link <?= ($isActive || $isMenuOpen) ? 'active' : ''; ?>">

                                <i class="nav-icon <?= $menu['tm_icon']; ?> mr-1"></i>
                                <p>
                                    <?= $menu['tm_nama']; ?>
                                    <?php if ($hasChild) : ?>
                                        <i class="right fas fa-angle-left"></i>
                                    <?php endif; ?>
                                </p>
                            </a>

                            <?php if ($hasChild) : ?>
                                <ul class="nav nav-treeview nav-second-level">

                                    <?php foreach ($children as $child) : ?>

                                        <?php
                                        if ($child['tm_status'] != 1 || $child['tm_grup_akses'] < $user) continue;

                                        $child2 = menu_child_child($child['tm_id']);
                                        $hasChild2 = !empty($child2);

                                        $isOpen2 = $hasChild2 && menu_is_open($child2);
                                        $isActive2 = menu_is_active($child['tm_url']);
                                        ?>

                                        <li class="nav-item <?= $isOpen2 ? 'menu-open' : ''; ?>">

                                            <a href="<?= $hasChild2 ? '#' : menu_url($child['tm_url']); ?>"
                                                class="nav-link <?= ($isActive2 || $isOpen2) ? 'active' : ''; ?>">

                                                <i class="nav-icon <?= $child['tm_icon']; ?> mr-1"></i>
                                                <p>
                                                    <?= $child['tm_nama']; ?>
                                                    <?php if ($hasChild2) : ?>
                                                        <i class="right fas fa-angle-left"></i>
                                                    <?php endif; ?>
                                                </p>
                                            </a>

                                            <?php if ($hasChild2) : ?>
                                                <ul class="nav nav-treeview nav-third-level">

                                                    <?php foreach ($child2 as $child_lvl2) : ?>

                                                        <?php
                                                        if ($child_lvl2['tm_status'] != 1 || $child_lvl2['tm_grup_akses'] < $user) continue;

                                                        $child3 = menu_child_child_child($child_lvl2['tm_id']);
                                                        $hasChild3 = !empty($child3);

                                                        $isActive3 = menu_is_active($child_lvl2['tm_url']);
                                                        ?>

                                                        <li class="nav-item">

                                                            <a href="<?= menu_url($child_lvl2['tm_url']); ?>"
                                                                class="nav-link <?= $isActive3 ? 'active' : ''; ?>">

                                                                <i class="nav-icon <?= $child_lvl2['tm_icon']; ?> mr-1"></i>
                                                                <p><?= $child_lvl2['tm_nama']; ?></p>
                                                            </a>

                                                        </li>

                                                    <?php endforeach; ?>

                                                </ul>
                                            <?php endif; ?>

                                        </li>

                                    <?php endforeach; ?>

                                </ul>
                            <?php endif; ?>

                        </li>

                    <?php endif; ?>

                <?php endforeach; ?>


                <!-- Logout -->
                <li class="nav-item" id="keluar">
                    <a href="/logout" class="nav-link">
                        <i class="nav-icon fa-fw fa fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->

</aside>