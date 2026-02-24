<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة العميل - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/js/Client/main.ts'])
</head>
<body class="antialiased">
    <div id="client-app"></div>
</body>
</html>
