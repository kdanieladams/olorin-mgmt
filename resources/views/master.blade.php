<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>@yield('title', 'Administration') | MGMT</title>
        @yield('vendor_head')
        <link rel="stylesheet" type="text/css" href="/css/mgmt_styles.css">
        @yield('head')
    </head>
    <body class="mgmt">
        <div class="master-wrapper">
            @include('mgmt::_navbar')

            @section('header')

            @stop

            <div class="container content">
                @yield('main')
            </div>

            @include('mgmt::_footer')
        </div>

        {{--@include('_flash-messages')--}}

        <script type="text/javascript" src="/js/mgmt_scripts.js"></script>
        @include('mgmt::_flash-messages')
        @yield('scripts')
    </body>
</html>