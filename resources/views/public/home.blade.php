@extends('layouts.app')

@section('content')
    @include('layouts.partials.public-navbar', ['clinic' => $clinic])

    <section id="home" style="position: relative; min-height: 100vh; overflow: hidden; color: white; padding: 140px 20px 80px;">
        <div style="position: absolute; inset: 0;">
            <img
                src="{{ asset('images/dentalimg.jpg') }}"
                alt="Dental clinic"
                style="width: 100%; height: 100%; object-fit: cover;"
            >
        </div>

        <div style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.70);"></div>

        <div style="max-width: 1100px; margin: 0 auto; position: relative; z-index: 1;">
            <p style="font-size: 18px; margin-bottom: 14px; color: #ccfbf1; font-weight: bold;">
                Professional Dental Care
            </p>

            <h1 style="font-size: clamp(34px, 7vw, 58px); line-height: 1.1; font-weight: bold; margin-bottom: 20px; max-width: 650px;">
                Healthy Smiles<br>Start Here
            </h1>

            <p style="max-width: 680px; font-size: 17px; line-height: 1.8; color: #f8fafc; margin-bottom: 28px;">
                Book appointments easily, explore dental services, and receive reliable care
                from a clinic that values comfort, safety, and healthy smiles.
            </p>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('booking.entry') }}"
                   style="display: inline-block; padding: 12px 22px; background: #0f9d8a; color: white; border-radius: 6px; font-weight: bold; text-decoration: none;">
                    Book Now
                </a>

                <a href="#services"
                   style="display: inline-block; padding: 12px 22px; border: 1px solid #ffffff; color: white; border-radius: 6px; font-weight: bold; background: rgba(255,255,255,0.08); text-decoration: none;">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <section id="services" style="padding: 80px 20px; background: #ffffff;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <p style="color: #0f9d8a; font-weight: bold; margin-bottom: 8px;">
                    Our Services
                </p>

                <h2 style="font-size: 32px; font-weight: bold; color: #0f172a; margin: 0;">
                    Dental services for every smile
                </h2>
            </div>

            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            ">
                @foreach ($services as $service)
                    <div style="
                        background: #ffffff;
                        border: 1px solid #dbe2ea;
                        border-radius: 8px;
                        display: flex;
                        flex-direction: column;
                        height: 100%;
                        overflow: hidden;
                    ">
                        <div style="
                            height: 160px;
                            background: #e2e8f0;
                            flex-shrink: 0;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        ">
                            <span style="font-size: 14px; font-weight: bold; color: #475569;">
                                Image
                            </span>
                        </div>

                        <div style="
                            padding: 14px;
                            display: flex;
                            flex-direction: column;
                            flex-grow: 1;
                        ">
                            <h3 style="
                                font-size: 18px;
                                font-weight: bold;
                                color: #111827;
                                margin: 0 0 8px;
                            ">
                                {{ $service->service_name }}
                            </h3>

                            <p style="
                                font-size: 14px;
                                color: #4b5563;
                                line-height: 1.6;
                                margin: 0;
                                flex-grow: 1;
                            ">
                                {{ $service->description }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="gallery" style="padding: 80px 20px; background: #f8fafc;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <p style="color: #0f9d8a; font-weight: bold; margin-bottom: 8px;">
                    Gallery
                </p>

                <h2 style="font-size: 32px; font-weight: bold; color: #0f172a; margin: 0;">
                    Inside our clinic
                </h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                <div style="height: 220px; border-radius: 8px; background: #cbd5e1;"></div>
                <div style="height: 220px; border-radius: 8px; background: #cbd5e1;"></div>
                <div style="height: 220px; border-radius: 8px; background: #cbd5e1;"></div>
            </div>
        </div>
    </section>

    <section id="about" style="padding: 80px 20px; background: #ffffff;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <p style="color: #0f9d8a; font-weight: bold; margin-bottom: 8px;">
                About Clinic
            </p>

            <h2 style="font-size: 32px; font-weight: bold; color: #0f172a; margin-bottom: 18px;">
                Comfortable, modern, and patient-friendly care
            </h2>

            <p style="color: #4b5563; line-height: 1.8; max-width: 820px; margin-bottom: 12px;">
                We focus on making every visit feel organized, welcoming, and professional.
                From consultation to follow-up care, our clinic supports both new and returning patients.
            </p>

            <p style="color: #4b5563; line-height: 1.8; max-width: 820px; margin: 0;">
                Our goal is to provide quality dental services while making appointment booking
                and clinic communication simple and convenient.
            </p>
        </div>
    </section>

    <section id="contact" style="padding: 80px 20px; background: #f8fafc;">
        <div style="max-width: 1100px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <p style="color: #0f9d8a; font-weight: bold; margin-bottom: 8px;">
                    Contact
                </p>

                <h2 style="font-size: 32px; font-weight: bold; color: #0f172a; margin: 0;">
                    Get in touch with us
                </h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="background: #ffffff; border-radius: 8px; padding: 24px; border: 1px solid #dbe2ea;">
                    <h3 style="font-size: 24px; font-weight: bold; margin-bottom: 18px; color: #111827;">
                        Clinic Information
                    </h3>

                    <div style="display: grid; gap: 10px; color: #374151; line-height: 1.7;">
                        <p><strong>Phone:</strong> {{ $clinic?->contact_number ?? '+63 900 123 4567' }}</p>
                        <p><strong>Email:</strong> {{ $clinic?->clinic_email ?? 'clinic@email.com' }}</p>
                        <p><strong>Location:</strong> {{ $clinic?->clinic_location ?? 'Quezon City, Philippines' }}</p>
                        <p><strong>Clinic Hours:</strong> {{ $clinic?->open_time ?? '08:00:00' }} - {{ $clinic?->close_time ?? '17:00:00' }}</p>
                    </div>
                </div>

                <div style="background: #1f2937; border-radius: 8px; padding: 24px; border: 1px solid #374151;">
                    <h3 style="font-size: 24px; font-weight: bold; margin-bottom: 18px; color: #ffffff;">
                        Need an appointment?
                    </h3>

                    <p style="color: #d1d5db; line-height: 1.8; margin-bottom: 18px;">
                        Book your visit in a few steps and let the clinic review your preferred date,
                        service, and dentist.
                    </p>

                    <a href="{{ route('booking.entry') }}"
                       style="display: inline-block; padding: 12px 20px; background: #0f9d8a; color: white; border-radius: 6px; font-weight: bold; text-decoration: none;">
                        Book an Appointment
                    </a>
                </div>
            </div>
        </div>
    </section>

<!-- CHAT BUTTON -->
<div id="chatToggle" style="
    position:fixed;
    bottom:20px;
    right:20px;
    width:60px;
    height:60px;
    border-radius:50%;
    background:#0f9d8a;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    cursor:pointer;
    z-index:999;
">
    Chat
</div>

<!-- CHAT BOX -->
<div id="chatBox" style="
    position:fixed;
    bottom:90px;
    right:20px;
    width:320px;
    height:420px;
    background:#fff;
    border:1px solid #dbe2ea;
    border-radius:12px;
    display:none;
    flex-direction:column;
    overflow:hidden;
    z-index:999;
">

    <!-- HEADER -->
    <div style="padding:10px;background:#0f9d8a;color:#fff;font-weight:700;">
        Clinic Chat
    </div>

    <!-- MESSAGES -->
    <div id="chatMessages" style="flex:1;overflow-y:auto;padding:10px;background:#f8fafc;"></div>

    <!-- INPUT -->
    <form id="chatForm" style="display:flex;border-top:1px solid #e5e7eb;">
        @csrf
        <input id="chatInput" name="message_text" type="text"
            placeholder="Type message..."
            style="flex:1;padding:10px;border:none;outline:none;">
        <button type="submit"
            style="background:#0f9d8a;color:#fff;border:none;padding:0 14px;">
            Send
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const chatToggle = document.getElementById('chatToggle');
    const chatBox = document.getElementById('chatBox');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const messagesBox = document.getElementById('chatMessages');

    // Toggle chat
    chatToggle.addEventListener('click', function () {
        chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
    });

    const sendUrl = "{{ route('chat.guest.send') }}";
    const fetchUrl = "{{ route('chat.guest.fetch', session('guest_chat_conversation_id') ?? 0) }}";

    async function loadMessages() {
        try {
            const res = await fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) return;

            const data = await res.json();
            messagesBox.innerHTML = '';

            (data.messages || []).forEach(msg => {

                const isGuest = msg.sender_type === 'guest';

                messagesBox.innerHTML += `
                    <div style="display:flex;justify-content:${isGuest ? 'flex-end' : 'flex-start'};margin-bottom:8px;">
                        <div style="
                            max-width:70%;
                            padding:8px 10px;
                            border-radius:8px;
                            background:${isGuest ? '#0f9d8a' : '#fff'};
                            color:${isGuest ? '#fff' : '#000'};
                            border:1px solid #ddd;
                        ">
                            ${escapeHtml(msg.message_text)}
                        </div>
                    </div>
                `;
            });

            messagesBox.scrollTop = messagesBox.scrollHeight;

        } catch (e) {
            console.error(e);
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const text = input.value.trim();
        if (!text) return;

        const formData = new FormData();
        formData.append('message_text', text);

        try {
            await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });

            input.value = '';
            loadMessages();

        } catch (e) {
            console.error(e);
        }
    });

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[m]);
    }

    // auto refresh
    setInterval(loadMessages, 4000);
});
</script>
@endsection
