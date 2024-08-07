<!DOCTYPE html>
<html lang="en">

<head>
    @include('app.layouts.header')
    <title>@yield('title')</title>
    @stack('styles')
</head>

<body>
@include('app.layouts.navBar')

<div style="min-height: 100vh">
    @yield('content')
</div>

<!-- -------------------Footer----------------- -->
@include('app.layouts.footer')
@include('app.layouts.scripts')
@yield('scripts')

</body>

</html>
