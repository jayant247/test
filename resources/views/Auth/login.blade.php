<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bloom App - Admin Dashboard</title>
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="h-100">



    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                <a class="text-center" href="index.html"> <h4>Rosella</h4></a>

                                <form action="" class="mt-5 mb-5 login-input form-validate" id="login-form" >
                                    <div>
                                    <div class="form-group" >
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                    </div>
                                    </div>
                                    <div>

                                    <div class="form-group">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                    </div>
                                    </div>
                                    <button type="submit" class="btn login-form__btn submit w-100">Sign In</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<script src="{{asset('js/functionality/commonService/toaster.js')}}" ></script>
<script src="{{asset('js/functionality/commonService/ajaxcall.js')}}" ></script>
<script src="{{asset('js/functionality/auth/login.js')}}" ></script>

</body>
</html>
