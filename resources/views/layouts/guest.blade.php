<!doctype html>
<html lang="en">    
    <head>     
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- App favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('assets/images/favicon_logo.ico') !!}" />

        <title>Sistem Informasi Persediaan Universitas Andalas</title>
    
        <!-- Vendors Style-->
        <link rel="stylesheet" href="{!! asset('assets/css/vendors_css.css') !!}">
        
        <!-- Style-->  
        <link rel="stylesheet" href="{!! asset('assets/css/style.css') !!}">
        <link rel="stylesheet" href="{!! asset('assets/css/skin_color.css') !!}">	
    </head>
        
    <body class="hold-transition theme-primary bg-img" style="background-image: url(assets/images/auth-bg/bg-13.jpg)">
               
       {{ $slot }}

        <!-- Vendor JS -->
        <script src="{!! asset('assets/js/vendors.min.js') !!}"></script>
        <script src="{!! asset('assets/js/pages/chat-popup.js') !!}"></script>
        <script src="{!! asset('assets/assets/icons/feather-icons/feather.min.js') !!}"></script>	
    </body>

</html>