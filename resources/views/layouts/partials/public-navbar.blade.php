<header style="
    width:100%;
    position:fixed;
    top:0;
    left:0;
    z-index:1000;
    font-family:inherit;
    overflow:hidden;
">

    <!-- GLASS CIRCLES -->
    <div style="
        position:absolute;
        inset:0;
        overflow:hidden;
        z-index:0;
        pointer-events:none;
    ">
        <div style="
            position:absolute;
            width:240px;
            height:240px;
            border-radius:50%;
            background:rgba(15,157,138,0.25);
            top:-80px;
            left:-70px;
            filter:blur(70px);
        "></div>

        <div style="
            position:absolute;
            width:200px;
            height:200px;
            border-radius:50%;
            background:rgba(59,130,246,0.22);
            top:10px;
            right:90px;
            filter:blur(70px);
        "></div>

        <div style="
            position:absolute;
            width:160px;
            height:160px;
            border-radius:50%;
            background:rgba(236,72,153,0.18);
            bottom:-50px;
            right:-30px;
            filter:blur(70px);
        "></div>
    </div>

    <!-- TOP BAR -->
    <div style="
        position:relative;
        z-index:1;
        background:rgba(11,15,19,0.40);
        color:#ffffff;
        font-size:12px;
        border-bottom:1px solid rgba(255,255,255,0.15);
        backdrop-filter:blur(18px);
        -webkit-backdrop-filter:blur(18px);
    ">
        <div style="max-width:1100px; margin:0 auto; padding:10px 16px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px;">

            <div style="display:flex; flex-wrap:wrap; gap:18px; color:#e5e7eb;">
                <span style="display:flex; align-items:center; gap:6px;">
                    <span style="color:#5eead4;">●</span>
                    {{ $clinic?->contact_number ?? '+63 900-1234-5678' }}
                </span>

                <span style="display:flex; align-items:center; gap:6px;">
                    <span style="color:#5eead4;">●</span>
                    {{ $clinic?->clinic_email ?? 'abcdefgfh@gmail.com' }}
                </span>

                <span style="display:flex; align-items:center; gap:6px;">
                    <span style="color:#5eead4;">●</span>
                    {{ $clinic?->clinic_location ?? 'Purok 5, Sta. Rosa, Nueva Ecija' }}
                </span>
            </div>

            <div style="display:flex; gap:14px; flex-wrap:wrap;">
                <a href="#" style="color:#f3f4f6; text-decoration:none;">Facebook</a>
                <a href="#" style="color:#f3f4f6; text-decoration:none;">Instagram</a>
                <a href="#" style="color:#f3f4f6; text-decoration:none;">Messenger</a>
            </div>
        </div>
    </div>

    <!-- MAIN NAV -->
    <div style="
        position:relative;
        z-index:1;
        background:rgba(255,255,255,0.35);
        border-bottom:1px solid rgba(255,255,255,0.35);
        box-shadow:0 8px 25px rgba(0,0,0,0.08);
        backdrop-filter:blur(22px);
        -webkit-backdrop-filter:blur(22px);
    ">
        <div style="max-width:1100px; margin:0 auto; padding:16px; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">

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

</div>



            <!-- NAV -->
            <nav style="display:flex; flex-wrap:wrap; gap:20px; font-size:14px; font-weight:600;">
                <a href="#home" style="color:#1f2937; text-decoration:none;">Home</a>
                <a href="#services" style="color:#1f2937; text-decoration:none;">Services</a>
                <a href="#gallery" style="color:#1f2937; text-decoration:none;">Gallery</a>
                <a href="#about" style="color:#1f2937; text-decoration:none;">About Clinic</a>

                @if (auth()->check())
                    <a href="{{ route('booking.create') }}" style="color:#1f2937; text-decoration:none;">My Booking</a>
                @elseif (Route::has('login'))
                    <a href="{{ route('login') }}" style="color:#1f2937; text-decoration:none;">Login</a>
                @endif
            </nav>


            <a href="{{ route('booking.entry') }}"
               style="
                    display:inline-flex;
                    align-items:center;
                    justify-content:center;
                    padding:10px 20px;
                    background: linear-gradient(135deg, #14b8a6, #10b981);
                    color:#ffffff;
                    border-radius:8px;
                    font-weight:700;
                    text-decoration:none;
                    box-shadow:0 8px 18px rgba(14,165,233,0.25);
               ">
                Book an Appointment
            </a>

        </div>
    </div>
</header>
