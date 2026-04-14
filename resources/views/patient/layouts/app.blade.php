<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0; font-family:system-ui, sans-serif; background:#f8fafc;">

<div style="display:flex; min-height:100vh;">

    <div style="width:260px; flex-shrink:0;">
        @include('patient.partials.sidebar')
    </div>

    <main style="flex:1; padding:20px;">
        @yield('content')
    </main>

</div>

</body>
</html>
