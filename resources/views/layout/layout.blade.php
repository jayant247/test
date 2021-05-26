<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bloom App - Admin Dashboard</title>
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('css')
</head>
<body>
{{--<div id="preloader">--}}
{{--    <div class="loader">--}}
{{--        <svg class="circular" viewBox="25 25 50 50">--}}
{{--            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />--}}
{{--        </svg>--}}
{{--    </div>--}}
{{--</div>--}}
<div id="main-wrapper">



    <!--**********************************
        Header start
    ***********************************-->
    @if(Auth::user())
        @include('layout.header')
    @endif
    <!--**********************************
        Header end ti-comment-alt
    ***********************************-->

    <!--**********************************
        Sidebar start
    ***********************************-->
    @if(Auth::user())
        @include('layout.sidebar')
    @endif
    <!--**********************************
        Sidebar end
    ***********************************-->

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">

    @yield('content')
        <!-- #/ container -->
    </div>
    <!--**********************************
        Content body end
    ***********************************-->


    <!--**********************************
        Footer start
    ***********************************-->
    <div class="footer">
        <div class="copyright">
            <p></p>
        </div>
    </div>
    <!--**********************************
        Footer end
    ***********************************-->
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha512-+NqPlbbtM1QqiK8ZAo4Yrj2c4lNQoGv8P79DPtKzj++l5jnN39rHA/xsqn8zE9l0uSoxaCdrOgFs6yjyfbBxSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/2.7.9/metisMenu.min.js" integrity="sha512-yFbKQKDgWCvfjZIFfQQXoppmUdCiqp5fmYMK6VZUOq+mdUrdMOp93vPaSu61OpmLMZgUdypuG9dC1nroTrj2+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js" integrity="sha512-cJMgI2OtiquRH4L9u+WQW+mz828vmdp9ljOcm/vKTQ7+ydQUktrPVewlykMgozPP+NUBbHdeifE6iJ6UVjNw5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha512-n6dYFOG599s4/mGlA6E+YLgtg9uPTOMDUb0IprSMDYVLr0ctiRryPEQ8gpM4DCMlx7M2G3CK+ZcaoOoJolzdCg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="js/custom.min.js"></script>
<script src="js/settings.js"></script>
<script src="js/gleek.js"></script>
<script src="js/styleSwitcher.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js" integrity="sha512-baRe8aL3+Cvo8XRk+YJ/1KdJDBi4ay7ENnfap/gEdRR+rz0rCTHBNDVCYm6HlR3WmMFCvRS4fZk2qNJIYyPgGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@yield('js')

</body>
</html>
