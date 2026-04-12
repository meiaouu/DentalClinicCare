@php
    $staffNavSections = [
        [
            'title' => 'Main',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => Route::has('staff.dashboard') ? route('staff.dashboard') : '#',
                    'active' => request()->routeIs('staff.dashboard'),
                    'enabled' => Route::has('staff.dashboard'),
                ],
                [
                    'label' => 'Appointment Requests',
                    'route' => Route::has('staff.appointment-requests.index') ? route('staff.appointment-requests.index') : '#',
                    'active' => request()->routeIs('staff.appointment-requests.*'),
                    'enabled' => Route::has('staff.appointment-requests.index'),
                ],
                [
                    'label' => 'Daily Appointments',
                    'route' => Route::has('staff.appointments.index') ? route('staff.appointments.index') : '#',
                    'active' => request()->routeIs('staff.appointments.*'),
                    'enabled' => Route::has('staff.appointments.index'),
                ],
                [
                    'label' => 'Clinic Schedule',
                    'route' => Route::has('staff.clinic-schedule.index') ? route('staff.clinic-schedule.index') : '#',
                    'active' => request()->routeIs('staff.clinic-schedule.*'),
                    'enabled' => Route::has('staff.clinic-schedule.index'),
                ],
            ],
        ],
        [
            'title' => 'Patients & Operations',
            'items' => [
                [
                    'label' => 'Queue / Arrivals',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Patients',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Guest Conversion',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Follow-Ups',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Reminders',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
            ],
        ],
        [
            'title' => 'Finance & Communication',
            'items' => [
                [
                    'label' => 'Billing / Payments',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Receipts',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Messages',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Attachments / X-Rays',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
            ],
        ],
        [
            'title' => 'Admin Support',
            'items' => [
                [
                    'label' => 'Audit Logs',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Reports',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
                [
                    'label' => 'Settings',
                    'route' => '#',
                    'active' => false,
                    'enabled' => false,
                ],
            ],
        ],
    ];
@endphp

<style>
    .staff-sidebar {
        background: #ffffff;
        border-right: 1px solid #edf2f7;
        padding: 26px 18px 22px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-height: 100%;
    }

    .staff-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px 18px;
        border-bottom: 1px solid #edf2f7;
    }

    .staff-sidebar-logo {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
    }

    .staff-sidebar-brand-text {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .staff-sidebar-brand-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .staff-sidebar-section {
        display: grid;
        gap: 10px;
    }

    .staff-sidebar-section-title {
        padding: 0 12px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .staff-sidebar-nav {
        display: grid;
        gap: 8px;
    }

    .staff-sidebar-link,
    .staff-sidebar-disabled {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: 0.18s ease;
    }

    .staff-sidebar-link {
        color: #334155;
        background: transparent;
    }

    .staff-sidebar-link:hover {
        background: #f8fafc;
        color: #2563eb;
    }

    .staff-sidebar-link.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
    }

    .staff-sidebar-disabled {
        color: #94a3b8;
        background: #f8fafc;
        border: 1px dashed #dbe2ea;
        cursor: not-allowed;
    }

    .staff-sidebar-icon {
        width: 18px;
        text-align: center;
        font-size: 13px;
        font-weight: 800;
    }

    .staff-sidebar-footer {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #edf2f7;
    }

    .staff-sidebar-logout {
        display: block;
        padding: 13px 14px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        color: #dc2626;
        background: #fff5f5;
        border: none;
        width: 100%;
        text-align: left;
    }
</style>

<aside class="staff-sidebar">
    <div class="staff-sidebar-brand">
        <div class="staff-sidebar-logo">Dr</div>
        <div>
            <div class="staff-sidebar-brand-text">Brendalyn
                Wansi
                Calacat</div>
            <div class="staff-sidebar-brand-sub">Staff Workspace</div>
        </div>
    </div>

    @foreach($staffNavSections as $section)
        <div class="staff-sidebar-section">
            <div class="staff-sidebar-section-title">{{ $section['title'] }}</div>

            <nav class="staff-sidebar-nav">
                @foreach($section['items'] as $item)
                    @if($item['enabled'])
                        <a href="{{ $item['route'] }}"
                           class="staff-sidebar-link {{ $item['active'] ? 'active' : '' }}">
                            <span class="staff-sidebar-icon">•</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <div class="staff-sidebar-disabled">
                            <span class="staff-sidebar-icon">•</span>
                            <span>{{ $item['label'] }}</span>
                        </div>
                    @endif
                @endforeach
            </nav>
        </div>
    @endforeach

    <div class="staff-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="staff-sidebar-logout">
                Log Out
            </button>
        </form>
    </div>
</aside>
