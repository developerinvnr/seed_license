<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
   <head>
      <meta charset="utf-8" />
      <title>Login | License</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="shortcut icon" href="{{ URL::to('/') }}/assets/images/favicon.ico">
      <script src="{{ URL::to('/') }}/assets/js/layout.js"></script>
      <link href="{{ URL::to('/') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
      <link href="{{ URL::to('/') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
      <link href="{{ URL::to('/') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
      <link href="{{ URL::to('/') }}/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
   </head>
   <body>
      {{ $slot }}
      <script src="{{ URL::to('/') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="{{ URL::to('/') }}/assets/libs/simplebar/simplebar.min.js"></script>
      <script src="{{ URL::to('/') }}/assets/libs/node-waves/waves.min.js"></script>
      <script src="{{ URL::to('/') }}/assets/libs/feather-icons/feather.min.js"></script>
      <script src="{{ URL::to('/') }}/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
      <script src="{{ URL::to('/') }}/assets/js/plugins.js"></script>
      <script src="{{ URL::to('/') }}/assets/js/pages/password-addon.init.js"></script>
   </body>
</html>
