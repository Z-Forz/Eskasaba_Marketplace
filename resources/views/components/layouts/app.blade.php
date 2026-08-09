{{-- resources/views/components/layouts/app.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>App Layout</title>
</head>
<body>
    {{ $slot ?? '' }}
</body>
</html>
