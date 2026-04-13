<header style="width:100%; position:fixed; top:0; left:0; z-index:1000; font-family:inherit;">
    <div style="background:#0b0f13; color:#ffffff; font-size:12px; border-bottom:1px solid rgba(255,255,255,0.08);">
        <div style="max-width:1100px; margin:0 auto; padding:10px 16px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px;">
            <div style="display:flex; flex-wrap:wrap; gap:18px; color:#d1d5db;">
                <span style="display:flex; align-items:center; gap:6px;">
                    <span style="color:#0f9d8a; font-weight:700;">●</span>
                    {{ $clinic?->contact_number ?? '+63 900-1234-5678' }}
                </span>

                <span style="display:flex; align-items:center; gap:6px;">
                    <span style="color:#0f9d8a; font-weight:700;">●</span>
                    {{ $clinic?->clinic_email ?? 'abcdefgfh@gmail.com' }}
                </span>

                <span style="display:flex; align-items:center; gap:6px;">
                    <span style="color:#0f9d8a; font-weight:700;">●</span>
                    {{ $clinic?->clinic_location ?? 'Purok 5, Sta. Rosa, Nueva Ecija' }}
                </span>
            </div>

            <div style="display:flex; gap:14px; flex-wrap:wrap;">
                <a href="{{ $clinic?->facebook_url ?: '#' }}"
                   style="color:#e5e7eb; text-decoration:none; font-weight:600; transition:0.2s ease;">
                    Facebook
                </a>
                <a href="{{ $clinic?->instagram_url ?: '#' }}"
                   style="color:#e5e7eb; text-decoration:none; font-weight:600; transition:0.2s ease;">
                    Instagram
                </a>
                <a href="{{ $clinic?->messenger_url ?: '#' }}"
                   style="color:#e5e7eb; text-decoration:none; font-weight:600; transition:0.2s ease;">
                    Messenger
                </a>
            </div>
        </div>
    </div>

    <div style="background:#ffffff; border-bottom:1px solid #e5e7eb; box-shadow:0 8px 24px rgba(0,0,0,0.06);">
        <div style="max-width:1100px; margin:0 auto; padding:16px 16px; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
            <a href="{{ route('home') }}"
               style="display:flex; align-items:center; gap:10px; font-weight:800; font-size:22px; color:#0b0f13; text-decoration:none; line-height:1.2;">
                <span style="display:inline-flex; width:38px; height:38px; border-radius:10px; background:#0f9d8a; color:#ffffff; align-items:center; justify-content:center; font-size:14px; box-shadow:0 8px 18px rgba(15,157,138,0.25);">
                    D
                </span>
                <span style="color:#111827;">Dr Brendalyn Wansi Calacat</span>
            </a>

            <nav style="display:flex; flex-wrap:wrap; align-items:center; gap:22px; font-size:14px; font-weight:700;">
                <a href="#home" style="color:#1f2937; text-decoration:none;">Home</a>
                <a href="#services" style="color:#1f2937; text-decoration:none;">Services</a>
                <a href="#gallery" style="color:#1f2937; text-decoration:none;">Gallery</a>
                <a href="#about" style="color:#1f2937; text-decoration:none;">About Clinic</a>
                <a href="#chat" style="color:#1f2937; text-decoration:none;">Chat</a>

                @if (auth()->check())
                    <a href="{{ route('booking.create') }}" style="color:#1f2937; text-decoration:none;">My Booking</a>
                @elseif (Route::has('login'))
                    <a href="{{ route('login') }}" style="color:#1f2937; text-decoration:none;">Login</a>
                @else
                    <span style="color:#9ca3af;">Login</span>
                @endif
            </nav>

            <a href="{{ route('booking.entry') }}"
               style="display:inline-flex; align-items:center; justify-content:center; padding:12px 22px; background:#0f9d8a; color:#ffffff; border-radius:999px; font-weight:800; text-decoration:none; box-shadow:0 10px 24px rgba(15,157,138,0.24); transition:0.2s ease;">
                Book an Appointment
            </a>
        </div>
    </div>
</header>
