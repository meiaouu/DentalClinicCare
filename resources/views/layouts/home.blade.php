@extends('layouts.app')

@section('content')
    @include('layouts.partials.public-navbar', ['clinic' => $clinic])

    <section id="home" class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=1600&q=80"
                alt="Dental clinic"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-[#09111D]/95 via-[#09111D]/75 to-[#09111D]/35"></div>
        </div>

        <div class="relative max-w-6xl mx-auto px-4 lg:px-6 pt-44 pb-24 min-h-screen flex items-center">
            <div class="max-w-2xl text-white">
                <p class="text-xl md:text-2xl mb-4 tracking-wide text-slate-200">
                    Professional Dental Care
                </p>

                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">
                    Healthy Smiles<br>
                    Start Here
                </h1>

                <p class="text-base md:text-lg text-slate-200 max-w-xl mb-10">
                    Book appointments easily, explore dental services, and receive reliable care
                    from a clinic that values comfort, safety, and healthy smiles.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('booking.create') }}"
                       class="px-7 py-3 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg">
                        Book Now
                    </a>

                    <a href="#services"
                       class="px-7 py-3 rounded-full border border-white/70 text-white font-semibold hover:bg-white hover:text-slate-900 transition">
                        View All Services
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-24 bg-white scroll-mt-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-6">
            <div class="text-center mb-14">
                <p class="text-blue-600 font-semibold mb-2">Our Services</p>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Dental services for every smile</h2>
                <p class="text-slate-600 mt-4 max-w-2xl mx-auto">
                    Explore some of the services available in the clinic.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($services as $service)
                    <div class="bg-slate-50 rounded-3xl p-6 shadow-sm hover:shadow-md transition border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold mb-4">
                            {{ strtoupper(substr($service['name'], 0, 1)) }}
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ $service['name'] }}</h3>
                        <p class="text-slate-600 leading-relaxed">{{ $service['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="gallery" class="py-24 bg-slate-100 scroll-mt-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-6">
            <div class="text-center mb-14">
                <p class="text-blue-600 font-semibold mb-2">Gallery</p>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Inside our clinic</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="rounded-3xl overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=900&q=80" alt="Clinic 1" class="w-full h-72 object-cover">
                </div>
                <div class="rounded-3xl overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=900&q=80" alt="Clinic 2" class="w-full h-72 object-cover">
                </div>
                <div class="rounded-3xl overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1620775997736-4565bb35d2f0?auto=format&fit=crop&w=900&q=80" alt="Clinic 3" class="w-full h-72 object-cover">
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-24 bg-white scroll-mt-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-6 grid lg:grid-cols-2 gap-14 items-center">
            <div>
                <p class="text-blue-600 font-semibold mb-2">About Clinic</p>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">
                    Comfortable, modern, and patient-friendly care
                </h2>
                <p class="text-slate-600 leading-8 mb-4">
                    We focus on making every visit feel organized, welcoming, and professional.
                    From consultation to follow-up care, our clinic supports both new and returning patients.
                </p>
                <p class="text-slate-600 leading-8">
                    Our goal is to provide quality dental services while making appointment booking
                    and clinic communication simple and convenient.
                </p>
            </div>

            <div class="rounded-3xl overflow-hidden shadow-lg">
                <img src="https://images.unsplash.com/photo-1588776813677-77aaf5595b83?auto=format&fit=crop&w=1200&q=80" alt="About clinic" class="w-full h-[420px] object-cover">
            </div>
        </div>
    </section>

    <section id="contact" class="py-24 bg-slate-100 scroll-mt-28">
        <div class="max-w-6xl mx-auto px-4 lg:px-6">
            <div class="text-center mb-12">
                <p class="text-blue-600 font-semibold mb-2">Contact</p>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Get in touch with us</h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h3 class="text-2xl font-semibold mb-6">Clinic Information</h3>

                    <div class="space-y-4 text-slate-700">
                        <p><span class="font-semibold">Phone:</span> {{ $clinic?->contact_number ?? '+63 900 123 4567' }}</p>
                        <p><span class="font-semibold">Email:</span> {{ $clinic?->clinic_email ?? 'clinic@email.com' }}</p>
                        <p><span class="font-semibold">Location:</span> {{ $clinic?->clinic_location ?? 'Quezon City, Philippines' }}</p>
                        <p><span class="font-semibold">Clinic Hours:</span> {{ $clinic?->open_time ?? '08:00 AM' }} - {{ $clinic?->close_time ?? '05:00 PM' }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h3 class="text-2xl font-semibold mb-6">Need an appointment?</h3>
                    <p class="text-slate-600 mb-6">
                        Book your visit in a few steps and let the clinic review your preferred date, service, and dentist.
                    </p>

                    <a href="{{ route('booking.create') }}"
                       class="inline-flex items-center px-6 py-3 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                        Book an Appointment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div id="chat" class="fixed bottom-6 right-6 z-50">
        <button class="w-16 h-16 rounded-full bg-white text-slate-900 shadow-xl border border-slate-200 text-sm font-bold hover:scale-105 transition">
            Chat
        </button>
    </div>
@endsection
