    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand-md bg-body">
        <div class="container-fluid">
            <!-- Navbar Toggler for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!--begin::Start Navbar Links-->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
            </ul>
            <!--end::Start Navbar Links-->

            <!-- Collabsible Content -->
            <div class="collapse navbar-collapse" id="navbarContent">


            <!--begin::End Navbar Links-->
            <ul class="navbar-nav ms-auto">
                <!--begin::Navbar Search-->
                <!--begin::Navbar Search-->
                <li class="nav-item">
                    <form class="d-flex" action="/FastBuy/admin/show-products.php" method="GET" style="margin-right: 10px;">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search products..." aria-label="Search">
                        <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </li>
                <!--end::Navbar Search-->
                <!--end::Navbar Search-->

                <!--begin::Fullscreen Toggle-->
                <li class="nav-item">
                    <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                        <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                        <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                    </a>
                </li>
                <!--end::Fullscreen Toggle-->

                <!--begin::User Menu Dropdown-->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="/FastBuy/assets/img/logo.png" class="user-image rounded-circle" alt="User Image" />
                        <span class="d-none d-md-inline" style="font-weight: 900;">FastBuy</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <li class="user-header text-bg-primary">
                            <img src="/FastBuy/assets/img/logo.png" class="rounded-circle shadow" alt="User Image">
                            <p>
                                FastBuy Admin
                                <small>Member since 2024</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <a href="#" class="btn btn-default btn-flat">Profile</a>
                            <a href="/FastBuy/actions/logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                        </li>
                    </ul>
                </li>
            </ul>


            </div>
            <!--end::Collabsible Content -->
        </div>
        <!--end::Container-->
    </nav>
    <!--end::Header-->