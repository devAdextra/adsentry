<!doctype html>
<html lang="en" data-bs-theme="white-theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagina non trovata | {{ config('app.name') }}</title>
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/pace.min.js') }}"></script>

    <!--Styles-->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/blue-theme.css') }}" rel="stylesheet">
  </head>

<body class="bg-error">

<!-- Start wrapper-->
 <div class="pt-5">
 
    <div class="container pt-5">
            <div class="row pt-5">
                <div class="col-lg-12">
                    <div class="text-center error-pages">
                        <h1 class="error-title text-primary mb-3">404</h1>
                        <h2 class="error-sub-title text-dark">404 NOT FOUND</h2>

                        <p class="error-message text-secondary text-uppercase">SORRY, Questa pagina non esiste!</p>
                        
                        <div class="mt-4 d-flex align-items-center justify-content-center gap-3">
                          <a href="/" class="btn btn-grd-danger rounded-5 px-4 text-white"><i class="bi bi-house-fill me-2"></i>Home</a>
                          <a href="javascript:history.back();" class="btn btn-outline-dark rounded-5 px-4"><i class="bi bi-arrow-left me-2"></i>Pagina precedente</a>
                        </div>

                        <div class="mt-4">
                            <p class="text-muted">Copyright © {{ date('Y') }} | All rights reserved.</p>
                        </div>
                           <hr class="border-dark border-2">
                           <!--<div class="list-inline contacts-social mt-4"> 
                            <a href="#" class="list-inline-item bg-facebook text-dark border-0"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="list-inline-item bg-pinterest text-dark border-0"><i class="bi bi-pinterest"></i></a>
                            <a href="#" class="list-inline-item bg-whatsapp text-dark border-0"><i class="bi bi-whatsapp"></i></a>
                            <a href="#" class="list-inline-item bg-linkedin text-dark border-0"><i class="bi bi-linkedin"></i></a>
                          </div>-->
                    </div>
                </div>
            </div><!--end row-->
        </div>

 </div><!--wrapper-->

</body>
</html>
