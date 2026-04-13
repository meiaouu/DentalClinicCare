@extends('layouts.app')

@section('content')
    @include('layouts.partials.public-navbar', ['clinic' => $clinic])

    <section id="home" style="position: relative; min-height: 100vh; overflow: hidden; color: white; padding: 160px 24px 100px;">
        <div style="position: absolute; inset: 0;">
            <img
                src="{{ asset('images/dentalimg.jpg') }}"
                alt="Dental clinic"
                style="width: 100%; height: 100%; object-fit: cover;"
            >
        </div>

        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(11, 15, 19, 0.88) 0%, rgba(31, 41, 55, 0.78) 45%, rgba(15, 157, 138, 0.55) 100%);"></div>

        <div style="max-width: 1100px; margin: 0 auto; position: relative; z-index: 1;">
            <p style="font-size: 20px; margin-bottom: 16px; color: #d1fae5; font-weight: 600; letter-spacing: 0.3px;">
                Professional Dental Care
            </p>

            <h1 style="font-size: clamp(38px, 8vw, 64px); line-height: 1.08; font-weight: 800; margin-bottom: 24px; max-width: 700px; text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);">
                Healthy Smiles<br>Start Here
            </h1>

            <p style="max-width: 700px; font-size: 18px; line-height: 1.9; color: #f3f4f6; margin-bottom: 34px; text-shadow: 0 2px 12px rgba(0, 0, 0, 0.25);">
                Book appointments easily, explore dental services, and receive reliable care
                from a clinic that values comfort, safety, and healthy smiles.
            </p>

            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <a href="{{ route('booking.entry') }}"
                   style="display: inline-block; padding: 14px 28px; background: #0f9d8a; color: white; border-radius: 999px; font-weight: 700; box-shadow: 0 10px 25px rgba(15, 157, 138, 0.28); text-decoration: none; transition: 0.2s ease;">
                    Book Now
                </a>

                <a href="#services"
                   style="display: inline-block; padding: 14px 28px; border: 1px solid rgba(255,255,255,0.85); color: white; border-radius: 999px; font-weight: 700; background: rgba(255,255,255,0.05); text-decoration: none; backdrop-filter: blur(4px); transition: 0.2s ease;">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <section id="services" style="padding: 90px 24px; background: #ffffff;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 52px;">
                <p style="color: #0f9d8a; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.8px;">Our Services</p>
                <h2 style="font-size: 40px; font-weight: 800; color: #0b0f13; margin: 0;">Dental services for every smile</h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                @foreach ($services as $service)
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 24px; padding: 26px; box-shadow: 0 10px 28px rgba(0,0,0,0.05); transition: 0.2s ease;">
                        <div style="width: 52px; height: 52px; border-radius: 16px; background: #ecfdf5; color: #0f9d8a; display: flex; align-items: center; justify-content: center; font-weight: 800; margin-bottom: 18px; font-size: 18px;">
                            {{ strtoupper(substr($service->service_name, 0, 1)) }}
                        </div>

                        <h3 style="font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 12px;">
                            {{ $service->service_name }}
                        </h3>

                        <p style="color: #4b5563; line-height: 1.8; margin: 0;">
                            {{ $service->description }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="gallery" style="padding: 90px 24px; background: #f3f4f6;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 52px;">
                <p style="color: #0f9d8a; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.8px;">Gallery</p>
                <h2 style="font-size: 40px; font-weight: 800; color: #0b0f13; margin: 0;">Inside our clinic</h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <div style="height: 260px; border-radius: 24px; background: linear-gradient(135deg, #d1d5db, #9ca3af); box-shadow: 0 10px 24px rgba(0,0,0,0.06);"></div>
                <div style="height: 260px; border-radius: 24px; background: linear-gradient(135deg, #d1d5db, #9ca3af); box-shadow: 0 10px 24px rgba(0,0,0,0.06);"></div>
                <div style="height: 260px; border-radius: 24px; background: linear-gradient(135deg, #d1d5db, #9ca3af); box-shadow: 0 10px 24px rgba(0,0,0,0.06);"></div>
            </div>
        </div>
    </section>

    <section id="about" style="padding: 90px 24px; background: #ffffff;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <p style="color: #0f9d8a; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.8px;">About Clinic</p>
            <h2 style="font-size: 40px; font-weight: 800; color: #0b0f13; margin-bottom: 20px;">
                Comfortable, modern, and patient-friendly care
            </h2>

            <p style="color: #4b5563; line-height: 1.95; max-width: 850px; margin-bottom: 12px;">
                We focus on making every visit feel organized, welcoming, and professional.
                From consultation to follow-up care, our clinic supports both new and returning patients.
            </p>

            <p style="color: #4b5563; line-height: 1.95; max-width: 850px; margin: 0;">
                Our goal is to provide quality dental services while making appointment booking
                and clinic communication simple and convenient.
            </p>
        </div>
    </section>

    <section id="contact" style="padding: 90px 24px; background: #f3f4f6;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 52px;">
                <p style="color: #0f9d8a; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.8px;">Contact</p>
                <h2 style="font-size: 40px; font-weight: 800; color: #0b0f13; margin: 0;">Get in touch with us</h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
                <div style="background: #ffffff; border-radius: 24px; padding: 32px; border: 1px solid #e5e7eb; box-shadow: 0 10px 24px rgba(0,0,0,0.05);">
                    <h3 style="font-size: 28px; font-weight: 800; margin-bottom: 20px; color: #111827;">Clinic Information</h3>

                    <div style="display: grid; gap: 12px; color: #374151; line-height: 1.8;">
                        <p><strong>Phone:</strong> {{ $clinic?->contact_number ?? '+63 900 123 4567' }}</p>
                        <p><strong>Email:</strong> {{ $clinic?->clinic_email ?? 'clinic@email.com' }}</p>
                        <p><strong>Location:</strong> {{ $clinic?->clinic_location ?? 'Quezon City, Philippines' }}</p>
                        <p><strong>Clinic Hours:</strong> {{ $clinic?->open_time ?? '08:00:00' }} - {{ $clinic?->close_time ?? '17:00:00' }}</p>
                    </div>
                </div>

                <div style="background: #111827; border-radius: 24px; padding: 32px; border: 1px solid #1f2937; box-shadow: 0 10px 24px rgba(0,0,0,0.08);">
                    <h3 style="font-size: 28px; font-weight: 800; margin-bottom: 20px; color: #ffffff;">Need an appointment?</h3>

                    <p style="color: #d1d5db; line-height: 1.85; margin-bottom: 20px;">
                        Book your visit in a few steps and let the clinic review your preferred date,
                        service, and dentist.
                    </p>

                    <a href="{{ route('booking.entry') }}"
                       style="display: inline-block; padding: 14px 24px; background: #0f9d8a; color: white; border-radius: 999px; font-weight: 700; text-decoration: none; box-shadow: 0 10px 20px rgba(15, 157, 138, 0.22);">
                        Book an Appointment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div id="chat" style="position: fixed; right: 24px; bottom: 24px; z-index: 50;">
        <button style="width: 64px; height: 64px; border: none; border-radius: 999px; background: #0f9d8a; color: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.18); font-weight: 800; cursor: pointer;">
            Chat
        </button>
    </div>
@endsection
