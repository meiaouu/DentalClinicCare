<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0; font-family:system-ui, sans-serif; background:#f5f7fb;">

<div style="display:flex; min-height:100vh;">

    {{-- Sidebar --}}
    <aside style="width:260px; background:#fff; border-right:1px solid #e5e7eb;">
        @include('staff.partials.sidebar')
    </aside>

    {{-- Main Content --}}
    <main style="flex:1; padding:20px;">
        @yield('content')
    </main>

</div>

</body>
</html>
