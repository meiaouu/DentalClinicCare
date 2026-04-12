@extends('layouts.app')

@section('content')
<style>
    .dentist-layout {
        min-height: 100vh;
        background: #f8fafc;
        display: flex;
    }

    .dentist-sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        padding: 24px 18px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 0;
        height: 100vh;
    }

    .dentist-brand {
        padding: 8px 10px 18px;
        border-bottom: 1px solid #eef2f7;
    }

    .dentist-brand h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }

    .dentist-brand p {
        margin: 6px 0 0;
        font-size: 13px;
        color: #64748b;
    }

    .dentist-nav-section {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .dentist-nav-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
        padding: 0 10px;
        margin-bottom: 6px;
    }

    .dentist-nav-link {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        border-radius: 12px;
        text-decoration: none;
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        transition: 0.2s ease;
    }

    .dentist-nav-link:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .dentist-nav-link.active {
        background: #0f766e;
        color: #ffffff;
    }

    .dentist-main {
        flex: 1;
        padding: 28px;
        min-width: 0;
    }

    .dentist-topbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .dentist-topbar h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .dentist-topbar p {
        margin: 6px 0 0;
        font-size: 14px;
        color: #64748b;
    }

    .dentist-topbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dentist-badge {
        padding: 8px 12px;
        border-radius: 999px;
        background: #ecfeff;
        color: #0f766e;
        font-size: 12px;
        font-weight: 800;
    }

    .dentist-logout-btn {
        border: 1px solid #dbe4ea;
        background: #ffffff;
        color: #334155;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
    }

    .dentist-page-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    @media (max-width: 991.98px) {
        .dentist-layout {
            flex-direction: column;
        }

        .dentist-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
        }

        .dentist-main {
            padding: 18px;
        }

        .dentist-topbar {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="dentist-layout">
    <aside class="dentist-sidebar">
        <div class="dentist-brand">
            <h2>Dental Clinic Care</h2>
            <p>Dentist Dashboard</p>
        </div>

        <div class="dentist-nav-section">
            <div class="dentist-nav-title">Main</div>
            <a href="{{ route('dentist.dashboard') }}"
               class="dentist-nav-link {{ request()->routeIs('dentist.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('dentist.availability.index') }}"
               class="dentist-nav-link {{ request()->routeIs('dentist.availability.*') || request()->routeIs('dentist.unavailable-dates.*') ? 'active' : '' }}">
                Availability
            </a>

            @if(Route::has('dentist.schedule.index'))
                <a href="{{ route('dentist.schedule.index') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.schedule.*') ? 'active' : '' }}">
                    My Schedule
                </a>
            @endif
        </div>

        <div class="dentist-nav-section">
            <div class="dentist-nav-title">Patients</div>

            @if(Route::has('dentist.patients.today'))
                <a href="{{ route('dentist.patients.today') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.patients.today') ? 'active' : '' }}">
                    Today’s Patients
                </a>
            @endif

            @if(Route::has('dentist.patients.charts'))
                <a href="{{ route('dentist.patients.charts') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.patients.charts') ? 'active' : '' }}">
                    Patient Charts
                </a>
            @endif
        </div>

        <div class="dentist-nav-section">
            <div class="dentist-nav-title">Records</div>

            @if(Route::has('dentist.treatments.index'))
                <a href="{{ route('dentist.treatments.index') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.treatments.*') ? 'active' : '' }}">
                    Treatment Records
                </a>
            @endif

            @if(Route::has('dentist.attachments.index'))
                <a href="{{ route('dentist.attachments.index') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.attachments.*') ? 'active' : '' }}">
                    Attachments / X-rays
                </a>
            @endif

            @if(Route::has('dentist.followups.index'))
                <a href="{{ route('dentist.followups.index') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.followups.*') ? 'active' : '' }}">
                    Follow-Ups
                </a>
            @endif
        </div>

        <div class="dentist-nav-section">
            <div class="dentist-nav-title">Account</div>

            @if(Route::has('dentist.profile.edit'))
                <a href="{{ route('dentist.profile.edit') }}"
                   class="dentist-nav-link {{ request()->routeIs('dentist.profile.*') ? 'active' : '' }}">
                    Profile
                </a>
            @endif
        </div>
    </aside>

    <main class="dentist-main">
        <div class="dentist-topbar">
            <div>
                <h1>@yield('page_title', 'Dentist Dashboard')</h1>
                <p>
                    Welcome, Dr.
                    {{ auth()->user()->first_name ?? '' }}
                    {{ auth()->user()->last_name ?? '' }}
                </p>
            </div>

            <div class="dentist-topbar-right">
                <span class="dentist-badge">Dentist Panel</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dentist-logout-btn">Logout</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="dentist-page-card">
            @yield('dentist_content')
        </div>
    </main>
</div>
@endsection
