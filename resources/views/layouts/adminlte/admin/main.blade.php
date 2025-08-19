<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Our-ERP | @yield('title')</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    @include('layouts.adminlte.partials.admin.css')
    <!--end::Required Plugin(AdminLTE)-->
    @yield('extra_css')
    @yield('style')
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      @include('layouts.adminlte.partials.admin.header')
      @include('layouts.adminlte.partials.admin.sidebar')
      @yield('content')
      @include('layouts.adminlte.partials.admin.footer')
    </div>
    <!--end::App Wrapper-->
</body>
@yield('modal')
@include('layouts.adminlte.partials.admin.script')
@yield('script')
  <!--end::Body-->
</html>
