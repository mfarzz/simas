<!doctype html>
<html lang="en">
    <head>        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Sistem Informasi Persediaan Universitas Andalas">
        <meta name="author" content="Universitas Andalas">
        
        <!-- App favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('assets/images/favicon_logo.ico') !!}" />
        <title>Persediaan UNAND</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Vendors Style-->
        <link rel="stylesheet" href="{!! asset('assets/css/vendors_css.css') !!}">
        
        <!-- Style-->  
        <link rel="stylesheet" href="{!! asset('assets/css/style.css') !!}">
        <link rel="stylesheet" href="{!! asset('assets/css/skin_color.css') !!}">
        <link rel="stylesheet" href="{!! asset('assets/css/stylebaru.css') !!}">

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
        <link  href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.4/dayjs.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        @livewireStyles
    </head>

    <body class="hold-transition light-skin sidebar-mini theme-primary fixed">	
        <x-header/>
        <x-menu-kanan/>
        <div class="content-wrapper">
            <div class="container-full">
                @yield('konten')
            </div>
        </div>
        <x-footer/>

        @stack('modals')
        @livewireScripts
        <!-- Vendor JS -->

        <style>
            
        </style>
        
        <script src="{!! asset('assets/js/pages/chat-popup.js') !!}"></script>
        <script src="{!! asset('assets/icons/feather-icons/feather.min.js') !!}"></script>
        <!-- EduAdmin App -->
        <script src="{!! asset('assets/js/template.js') !!}"></script>
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
        <script>
            $('#idUr').select2({
                dropdownParent: $('#data-modal')
            });
            $('#kategori').select2({
                dropdownParent: $('#data-modal')
            });
            $('#idSubkategori').select2({
                dropdownParent: $('#data-modal')
            });
            $('#idItem').select2({
                dropdownParent: $('#data-modal')
            });
            $('#idKelompok').select2({
                dropdownParent: $('#data-modal')
            });
            $('#idJenis').select2({
                dropdownParent: $('#data-modal')
            });
            $('#idBarang').select2({
                dropdownParent: $('#data-modal')
            });
        </script>
        
        <script>
            window.livewire.on('tutup_tambah', () => {    
                $('.formInputModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_ubah', () => {    
                $('.ubahModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_hapus', () => {    
                $('.hapusModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_histori', () => {    
                $('.historiModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_proses', () => {    
                $('.prosesdataModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_selesai', () => {    
                $('.formprosesselesaiModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_perpanjang', () => {    
                $('.formprosesperpanjangModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })
            window.livewire.on('tutup_kembali', () => {    
                $('.formproseskembaliModal').modal('hide');            
                var toastLiveExample = document.getElementById('liveToast')
                var toast = new bootstrap.Toast(toastLiveExample)
                toast.show()
            })                      
            window.livewire.on('gagal', () => {                    
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
