<x-guest-layout>
    <!doctype html>
    <html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
        data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

    <head>
        <meta charset="utf-8" />
        <title>Sign In | License Tracking</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="assets/images/license_logo_v.png" height="80" />
        <script src="assets/js/layout.js"></script>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <div class="auth-page-wrapper pt-5">
            <div class="auth-one-bg-position auth-one-bg">
                <div class="bg-overlay"></div>
                <div class="shape">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        viewBox="0 0 1440 120">
                        <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="auth-page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mt-sm-5 mb-4 text-white-50">
                                <div>

                                </div>
                                <p class="mt-3 fs-15 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-5" style="margin-top: 85px;">
                            <div class="card mt-4 card-bg-fill">

                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <span class="logo-sm">
                                            <img src="{{ asset('') }}assets/images/License_logo.png" alt=""
                                                height="80" />
                                        </span>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="Enter Email">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password-input">Password</label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input type="password" class="form-control pe-5 password-input"
                                                        placeholder="Enter password" id="password" name="password">
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none"
                                                        type="button" id="password-addon"><i
                                                            class="ri-eye-fill align-middle"
                                                            id="togglePassword"></i></button>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Sign In</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">&copy;
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> License Agreement & Tracking with
                                    by VNR Seeds Private Limited
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>
        <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
        <script src="assets/js/plugins.js"></script>
        {{-- <script src="assets/libs/particles.js/particles.js"></script>
    <script src="assets/js/pages/particles.app.js"></script> --}}
        <script src="assets/js/pages/password-addon.init.js"></script>
    </body>
    <script>
        document.getElementById("togglePassword").onclick = function() {
            var password = document.getElementById("password");
            password.type = password.type === "password" ? "text" : "password";
        };
    </script>

    </html>
</x-guest-layout>
