@php
    $clinic = $clinic ?? \App\Models\ClinicSetting::query()->first();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Dental Clinic Care') }}</title>

    <style>
        :root {
            --topbar-bg: #0b0f13;
            --brand-dark: #0b0f13;
            --nav-text: #1f2937;
            --nav-text-light: #9ca3af;
            --primary: #0f9d8a;
            --primary-hover: #0d8574;
            --white: #ffffff;
            --page-bg: #f9fafb;
            --border: #e5e7eb;
            --shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--page-bg);
            color: var(--brand-dark);
        }

        a {
            text-decoration: none;
        }

        .site-header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .topbar {
            background: var(--topbar-bg);
            color: var(--white);
            font-size: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .topbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 10px 16px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
        }

        .topbar-left span {
            color: #d1d5db;
        }

        .topbar-left span::before {
            content: "●";
            color: var(--primary);
            margin-right: 6px;
            font-size: 10px;
        }

        .topbar-link {
            color: #e5e7eb;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .topbar-link:hover {
            color: var(--primary);
        }

        .mainbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .mainbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 16px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 22px;
            color: var(--brand-dark);
        }

        .brand-icon {
            display: inline-flex;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--primary);
            color: var(--white);
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 8px 20px rgba(15,157,138,0.25);
            flex-shrink: 0;
        }

        .brand-name {
            line-height: 1.2;
        }

        .main-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            font-size: 14px;
            font-weight: 700;
            align-items: center;
            justify-content: center;
        }

        .main-nav a {
            color: var(--nav-text);
            transition: 0.2s ease;
        }

        .main-nav a:hover,
        .main-nav a.active-link {
            color: var(--primary);
        }

        .muted-link {
            color: var(--nav-text-light);
        }

        .book-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            background: var(--primary);
            color: var(--white);
            border-radius: 999px;
            font-weight: 800;
            white-space: nowrap;
            transition: 0.2s ease;
            box-shadow: 0 10px 25px rgba(15,157,138,0.25);
        }

        .book-btn:hover {
            background: var(--primary-hover);
            color: var(--white);
        }

        .site-content {
            padding-top: 110px;
            min-height: 100vh;
        }

        @media (max-width: 991px) {
            .topbar-inner,
            .mainbar-inner {
                justify-content: center;
                text-align: center;
            }

            .brand-link {
                justify-content: center;
            }

            .main-nav {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .brand-link {
                font-size: 18px;
            }

            .main-nav {
                gap: 14px;
                font-size: 13px;
            }

            .book-btn {
                width: 100%;
            }

            .site-content {
                padding-top: 135px;
            }
        }
    </style>
</head>
<body>

<header class="site-header">
    <div class="topbar">
        <div class="topbar-inner">
            <div class="topbar-left">
                <span>{{ $clinic?->contact_number ?? '+63 900-1234-5678' }}</span>
                <span>{{ $clinic?->clinic_email ?? 'abcdefgfh@gmail.com' }}</span>
                <span>{{ $clinic?->clinic_location ?? 'Purok 5, Sta. Rosa, Nueva Ecija' }}</span>
            </div>

            <div class="topbar-right">
                <a href="{{ $clinic?->facebook_url ?: '#' }}" class="topbar-link">Facebook</a>
                <a href="{{ $clinic?->instagram_url ?: '#' }}" class="topbar-link">Instagram</a>
                <a href="{{ $clinic?->messenger_url ?: '#' }}" class="topbar-link">Messenger</a>
            </div>
        </div>
    </div>

    <div class="mainbar">
        <div class="mainbar-inner">
            <a href="{{ route('home') }}" class="brand-link">
                <span class="brand-icon">D</span>
                <span class="brand-name">{{ $clinic?->clinic_name ?? 'Dr Brendalyn Wansi Calacat' }}</span>
            </a>

            <a href="{{ route('booking.entry') }}" class="book-btn">
                Book an Appointment
            </a>
        </div>
    </div>
</header>

<main class="site-content">
    @yield('content')
</main>

</body>
</html>
