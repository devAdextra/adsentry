<!doctype html>
<html lang="en" data-bs-theme="light-theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    
    <!--Favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
    
    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/pace.min.js') }}"></script>

    <!-- Plugins CSS -->
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/metismenu/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet">

    <!-- Main CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
    
    <!--main css-->
    <link href="{{ asset('assets/css/dark-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/semi-dark.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bordered-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/responsive.css') }}" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet">

    <!-- ApexCharts -->
    <link href="{{ asset('assets/plugins/apexchart/apexcharts.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <!--start wrapper-->
    <div class="wrapper">
        @include('layouts.partials.sidebar')
        
        <!--start content-->
        <main class="page-content">
            @include('layouts.partials.header')
            
            <!--start main content-->
            <div class="main-content">
                @yield('content')
            </div>
            <!--end main content-->

            @include('layouts.partials.footer')
        </main>
        <!--end content-->
    </div>
    <!--end wrapper-->

    <!-- JS Files -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!-- ApexCharts -->
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    @stack('scripts')
    <script>
    console.log("Elemento scoreChart:", document.querySelector("#scoreChart"));
    console.log("ApexCharts disponibile:", typeof ApexCharts !== 'undefined');
    </script>
</body>
</html>