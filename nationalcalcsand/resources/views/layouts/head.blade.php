<head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8" />

    <title>{{ $title or Config::get('app.name', "Title") }}</title>

    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="description" content="" />
    <meta name="csrf-token" ng-init="csrf_token = {{ csrf_token(); }}" content="{{ csrf_token(); }}">

    <!-- Mobile Specific Metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS
    ================================================== -->
	@section('styles')
		@include('layouts.defaultCSS')
	@show

    <style>
        @yield('inlineCSS')
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="{{{ asset('assets/js/libs/html5shiv-printshiv-3.7.2-master.min.js') }}}"></script>
    <![endif]-->

    <!-- Favicons
    ================================================== -->
    <link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">
</head>