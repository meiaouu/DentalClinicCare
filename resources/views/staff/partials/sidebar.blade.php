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
                    'label' => 'Appointments',
                    'route' => Route::has('staff.appointments.index') ? route('staff.appointments.index') : '#',
                    'active' => request()->routeIs('staff.appointments.*'),
                    'enabled' => Route::has('staff.appointments.index'),
                ],

            ],
        ],
        [
            'title' => 'Patient',
            'items' => [
    [
        'label' => 'Patients',
        'route' => Route::has('staff.patients.index') ? route('staff.patients.index') : '#',
        'active' => request()->routeIs('staff.patients.*'),
        'enabled' => Route::has('staff.patients.index'),
    ],
    [
        'label' => 'Messages',
        'route' => Route::has('staff.messages.index') ? route('staff.messages.index') : '#',
        'active' => request()->routeIs('staff.messages.*'),
        'enabled' => Route::has('staff.messages.index'),
    ],

            ],
        ],
        [
            'title' => 'Finance',
            'items' => [
                [
                    'label' => 'Billing',
                    'route' => Route::has('staff.billing.index') ? route('staff.billing.index') : '#',
                    'active' => request()->routeIs('staff.billing.*'),
                    'enabled' => Route::has('staff.billing.index'),
                ],
            ],
        ],
        [
            'title' => 'Account',
            'items' => [
                [
                    'label' => 'Profile',
                    'route' => Route::has('staff.profile.edit') ? route('staff.profile.edit') : '#',
                    'active' => request()->routeIs('staff.profile.*'),
                    'enabled' => Route::has('staff.profile.edit'),
                ],
            ],
        ],
    ];
@endphp

<style>
    .staff-sidebar {
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 22px 16px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
    }

    .staff-brand {
        padding: 8px 10px 18px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 18px;
    }

    .staff-brand-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .staff-brand-subtitle {
        margin-top: 4px;
        font-size: 13px;
        color: #64748b;
    }

    .staff-section {
        margin-bottom: 18px;
    }

    .staff-section-title {
        margin: 0 0 8px;
        padding: 0 10px;
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .staff-nav {
        display: grid;
        gap: 8px;
    }

    .staff-nav-link,
    .staff-nav-disabled {
        display: block;
        padding: 12px 14px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .staff-nav-link {
        color: #334155;
        background: transparent;
        transition: 0.15s ease;
    }

    .staff-nav-link:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .staff-nav-link.active {
        background: #0f9d8a;
        color: #ffffff;
    }

    .staff-nav-disabled {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px dashed #e2e8f0;
        cursor: not-allowed;
    }

    .staff-sidebar-footer {
        margin-top: auto;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .staff-logout-btn {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 12px 14px;
        background: #fef2f2;
        color: #dc2626;
        font-size: 14px;
        font-weight: 700;
        text-align: left;
        cursor: pointer;
    }
</style>

<aside class="staff-sidebar">
    <div class="staff-brand">
        <div class="staff-brand-title">Staff Panel</div>
        <div class="staff-brand-subtitle">Dental Clinic System</div>
    </div>

    @foreach($staffNavSections as $section)
        <div class="staff-section">
            <div class="staff-section-title">{{ $section['title'] }}</div>

            <nav class="staff-nav">
                @foreach($section['items'] as $item)
                    @if($item['enabled'])
                        <a href="{{ $item['route'] }}" class="staff-nav-link {{ $item['active'] ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <div class="staff-nav-disabled">
                            {{ $item['label'] }}
                        </div>
                    @endif
                @endforeach
            </nav>
        </div>
    @endforeach

    <div class="staff-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="staff-logout-btn">Logout</button>
        </form>
    </div>
</aside>
