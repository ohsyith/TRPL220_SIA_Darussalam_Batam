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
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/budget-rapbs-akun" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">RAPBS Akun</span>
                        </a>
                    </li>





                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">Kegiatan</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/kegiatan" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Kegiatan</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/budget-rapbs-kegiatan" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">RAPBS Kegiatan</span>
                        </a>
                    </li>
                @endif


                <!-- Menu Pencatatan dan Laporan hanya untuk user selain admin -->

                @if (in_array($role, ['akuntan_unit']))
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                        <span class="hide-menu">RAPBS</span>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/budget-rapbs-akun" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">RAPBS Akun</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/budget-rapbs-kegiatan" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">RAPBS Kegiatan</span>
                        </a>
                    </li>
                @endif



                @props(['role', 'hak_akses'])

                {{-- Pencatatan --}}
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">Pencatatan</span>
                </li>

                {{-- Input Transaksi hanya untuk selain auditor --}}
                @if (in_array($role, ['admin']) || optional($hak_akses)->create_jurnal_umum)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/input-transaksi" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Input Transaksi</span>
                        </a>
                    </li>
                @endif

                {{-- Jurnal Umum --}}
                @if (in_array($role, ['admin', 'auditor']) || optional($hak_akses)->view_jurnal_umum)
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/jurnal-umum" aria-expanded="false">
                            <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                    class="fs-6"></iconify-icon></span>
                            <span class="hide-menu">Jurnal Umum</span>
                        </a>
                    </li>
                @endif

                {{-- Buku Besar --}}
                @if (in_array($role, ['admin', 'auditor']) || optional($hak_akses)->view_buku_besar)
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
                        'view_laporan_komprehensif' => ['url' => '/laporan-komprehensif', 'label' => 'Komprehensif'],
                        'view_laporan_posisi_keuangan' => ['url' => '/neraca-saldo', 'label' => 'Posisi Keuangan'],
                        'view_laporan_arus_kas' => ['url' => '/arus-kas', 'label' => 'Arus Kas'],
                        'view_laporan_perubahan_aset_neto' => [
                            'url' => '/perubahan-aset-neto',
                            'label' => 'Perubahan Aset Neto',
                        ],
                        'view_laporan_catatan_atas_laporan_keuangan' => ['url' => '/calk', 'label' => 'CALK'],
                        'view_laporan_proyeksi_rencana_dan_realisasi_anggaran' => ['url' => '/prra', 'label' => 'PRRA'],
                    ];
                @endphp

                @foreach ($menus as $permission => $menu)
                    @if (in_array($role, ['admin', 'auditor']) || optional($hak_akses)->$permission)
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ $menu['url'] }}" aria-expanded="false">
                                <span><iconify-icon icon="solar:home-smile-bold-duotone"
                                        class="fs-6"></iconify-icon></span>
                                <span class="hide-menu">{{ $menu['label'] }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach


                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                    <span class="hide-menu">SOP</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/sop" aria-expanded="false">
                        <span><iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon></span>
                        <span class="hide-menu">SOP</span>
                    </a>
                </li>

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
