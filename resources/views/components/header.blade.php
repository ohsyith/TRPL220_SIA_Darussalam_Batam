<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
            <div class="close-btn d-none d-xl-block sidebartoggler cursor-pointer" id="desktopSidebarToggle">
                <i class="ti ti-chevrons-left fs-8"></i>
            </div>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item dropdown d-flex align-items-center">
                    <span class="d-none d-md-inline text-dark fw-semibold me-2">{{ Auth::user()->nama }}</span>

                    <a class="nav-link p-0" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35"
                            class="rounded-circle nav-icon-hover">
                    </a>

                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">Akun Saya</p>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="mx-3 mt-2 d-block">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
