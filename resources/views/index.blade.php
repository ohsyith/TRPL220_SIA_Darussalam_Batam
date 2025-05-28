<!doctype html>
<html lang="en">
test
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIA Yayasan Darussalam | Akun</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/YDB_PNG.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../css/sidebar.css" />

</head>
<style>
  html,
  body,
  .page-wrapper,
  .body-wrapper {
    height: 100%;
    min-height: 100vh;
  }

  .body-wrapper {
    display: flex;
    flex-direction: column;
  }

  .container-fluid {
    flex: 1;
  }
</style>

<body>

  @php
  $user = Auth::user();
  @endphp

  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <x-sidebar></x-sidebar>
    <!--  Sidebar End -->

    <!--  Main wrapper -->
    <div class="body-wrapper">

      <!--  Header Start -->
      <x-header></x-header>
      <!--  Header End -->

      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Selamat datang di Dashboard, {{ $user->nama }}</h5>
                <p class="card-text">Ini adalah halaman dashboard utama Anda.</p>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="py-6 px-6 text-center">
        <p class="mb-0 fs-4">Sistem Informasi Akuntansi Yayasan Darussalam Batam | 2025</p>

      </div>
    </div>
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    {{-- sidebar --}}
    <script src="../js/sidebar.js"></script>


</body>

</html>