@extends('layouts.app')

@section('content')
    @include('layouts.partials.public-navbar', ['clinic' => $clinic])

    <section id="home" style="min-height:100vh; background:#0f172a; color:white; padding:160px 24px 100px;">
        <div style="max-width:1100px; margin:0 auto;">
            <p style="font-size:22px; margin-bottom:16px; color:#cbd5e1;">Professional Dental Care</p>

            <h1 style="font-size:64px; line-height:1.1; font-weight:800; margin-bottom:24px;">
                Healthy Smiles<br>Start Here
            </h1>

            <p style="max-width:700px; font-size:18px; line-height:1.8; color:#e2e8f0; margin-bottom:32px;">
                Book appointments easily, explore dental services, and receive reliable care
                from a clinic that values comfort, safety, and healthy smiles.
            </p>

            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                <a href="{{ route('booking.entry') }}"
                   style="padding:14px 24px; background:#2563eb; color:white; border-radius:999px; font-weight:600;">
                    Book Now
                </a>

                <a href="#services"
                   style="padding:14px 24px; border:1px solid white; color:white; border-radius:999px; font-weight:600;">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <section id="services" style="padding:90px 24px; background:white;">
        <div style="max-width:1100px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:50px;">
                <p style="color:#2563eb; font-weight:700; margin-bottom:8px;">Our Services</p>
                <h2 style="font-size:40px; font-weight:800; color:#0f172a;">Dental services for every smile</h2>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:24px;">
                @foreach ($services as $service)
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:24px; padding:24px;">
                        <div style="width:48px; height:48px; border-radius:16px; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; font-weight:700; margin-bottom:16px;">
                            {{ strtoupper(substr($service->service_name, 0, 1)) }}
                        </div>

                        <h3 style="font-size:22px; font-weight:700; color:#0f172a; margin-bottom:12px;">
                            {{ $service->service_name }}
                        </h3>

                        <p style="color:#475569; line-height:1.7;">
                            {{ $service->description }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="gallery" style="padding:90px 24px; background:#f1f5f9;">
        <div style="max-width:1100px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:50px;">
                <p style="color:#2563eb; font-weight:700; margin-bottom:8px;">Gallery</p>
                <h2 style="font-size:40px; font-weight:800; color:#0f172a;">Inside our clinic</h2>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
                <div style="height:260px; border-radius:24px; background:#cbd5e1;"></div>
                <div style="height:260px; border-radius:24px; background:#cbd5e1;"></div>
                <div style="height:260px; border-radius:24px; background:#cbd5e1;"></div>
            </div>
        </div>
    </section>

    <section id="about" style="padding:90px 24px; background:white;">
        <div style="max-width:1100px; margin:0 auto;">
            <p style="color:#2563eb; font-weight:700; margin-bottom:8px;">About Clinic</p>
            <h2 style="font-size:40px; font-weight:800; color:#0f172a; margin-bottom:20px;">
                Comfortable, modern, and patient-friendly care
            </h2>

            <p style="color:#475569; line-height:1.9; max-width:850px; margin-bottom:12px;">
                We focus on making every visit feel organized, welcoming, and professional.
                From consultation to follow-up care, our clinic supports both new and returning patients.
            </p>

            <p style="color:#475569; line-height:1.9; max-width:850px;">
                Our goal is to provide quality dental services while making appointment booking
                and clinic communication simple and convenient.
            </p>
        </div>
    </section>

    <section id="contact" style="padding:90px 24px; background:#f1f5f9;">
        <div style="max-width:1100px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:50px;">
                <p style="color:#2563eb; font-weight:700; margin-bottom:8px;">Contact</p>
                <h2 style="font-size:40px; font-weight:800; color:#0f172a;">Get in touch with us</h2>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px;">
                <div style="background:white; border-radius:24px; padding:32px;">
                    <h3 style="font-size:28px; font-weight:700; margin-bottom:20px;">Clinic Information</h3>

                    <div style="display:grid; gap:12px; color:#334155;">
                        <p><strong>Phone:</strong> {{ $clinic?->contact_number ?? '+63 900 123 4567' }}</p>
                        <p><strong>Email:</strong> {{ $clinic?->clinic_email ?? 'clinic@email.com' }}</p>
                        <p><strong>Location:</strong> {{ $clinic?->clinic_location ?? 'Quezon City, Philippines' }}</p>
                        <p><strong>Clinic Hours:</strong> {{ $clinic?->open_time ?? '08:00:00' }} - {{ $clinic?->close_time ?? '17:00:00' }}</p>
                    </div>
                </div>

                <div style="background:white; border-radius:24px; padding:32px;">
                    <h3 style="font-size:28px; font-weight:700; margin-bottom:20px;">Need an appointment?</h3>

                    <p style="color:#475569; line-height:1.8; margin-bottom:20px;">
                        Book your visit in a few steps and let the clinic review your preferred date,
                        service, and dentist.
                    </p>

                    <a href="{{ route('booking.entry') }}"
                       style="display:inline-block; padding:14px 24px; background:#2563eb; color:white; border-radius:999px; font-weight:600;">
                        Book an Appointment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div id="chat" style="position:fixed; right:24px; bottom:24px;">
        <button style="width:64px; height:64px; border:none; border-radius:999px; background:white; box-shadow:0 10px 30px rgba(0,0,0,0.15); font-weight:700;">
            Chat
        </button>
    </div>
@endsection
