<aside id="sidebar" class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="./index.html" class="text-nowrap logo-img">
                <img src="../assets/images/logos/SIAD.png" alt="" width="230" />
            </a>

            <!-- Mobile close button - keep the original one -->
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>

        </div>

        @php
            $role = Auth::user()->role;
            // $role = "admin";
        @endphp

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item active">
                    <a class="sidebar-link" href="{{ $role == 'admin' ? '/admin' : '/' }}" aria-expanded="false">
                        <span>
                            <iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                @if ($role == 'admin')
                    <!-- Menu AKUN -->
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">Akun Pengguna</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/admin/buat-akun" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Tambah Pengguna</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/akuntan-divisi" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Akuntan Divisi</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/akuntan-unit" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Akuntan Unit</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/auditor" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Auditor</span>
                        </a>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">Akun Keuangan</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/kategori-akun" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Kategori Akun</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/sub-kategori-akun" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Sub Kategori Akun</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/akun" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Akun</span>
                        </a>
                    </li>
                @endif


                <!-- Menu Pencatatan dan Laporan hanya untuk user selain admin -->
                @props(['role', 'hak_akses'])

                @if ($role != 'admin')
                    {{-- Pencatatan --}}
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">Pencatatan</span>
                    </li>

                    {{-- Input Transaksi hanya untuk selain auditor --}}
                    @if ($role !== 'auditor')
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/input-transaksi" aria-expanded="false">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Input Transaksi</span>
                            </a>
                        </li>
                    @endif

                    {{-- Jurnal Umum --}}
                    @if ($role === 'akuntan_divisi' || $role === 'auditor' || optional($hak_akses)->view_jurnal_umum)
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/jurnal-umum" aria-expanded="false">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Jurnal Umum</span>
                            </a>
                        </li>
                    @endif

                    {{-- Buku Besar --}}
                    @if ($role === 'akuntan_divisi' || $role === 'auditor' || optional($hak_akses)->view_buku_besar)
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/buku-besar" aria-expanded="false">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Buku Besar</span>
                            </a>
                        </li>
                    @endif

                    {{-- Laporan --}}
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">Laporan</span>
                    </li>

                    {{-- Looping Menu Laporan --}}
                    @php
                        $menus = [
                            'view_laporan_komprehensif' => [
                                'url' => '/laporan-komprehensif',
                                'label' => 'Komprehensif',
                            ],
                            'view_laporan_neraca' => ['url' => '/neraca-saldo', 'label' => 'Neraca'],
                            'view_laporan_posisi_keuangan' => ['url' => '#', 'label' => 'Posisi Keuangan'],
                            'view_laporan_arus_kas' => ['url' => '/arus-kas', 'label' => 'Arus Kas'],
                            'view_laporan_perubahan_aset_neto' => ['url' => '#', 'label' => 'Perubahan Aset Neto'],
                            'view_laporan_catatan_atas_laporan_keuangan' => ['url' => '#', 'label' => 'CALK'],
                            'view_laporan_proyeksi_rencana_dan_realisasi_anggaran' => [
                                'url' => '#',
                                'label' => 'PRRA',
                            ],
                        ];
                    @endphp

                    @foreach ($menus as $permission => $menu)
                        @if ($role === 'akuntan_divisi' || $role === 'auditor' || optional($hak_akses)->$permission)
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ $menu['url'] }}" aria-expanded="false">
                                    <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                            class="fs-6"></iconify-icon></span>
                                    <span class="hide-menu">{{ $menu['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach

                @endif

                @if ($role == 'admin' || $role == 'auditor')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">Aktivitas</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/log-aktivitas" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Log Aktivitas</span>
                        </a>
                    </li>
                @endif


            </ul>
        </nav>


        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>











{{-- @if ($role == 'admin')
                    <!-- Menu AKUN -->
                    <!-- Trigger -->
                    <li class="nav-small-cap dropdown-trigger" style="cursor: pointer;">
                        <div class="d-flex align-items-center justify-content-between" data-bs-toggle="collapse"
                            data-bs-target="#menuUser" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-dots nav-small-cap-icon fs-6 me-2"></i>
                                <span class="hide-menu">Akun Pengguna</span>
                            </div>
                            <span class="arrow-toggle-user">&#9662;</span> <!-- ▼ = panah bawah -->
                        </div>
                    </li>

                    <!-- Dropdown content -->
                    <ul class="collapse first-level" id="menuUser">
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/admin/buat-akun">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Tambah Pengguna</span> </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/akuntan-unit">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Akuntan Unit</span> </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/akuntan-divisi">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Akuntan Divisi</span> </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/auditor">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Auditor</span> </a>
                        </li>
                    </ul>

                    <!-- Trigger Akun Keuangan -->
                    <li class="nav-small-cap dropdown-trigger" style="cursor: pointer;">
                        <div class="d-flex align-items-center justify-content-between" data-bs-toggle="collapse"
                            data-bs-target="#menuKeuangan" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-dots nav-small-cap-icon fs-6 me-2"></i>
                                <span class="hide-menu">Akun Keuangan</span>
                            </div>
                            <span class="arrow-toggle-keuangan">&#9662;</span> <!-- ▼ panah bawah -->
                        </div>
                    </li>

                    <!-- Dropdown content Akun Keuangan -->
                    <ul class="collapse first-level" id="menuKeuangan">
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/kategori-akun">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Kategori Akun</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/sub-kategori-akun">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Sub Kategori Akun</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/akun">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">Akun</span>
                            </a>
                        </li>
                    </ul>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Define menu configurations
                            const menus = [
                                { 
                                    triggerSelector: '[data-bs-target="#menuUser"]', 
                                    targetId: 'menuUser',
                                    arrowSelector: '.arrow-toggle-user'
                                },
                                { 
                                    triggerSelector: '[data-bs-target="#menuKeuangan"]', 
                                    targetId: 'menuKeuangan',
                                    arrowSelector: '.arrow-toggle-keuangan'
                                }
                            ];
                            
                            // Process each menu
                            menus.forEach(menu => {
                                const triggerEl = document.querySelector(menu.triggerSelector);
                                const arrow = document.querySelector(menu.arrowSelector);
                                const collapseEl = document.getElementById(menu.targetId);
                                
                                // Initialize Bootstrap collapse with manual control
                                const bsCollapse = new bootstrap.Collapse(collapseEl, {
                                    toggle: false
                                });
                                
                                // Check if current page is in this menu section
                                const isInSection = Array.from(collapseEl.querySelectorAll('.sidebar-link'))
                                    .some(link => link.href === window.location.href);
                                
                                // Initially open the menu if current page is in this section
                                if (isInSection) {
                                    bsCollapse.show();
                                    arrow.innerHTML = '&#9652;'; // ▲
                                }
                                
                                // Toggle collapse when trigger is clicked
                                triggerEl.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    
                                    if (collapseEl.classList.contains('show')) {
                                        bsCollapse.hide();
                                        arrow.innerHTML = '&#9662;'; // ▼
                                    } else {
                                        bsCollapse.show();
                                        arrow.innerHTML = '&#9652;'; // ▲
                                    }
                                });
                                
                                // Update arrow when collapse state changes
                                collapseEl.addEventListener('shown.bs.collapse', function() {
                                    arrow.innerHTML = '&#9652;'; // ▲
                                });
                                
                                collapseEl.addEventListener('hidden.bs.collapse', function() {
                                    arrow.innerHTML = '&#9662;'; // ▼
                                });
                            });
                        });
                    </script>
                @endif --}}
