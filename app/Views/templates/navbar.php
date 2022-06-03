<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="/dashboard" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <img src="/assets/dist/img/profile/default.png" class="img-size-50 mr-3 img-circle float-right" style="width: 24px;">
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="/assets/dist/img/profile/default.png" class="img-size-50 mr-3 img-circle">
                        <span class="text-success text-sm"><i class="fas fa-circle fa-sm"></i> </span>
                        <div class="media-body">
                            <h3 class="dropdown-item-title pl-1">
                                <?= ucwords(strtolower(session()->get('fullname'))); ?> </h3>
                            <p class="text-sm pl-1">
                                <?php foreach ($statusRole as $row) { ?>
                                    <?php echo session()->get('role_id') == $row['id_role'] ? $row['nm_role'] : ''; ?>
                                <?php } ?>
                            </p>
                            <p class="text-sm text-muted pl-1"><i class="far fa-clock mr-1"></i></p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Keluar dari Aplikasi
                </a>
                <div class="dropdown-divider"></div>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->

    </ul>
</nav>