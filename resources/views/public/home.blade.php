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

<style>
    .clinic-chat-toggle {
        position: fixed;
        right: 22px;
        bottom: 22px;
        width: 74px;
        height: 74px;
        border-radius: 999px;
        border: none;
        background: #14b8a6;
        color: #ffffff;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
        z-index: 9999;
    }

    .clinic-chat-widget {
        position: fixed;
        right: 22px;
        bottom: 108px;
        width: 420px;
        max-width: calc(100vw - 24px);
        height: 560px;
        background: #ffffff;
        border: 1px solid #dbe2ea;
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
        overflow: hidden;
        display: none;
        flex-direction: column;
        z-index: 9998;
    }

    .clinic-chat-header {
        background: #14b8a6;
        color: #ffffff;
        padding: 16px 18px;
        font-size: 17px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .clinic-chat-close {
        border: none;
        background: transparent;
        color: #ffffff;
        font-size: 24px;
        cursor: pointer;
        line-height: 1;
    }

    .clinic-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        background: #f8fafc;
    }

    .clinic-chat-row {
        display: flex;
        margin-bottom: 10px;
    }

    .clinic-chat-row.guest {
        justify-content: flex-end;
    }

    .clinic-chat-row.bot {
        justify-content: flex-start;
    }

    .clinic-chat-bubble {
        max-width: 78%;
        padding: 10px 12px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.5;
    }

    .clinic-chat-bubble.guest {
        background: #14b8a6;
        color: #ffffff;
        border: 1px solid #14b8a6;
    }

    .clinic-chat-bubble.bot {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #dbe2ea;
    }

    .clinic-chat-label {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 4px;
        opacity: 0.85;
    }

    .clinic-chat-time {
        font-size: 11px;
        margin-top: 6px;
        opacity: 0.75;
    }

    .clinic-chat-form {
        border-top: 1px solid #e5e7eb;
        padding: 12px;
        display: flex;
        gap: 10px;
        background: #ffffff;
    }

    .clinic-chat-input {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 14px;
        outline: none;
    }

    .clinic-chat-send {
        border: none;
        background: #14b8a6;
        color: #ffffff;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .clinic-chat-empty {
        color: #64748b;
        font-size: 13px;
        padding: 10px 4px;
    }

    @media (max-width: 640px) {
        .clinic-chat-widget {
            right: 12px;
            left: 12px;
            width: auto;
            height: 70vh;
            bottom: 96px;
        }

        .clinic-chat-toggle {
            right: 16px;
            bottom: 16px;
            width: 68px;
            height: 68px;
        }
    }
</style>

<button
    type="button"
    id="clinicChatToggle"
    class="clinic-chat-toggle"
    data-start-url="{{ route('chat.widget.start') }}"
    data-send-url="{{ route('chat.widget.send') }}"
    data-fetch-url="{{ route('chat.widget.fetch') }}"
>
    Chat
</button>

