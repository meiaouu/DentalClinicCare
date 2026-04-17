@php
    $clinic = $clinic ?? \App\Models\ClinicSetting::query()->first();
@endphp

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

    /* ================= TOPBAR ================= */
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

    /* ================= MAINBAR ================= */
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

    /* ================= BRAND ================= */
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
    }

    .brand-name {
        line-height: 1.2;
    }

    /* ================= NAV ================= */
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

    /* ================= BUTTON ================= */
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
    }

    /* ================= CONTENT ================= */
    .site-content {
        padding-top: 110px;
        min-height: 100vh;
    }

    /* ================= RESPONSIVE ================= */
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
    }
</style>

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
                <a href="{{ optional($clinic)->facebook_url ?: '#' }}" class="topbar-link">Facebook</a>
                <a href="{{ optional($clinic)->instagram_url ?: '#' }}" class="topbar-link">Instagram</a>
                <a href="{{ optional($clinic)->messenger_url ?: '#' }}" class="topbar-link">Messenger</a>
            </div>
        </div>
    </div>

    <div class="mainbar">
        <div class="mainbar-inner">
           <div style="display:flex; align-items:center; gap:10px;">

    <!-- TOOTH ICON -->
    <svg width="33" height="33" viewBox="0 0 64 64" fill="none" stroke="#0f172a" stroke-width="2">
        <path d="M20 10C12 10 8 16 10 24C12 32 16 50 22 50C26 50 26 40 32 40C38 40 38 50 42 50C48 50 52 32 54 24C56 16 52 10 44 10C40 10 36 12 32 14C28 12 24 10 20 10Z"/>
    </svg>

    <!-- DIVIDER -->
    <div style="width:1px; height:32px; background:#0f172a;"></div>

    <!-- TEXT -->
    <div style="line-height:1.1;">
        <div style="font-size:13px; font-weight:600; color:#374151;">
            Dr. Brendalyn Wansi Calacat
        </div>

        <div style="font-size:17px; font-weight:800; color:#111827;">
            Dental Clinic
        </div>

        <div style="font-size:10px; color:#6b7280;">
            Personalized Care
        </div>
    </div>

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
