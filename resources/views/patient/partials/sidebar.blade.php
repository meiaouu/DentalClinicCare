@php
    $currentRoute = request()->route()?->getName();
@endphp

<style>
    .patient-sidebar {
        width: 260px;
        min-height: 100vh;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        padding: 22px 14px;
        box-sizing: border-box;
    }

    .patient-sidebar-brand {
        padding: 4px 10px 18px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 18px;
    }

    .patient-sidebar-brand h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .patient-sidebar-brand p {
        margin: 4px 0 0;
        font-size: 13px;
        color: #64748b;
    }

    .patient-sidebar-section {
        margin-bottom: 20px;
    }

    .patient-sidebar-label {
        padding: 0 10px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .patient-sidebar-nav {
        display: grid;
        gap: 8px;
    }

    .patient-sidebar-link,
    .patient-sidebar-disabled,
    .patient-sidebar-button {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 44px;
        padding: 0 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        box-sizing: border-box;
    }

    .patient-sidebar-link {
        text-decoration: none;
        color: #334155;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: 0.2s ease;
    }

    .patient-sidebar-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .patient-sidebar-link.active {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #ffffff;
    }

    .patient-sidebar-disabled {
        color: #94a3b8;
        background: #f8fafc;
        border: 1px dashed #dbe2ea;
        cursor: not-allowed;
    }

    .patient-sidebar-form {
        margin: 0;
    }

    .patient-sidebar-button {
        border: 1px solid #fecaca;
        background: #fff5f5;
        color: #b91c1c;
        cursor: pointer;
        text-align: left;
    }

    .patient-sidebar-button:hover {
        background: #fef2f2;
    }

    @media (max-width: 991px) {
        .patient-sidebar {
            width: 100%;
            min-height: auto;
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
        }
    }
</style>

<aside class="patient-sidebar">
    <div class="patient-sidebar-brand">
        <h2>Patient Panel</h2>
        <p>Dental Clinic System</p>
    </div>

    <div class="patient-sidebar-section">
        <div class="patient-sidebar-label">Main</div>
        <div class="patient-sidebar-nav">
            <a href="{{ route('patient.dashboard') }}"
               class="patient-sidebar-link {{ $currentRoute === 'patient.dashboard' ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('booking.create') }}"
               class="patient-sidebar-link {{ $currentRoute === 'booking.create' ? 'active' : '' }}">
                Book Appointment
            </a>
        </div>
    </div>

    <div class="patient-sidebar-section">
        <div class="patient-sidebar-label">Appointments</div>
        <div class="patient-sidebar-nav">
            <div class="patient-sidebar-disabled">
                My Appointments
            </div>

            <div class="patient-sidebar-disabled">
                Appointment Requests
            </div>

            <div class="patient-sidebar-disabled">
                Follow-Ups
            </div>
        </div>
    </div>

    <div class="patient-sidebar-section">
        <div class="patient-sidebar-label">Account</div>
        <div class="patient-sidebar-nav">
            <a href="{{ route('messages.patient.form') }}"
               class="patient-sidebar-link {{ $currentRoute === 'messages.patient.form' ? 'active' : '' }}">
                Messages
            </a>

            <div class="patient-sidebar-disabled">
                Profile
            </div>

            <form method="POST" action="{{ route('logout') }}" class="patient-sidebar-form">
                @csrf
                <button type="submit" class="patient-sidebar-button">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
