<header style="width:100%; position:fixed; top:0; left:0; z-index:1000;">
    <div style="background:#09111D; color:white; font-size:12px;">
        <div style="max-width:1100px; margin:0 auto; padding:12px 16px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px;">
            <div style="display:flex; flex-wrap:wrap; gap:18px;">
                <span>{{ $clinic?->contact_number ?? '+63 900-1234-5678' }}</span>
                <span>{{ $clinic?->clinic_email ?? 'abcdefgfh@gmail.com' }}</span>
                <span>{{ $clinic?->clinic_location ?? 'Purok 5, Sta. Rosa, Nueva Ecija' }}</span>
            </div>

            <div style="display:flex; gap:14px;">
                <a href="{{ $clinic?->facebook_url ?: '#' }}" style="color:white;">Facebook</a>
                <a href="{{ $clinic?->instagram_url ?: '#' }}" style="color:white;">Instagram</a>
                <a href="{{ $clinic?->messenger_url ?: '#' }}" style="color:white;">Messenger</a>
            </div>
        </div>
    </div>

    <div style="background:white; box-shadow:0 4px 14px rgba(0,0,0,0.06);">
        <div style="max-width:1100px; margin:0 auto; padding:18px 16px; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:8px; font-weight:800; font-size:22px; color:#0f172a;">
                <span style="display:inline-flex; width:30px; height:30px; border-radius:6px; background:#0f172a; color:white; align-items:center; justify-content:center; font-size:12px;">D</span>
                <span>Dr Brendalyn Wansi Calacat</span>
            </a>

            <nav style="display:flex; flex-wrap:wrap; gap:22px; font-size:14px; font-weight:600;">
                <a href="#home" style="color:#334155;">Home</a>
                <a href="#services" style="color:#334155;">Services</a>
                <a href="#gallery" style="color:#334155;">Gallery</a>
                <a href="#about" style="color:#334155;">About Clinic</a>
                <a href="#chat" style="color:#334155;">Chat</a>

                @if (auth()->check())
                    <a href="{{ route('booking.create') }}" style="color:#334155;">My Booking</a>
                @elseif (Route::has('login'))
                    <a href="{{ route('login') }}" style="color:#334155;">Login</a>
                @else
                    <span style="color:#94a3b8;">Login</span>
                @endif
            </nav>

            <a href="{{ route('booking.entry') }}"
               style="padding:12px 20px; background:#2563eb; color:white; border-radius:999px; font-weight:700;">
                Book an Appointment
            </a>
        </div>
    </div>
</header>



