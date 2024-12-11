<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="New Mansoura University Housing offers comfortable, affordable, and secure accommodation for students. Conveniently located near the campus, our modern facilities provide a supportive environment for academic success.">
    <meta name="keywords" content="New Mansoura University, university housing, student accommodation, New Mansoura, dorms, student apartments, affordable student housing, university residence, student life, student housing, NMU housing, New Mansoura student living, secure housing for students">
    <meta name="author" content="Themesbox">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>404 - Page Not Found</title>
    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <!-- Start CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    
</head>
<body class="error-page">
    <div class="container error-container mt-4">
        <!-- Row for Logo -->
        <div class="row">
            <div class="col-12">
                <img src="{{ asset('images/logo.png') }}" class="img-fluid error-logo" alt="logo">
            </div>
        </div>

        <!-- Row for Image -->
        <div class="row">
            <div class="col-12">
                <img src="{{ asset('images/error/404.svg') }}" class="img-fluid error-image" alt="404">
            </div>
        </div>

        <!-- Row for Button -->
        <div class="row">
            <div class="col-12">
                <h4 class="error-subtitle mb-3">Oops! Page not Found</h4>
                <p class="mb-4">We did not find the page you are looking for. Please return to the previous page or visit the home page.</p>
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="feather icon-home me-2"></i> Go back to Home
                </a>
            </div>
        </div>
    </div>

    <!-- Start js -->        
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/modernizr.min.js') }}"></script>
    <script src="{{ asset('js/detect.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <!-- End js -->
</body>
</html>
