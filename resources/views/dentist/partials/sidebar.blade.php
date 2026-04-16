@php
    $route = request()->route()?->getName();
@endphp

<style>
    .sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        min-height: 100vh;
        padding: 20px 14px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 18px;
    }

    .sidebar-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }

    .sidebar-subtitle {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }

    .sidebar-section {
        margin-top: 10px;
    }

    .sidebar-label {
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        margin-bottom: 10px;
        padding-left: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .sidebar-link {
        display: block;
        padding: 11px 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        text-decoration: none;
        margin-bottom: 8px;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .sidebar-link:hover {
        background: #f8fafc;
    }

    .sidebar-link.active {
        background: #14b8a6;
        color: #ffffff;
    }

    .sidebar-bottom {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
    }

    .logout-btn {
        width: 100%;
        padding: 11px 14px;
        border-radius: 14px;
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #be123c;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-align: left;
    }

    .logout-btn:hover {
        background: #ffe4e6;
    }
</style>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2 class="sidebar-title">Dentist Panel</h2>
        <p class="sidebar-subtitle">Dental Clinic System</p>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-label">Main</div>

        <a href="{{ route('dentist.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dentist.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('dentist.schedule.index') }}"
           class="sidebar-link {{ request()->routeIs('dentist.schedule.*') ? 'active' : '' }}">
            Schedule
        </a>

        <a href="{{ route('dentist.availability.index') }}"
           class="sidebar-link {{ request()->routeIs('dentist.availability.*') ? 'active' : '' }}">
            Availability
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-label">Patient</div>

        <a href="{{ route('dentist.patients.today') }}"
           class="sidebar-link {{ request()->routeIs('dentist.patients.today') ? 'active' : '' }}">
            Today’s Patients
        </a>

        <a href="{{ route('dentist.patients.charts') }}"
           class="sidebar-link {{ request()->routeIs('dentist.patients.charts') ? 'active' : '' }}">
            Patient Records
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-label">Records</div>

        <a href="{{ route('dentist.treatments.index') }}"
           class="sidebar-link {{ request()->routeIs('dentist.treatments.*') ? 'active' : '' }}">
            Treatments
        </a>

        <a href="{{ route('dentist.followups.index') }}"
           class="sidebar-link {{ request()->routeIs('dentist.followups.*') ? 'active' : '' }}">
            Follow-ups
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-label">Account</div>

        <a href="{{ route('dentist.profile.edit') }}"
           class="sidebar-link {{ request()->routeIs('dentist.profile.*') ? 'active' : '' }}">
            Profile
        </a>
    </div>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                Logout
            </button>
        </form>
    </div>
</aside>