<div id="clinicChatWidget" class="clinic-chat-widget">
    <div class="clinic-chat-header">
        <span>Clinic Chat</span>
        <button type="button" id="clinicChatClose" class="clinic-chat-close">×</button>
    </div>

    <div id="clinicChatMessages" class="clinic-chat-messages">
        <div class="clinic-chat-empty">Start chatting with the clinic bot.</div>
    </div>

    <form id="clinicChatForm" class="clinic-chat-form">
        @csrf
        <input
            type="text"
            id="clinicChatInput"
            class="clinic-chat-input"
            name="message_text"
            placeholder="Type message..."
            autocomplete="off"
            required
        >
        <button type="submit" class="clinic-chat-send">Send</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('clinicChatToggle');
    const widget = document.getElementById('clinicChatWidget');
    const closeBtn = document.getElementById('clinicChatClose');
    const form = document.getElementById('clinicChatForm');
    const input = document.getElementById('clinicChatInput');
    const messagesBox = document.getElementById('clinicChatMessages');

    const startUrl = toggleBtn.dataset.startUrl;
    const sendUrl = toggleBtn.dataset.sendUrl;
    const fetchUrl = toggleBtn.dataset.fetchUrl;
    const csrfToken = document.querySelector('#clinicChatForm input[name="_token"]').value;

    let isStarted = false;
    let isLoading = false;

    toggleBtn.addEventListener('click', async function () {
        widget.style.display = widget.style.display === 'flex' ? 'none' : 'flex';

        if (widget.style.display === 'flex' && !isStarted) {
            await startChat();
            await loadMessages();
        }
    });

    closeBtn.addEventListener('click', function () {
        widget.style.display = 'none';
    });

    async function startChat() {
        if (isStarted) {
            return;
        }

        try {
            const response = await fetch(startUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                messagesBox.innerHTML = '<div class="clinic-chat-empty">Chat is ready. You can start messaging.</div>';
                isStarted = true;
                return;
            }

            isStarted = true;
        } catch (error) {
            console.error('Failed to start chat:', error);
            messagesBox.innerHTML = '<div class="clinic-chat-empty">Chat is ready. You can start messaging.</div>';
            isStarted = true;
        }
    }

    async function loadMessages() {
        if (isLoading) {
            return;
        }

        isLoading = true;

        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            renderMessages(data.messages || []);
        } catch (error) {
            console.error('Failed to load messages:', error);
        } finally {
            isLoading = false;
        }
    }

    function renderMessages(messages) {
        messagesBox.innerHTML = '';

        if (!messages.length) {
            messagesBox.innerHTML = '<div class="clinic-chat-empty">Start chatting with the clinic bot.</div>';
            return;
        }

        messages.forEach(function (message) {
            appendMessage(
                message.sender_type,
                message.message_text || message.message_body || '',
                message.sent_at
            );
        });

        scrollMessagesToBottom();
    }

    function appendMessage(senderType, text, sentAt) {
        const row = document.createElement('div');
        const bubble = document.createElement('div');
        const label = document.createElement('div');
        const body = document.createElement('div');
        const time = document.createElement('div');

        const isGuest = senderType === 'guest';

        row.className = 'clinic-chat-row ' + (isGuest ? 'guest' : 'bot');
        bubble.className = 'clinic-chat-bubble ' + (isGuest ? 'guest' : 'bot');
        label.className = 'clinic-chat-label';
        body.className = 'clinic-chat-body';
        time.className = 'clinic-chat-time';

        label.textContent = isGuest ? 'You' : 'Clinic Bot';
        body.textContent = text;
        time.textContent = formatDate(sentAt);

        bubble.appendChild(label);
        bubble.appendChild(body);
        bubble.appendChild(time);
        row.appendChild(bubble);
        messagesBox.appendChild(row);
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const text = input.value.trim();

        if (!text) {
            return;
        }

        if (!isStarted) {
            await startChat();
        }

        appendMessage('guest', text, new Date().toISOString());
        scrollMessagesToBottom();

        input.value = '';
        input.disabled = true;

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message_text: text
                })
            });

            if (!response.ok) {
                appendMessage('bot', 'Thank you for your message. Our clinic chatbot is temporarily limited, but your message has been received.', new Date().toISOString());
                scrollMessagesToBottom();
                return;
            }

            const data = await response.json();

            if (data.bot_message) {
                appendMessage(
                    'bot',
                    data.bot_message.message_text || data.bot_message.message_body || '',
                    data.bot_message.sent_at
                );
            } else {
                await loadMessages();
            }

            scrollMessagesToBottom();
        } catch (error) {
            console.error('Failed to send message:', error);
            appendMessage('bot', 'Thank you for your message. Our clinic chatbot is temporarily limited, but your message has been received.', new Date().toISOString());
            scrollMessagesToBottom();
        } finally {
            input.disabled = false;
            input.focus();
        }
    });

    function formatDate(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value);

        if (isNaN(date.getTime())) {
            return '';
        }

        return date.toLocaleString();
    }

    function scrollMessagesToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    setInterval(function () {
        if (widget.style.display === 'flex' && isStarted) {
            loadMessages();
        }
    }, 2000);
});
</script>
@endsection
