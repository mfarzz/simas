<!doctype html>
<html lang="en">
    <head>        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Sistem Inventaris Universitas Andalas">
        <meta name="author" content="Universitas Andalas">
        <!-- App favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('assets/images/favicon_logo.ico') !!}" />
        <title>Aset UNAND</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Bootstrap Css -->
        <link href="{!! asset('assets/assets_aset/css/bootstrap.min.css') !!}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{!! asset('assets/assets_aset/css/icons.min.css') !!}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{!! asset('assets/assets_aset/css/app.min.css') !!}" id="app-style" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{!! asset('assets/css/stylebaru.css') !!}">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.4/dayjs.min.js"></script>
        <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>-->
        <link  href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
       
        @livewireStyles
    </head>

    <body data-sidebar="dark">

    <!-- <body data-layout="horizontal"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

            
            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="/dashboard" class="logo logo-dark"> Unand
                                <span class="logo-sm">
                                    <img src="assets/images/logo.svga" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{!! asset('assets/images/header-e-aset.png') !!}" alt="" height="17">
                                </span>
                            </a>

                            <a href="/dashboard" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-light.svga" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{!! asset('assets/images/header-e-aset.png') !!}" alt="" height="65">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                            @if(auth()->user()->role_id == 1) Admin 
                            @elseif(auth()->user()->role_id == 2) Kepala Seksi Perlengkapan
                            @elseif(auth()->user()->role_id == 4) Subdit Pengelolaan Aset
                            @elseif(auth()->user()->role_id == 5) Operator
                            @elseif(auth()->user()->role_id == 6) Pimpinan
                            @elseif(auth()->user()->role_id == 7) Operator
                            @elseif(auth()->user()->role_id == 8) Pimpinan
                            @endif
                            {{ session()->get('posisi') }}
                        </button>
                    </div>

                    <div class="d-flex">
                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                                <i class="bx bx-fullscreen"></i>
                            </button>
                        </div>

                        <div class="dropdown d-inline-block">
                            
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="@if(auth()->user()->profile_photo_path==""){!! asset('assets/images/no_image.jpeg') !!} @else {{ asset('storage/akunprofile/'.auth()->user()->profile_photo_path.'') }} @endif"
                                    alt="">
                                <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ auth()->user()->name }}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="/profile"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf    
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                             onclick="event.preventDefault();
                                                    this.closest('form').submit();"><i class="dropdown-ite mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</a>  
                                </form>   
                                
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <x-menu-kanan-sis-aset/>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                @yield('konten')                        
                <!-- End Page-content -->
                <x-footer-sis-aset/>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->
        
        @livewireScripts
        @stack('modals')
        <!-- JAVASCRIPT -->
        <script src="{!! asset('assets/libs/jquery/jquery.min.js') !!}"></script>
        <script src="{!! asset('assets/assets_aset/libs/bootstrap/js/bootstrap.bundle.min.js') !!}"></script>
        <script src="{!! asset('assets/assets_aset/libs/metismenu/metisMenu.min.js') !!}"></script>
        <script src="{!! asset('assets/assets_aset/libs/simplebar/simplebar.min.js') !!}"></script>
        <script src="{!! asset('assets/assets_aset/libs/node-waves/waves.min.js') !!}"></script>

        <!-- Init js -->
        <script src="{!! asset('assets/assets_aset/js/pages/table-responsive.init.js') !!}"></script>
        
        <script src="{!! asset('assets/assets_aset/js/app.js') !!}"></script>

        <script src="{!! asset('assets/assets_aset/js/pages/bootstrap-toastr.init.js') !!}"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <script src="{!! asset('assets/js/rupiah.js') !!}"></script>
        <script src="{!! asset('assets/js/puluhan.js') !!}"></script>

        
        <script>
            function showSaveMessage(message) {
                toastr.success(message, 'Berhasil', {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: true,
                    positionClass: 'toast-top-right', // Menentukan posisi notifikasi di kanan atas
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: '300',
                    hideDuration: '1000',
                    timeOut: '5000', // Durasi notifikasi ditampilkan (milidetik), dalam contoh ini 5 detik
                    extendedTimeOut: '1000',
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                });
            }
            function showDeleteMessage(message) {
                toastr.error(message, 'Berhasil', {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: true,
                    positionClass: 'toast-top-right', // Menentukan posisi notifikasi di kanan atas
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: '300',
                    hideDuration: '1000',
                    timeOut: '5000', // Durasi notifikasi ditampilkan (milidetik), dalam contoh ini 5 detik
                    extendedTimeOut: '1000',
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                });
            }
            function showDangerMessage(message) {
                toastr.error(message, 'Gagal', {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: true,
                    positionClass: 'toast-top-right', // Menentukan posisi notifikasi di kanan atas
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: '300',
                    hideDuration: '1000',
                    timeOut: '5000', // Durasi notifikasi ditampilkan (milidetik), dalam contoh ini 5 detik
                    extendedTimeOut: '1000',
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                });
            }
            function showWarningMessage(message) {
                toastr.warning(message, 'Gagal', {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: true,
                    positionClass: 'toast-top-right', // Menentukan posisi notifikasi di kanan atas
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: '300',
                    hideDuration: '1000',
                    timeOut: '5000', // Durasi notifikasi ditampilkan (milidetik), dalam contoh ini 5 detik
                    extendedTimeOut: '1000',
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                });
            }
        </script>
    </body>
</html>
