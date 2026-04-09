<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentist Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f8fafc; color: #0f172a; }
        .shell { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #0f172a; color: white; padding: 24px 16px; }
        .brand { font-size: 20px; font-weight: 800; margin-bottom: 28px; }
        .nav-link { display: block; color: #cbd5e1; text-decoration: none; padding: 12px 14px; border-radius: 12px; margin-bottom: 8px; }
        .nav-link:hover, .nav-link.active { background: #1e293b; color: #fff; }
        .content { flex: 1; padding: 28px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .logout-btn { border: none; background: #dc2626; color: white; padding: 10px 14px; border-radius: 10px; cursor: pointer; font-weight: 700; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; }
    </style>
</head>
<body>
<div class="shell">
    @include('dentist.layouts.sidebar')

    <main class="content">
        <div class="topbar">
            <div><strong>Dentist Portal</strong></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:12px;margin-bottom:16px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:12px;margin-bottom:16px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
