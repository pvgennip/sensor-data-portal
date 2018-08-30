<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>

    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Akvo sensor">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="shortcut icon" type="image/png" href="/webapp/img/icons/Icon-40.png"/>
    <link rel="apple-touch-icon" href="webapp/img/icons/Icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/webapp/img/icons/Icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/webapp/img/icons/Icon-60@2x.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/webapp/img/icons/Icon-72@2x.png">

    <!-- admin LTE template -->
    <link rel="stylesheet" href="/webapp/vendor/admin-lte/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="/webapp/css/portal.css">
    <link rel="stylesheet" href="/webapp/css/skin-akvo.css">
    <link rel="stylesheet" type="text/css" href="/webapp/vendor/normalize-css/normalize.css">

    <link rel="stylesheet" href="/webapp/vendor/datatables/media/css/dataTables.bootstrap4.min.css" media="screen">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    @yield('head')

</head>
<body class="hold-transition skin-blue fixed @yield('body-class')">
    <div id="app">
        
        @if (Auth::guest())
        @else
            <div class="wrapper">

                @include('layouts/admin-lte/header')
                @include('layouts/admin-lte/sidebar-left')

                <div class="content-wrapper">
        @endif
        
                    <section class="content-header">
                        
                        <h1>
                            @yield('page-title')
                        </h1>
                        @yield('breadcrum')

                    </section>

                    <section class="content" >
        
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
        
                        @yield('content')

                    </section>

        @if (Auth::guest())
        @else
                </div>

                @include('layouts/admin-lte/footer')
            
            </div>
        @endif

    </div>

    <!-- AdminLTE App -->
    <script src="/webapp/vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="/webapp/vendor/admin-lte/bootstrap/js/bootstrap.min.js"></script>
    <script src="/webapp/vendor/admin-lte/dist/js/app.min.js"></script>
    <script src="/webapp/vendor/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    
    <script src="/webapp/vendor/datatables.net/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="/webapp/vendor/datatables/media/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready( function () {
            $('table').DataTable(
                {
                    "language": 
                        @php
                            echo File::get(public_path('/webapp/vendor/datatables.net-plugins/i18n/English.lang'));
                        @endphp
                    ,
                    "order": 
                    [
                        [ 2, "asc" ]
                    ],
                }
            );
        } );
    </script>
</body>
</html>

