<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
</head>
<body class="antialiased">
@php
    dd(
        Browser::deviceType(),
        Browser::deviceFamily() ,
        Browser::platformFamily(),
        Browser::deviceModel(),
        Browser::platformName(),
        Browser::browserFamily(),
        Browser::browserName(),
        )
@endphp
</body>
</html>
