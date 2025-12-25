<!doctype html>
<html lang="en">
    <head>        
        <meta charset="utf-8" />
        <title>SISFO STMIK Indonesia Padang</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Sistem Informasi STMIK Indonesia Padang" name="description" />
        <meta content="Lembaga Pengembangan Teknologi Informasi - LPTI" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{!! asset('assets/images/icon-login.ico') !!}">

        <!-- Responsive Table css -->
        <link href="{!! asset('assets/libs/admin-resources/rwd-table/rwd-table.min.css') !!}" rel="stylesheet" type="text/css" />

        <!-- preloader css -->
        <link rel="stylesheet" href="{!! asset('assets/css/preloader.min.css') !!}" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="{!! asset('assets/css/bootstrap.min.css') !!}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{!! asset('assets/css/icons.min.css') !!}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{!! asset('assets/css/app.min.css') !!}" id="app-style" rel="stylesheet" type="text/css" />

        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css" />
        @livewireStyles

    </head>

    <body>

    <!-- <body data-layout="horizontal"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">            
           
            
            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="#" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{!! asset('assets/images/logo_stmik.jpg') !!}" alt="" height="24">
                                </span>
                                <span class="logo-lg">
                                    <img src="{!! asset('assets/images/logo_stmik.jpg') !!}" alt="" height="24"> <span class="logo-txt">STMIK Indonesia</span>
                                </span>
                            </a>

                            <a href="index.html" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{!! asset('assets/images/logo_stmik.jpg') !!}" alt="" height="24">
                                </span>
                                <span class="logo-lg">
                                    <img src="{!! asset('assets/images/logo_stmik.jpg') !!}" alt="" height="24"> <span class="logo-txt">STMIK Indonesia</span>
                                </span>
                            </a>
                        </div>
                        

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                        <form class="app-search d-none d-lg-block">
                            <div class="position-relative">
                                <span class="logo-txt">Quisioner Pengguna Alumni</span>
                            </div>
                        </form>
                        
                    </div>

                    
                </div>
            </header>
                <div class="page-content">
                    <div class="container-fluid">
                        {{ $slot }}                        
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                              
            
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->        
        

        @stack('modals')        
        <!-- ckeditor -->
        <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>
        <script>
            ClassicEditor
                .create( document.querySelector( '#note'))
                .then( editor => {
                   editor.model.document.on('change:data', () => {
                        let note = $('#note').data('note');
                        eval(note).set('ket', editor.getData());
                   });
                })
                .catch( error => {
                    console.error( error );
                });
        </script>

        <script>
            ClassicEditor
                .create( document.querySelector( '#note2'))                                                
                .then( editor => {                                        
                editor.model.document.on('change:data', () => {                    
                        let note2 = $('#note2').data('note2');
                        eval(note2).set('ket2', editor.getData());
                });                
                })
                .catch( error => {
                    console.error( error );
                });                
        </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
        @livewireScripts

        <!-- JAVASCRIPT -->
        <script src="{!! asset('assets/libs/jquery/jquery.min.js') !!}"></script>
        <script src="{!! asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') !!}"></script>
        <script src="{!! asset('assets/libs/metismenu/metisMenu.min.js') !!}"></script>
        <script src="{!! asset('assets/libs/simplebar/simplebar.min.js') !!}"></script>
        <script src="{!! asset('assets/libs/node-waves/waves.min.js') !!}"></script>
        <script src="{!! asset('assets/libs/feather-icons/feather.min.js') !!}"></script>
        <!-- pace js -->
        <script src="{!! asset('assets/libs/pace-js/pace.min.js') !!}"></script>

        <!-- Responsive Table js -->
        <script src="{!! asset('assets/libs/admin-resources/rwd-table/rwd-table.min.js') !!}"></script>

        <!-- init js -->
        <script src="{!! asset('assets/js/pages/form-editor.init.js') !!}"></script>

        <!-- Init js -->
        <script src="{!! asset('assets/js/pages/table-responsive.init.js') !!}"></script>

        <!-- Bootstrap Toasts Js -->
        <script src="{!! asset('assets/css/bootstrap.min.cssassets/js/pages/bootstrap-toasts.init.js') !!}"></script>

        <script src="{!! asset('assets/js/app.js') !!}"></script>
        <script>
            window.livewire.on('tutup', () => {                
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })   
            
            var toastTrigger = document.getElementById('liveToastBtn')
            var toastLiveExample = document.getElementById('liveToast')
            if (toastTrigger) {
            toastTrigger.addEventListener('click', function () {
                var toast = new bootstrap.Toast(toastLiveExample)

                toast.show()
            })
            }
        </script>
    </body>
</html>
