@php
    $dentistName = trim(
        collect([
            auth()->user()->first_name ?? '',
            auth()->user()->middle_name ?? '',
            auth()->user()->last_name ?? '',
        ])->filter()->implode(' ')
    );

    $dentistNavSections = [
        [
            'title' => 'Main',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => Route::has('dentist.dashboard') ? route('dentist.dashboard') : '#',
                    'active' => request()->routeIs('dentist.dashboard'),
                    'enabled' => Route::has('dentist.dashboard'),
                ],
                [
                    'label' => 'Availability',
                    'route' => Route::has('dentist.availability.index') ? route('dentist.availability.index') : '#',
                    'active' => request()->routeIs('dentist.availability.*') || request()->routeIs('dentist.unavailable-dates.*'),
                    'enabled' => Route::has('dentist.availability.index'),
                ],
                [
                    'label' => 'My Schedule',
                    'route' => Route::has('dentist.schedule.index') ? route('dentist.schedule.index') : '#',
                    'active' => request()->routeIs('dentist.schedule.*'),
                    'enabled' => Route::has('dentist.schedule.index'),
                ],
            ],
        ],
        [
            'title' => 'Patients',
            'items' => [
                [
                    'label' => 'Today’s Patients',
                    'route' => Route::has('dentist.patients.today') ? route('dentist.patients.today') : '#',
                    'active' => request()->routeIs('dentist.patients.today'),
                    'enabled' => Route::has('dentist.patients.today'),
                ],
                [
                    'label' => 'Patient Charts',
                    'route' => Route::has('dentist.patients.charts') ? route('dentist.patients.charts') : '#',
                    'active' => request()->routeIs('dentist.patients.charts') || request()->routeIs('dentist.patients.show'),
                    'enabled' => Route::has('dentist.patients.charts'),
                ],
            ],
        ],
        [
            'title' => 'Clinical Records',
            'items' => [
                [
                    'label' => 'Treatment Records',
                    'route' => Route::has('dentist.treatments.index') ? route('dentist.treatments.index') : '#',
                    'active' => request()->routeIs('dentist.treatments.*'),
                    'enabled' => Route::has('dentist.treatments.index'),
                ],
                [
                    'label' => 'Attachments / X-Rays',
                    'route' => Route::has('dentist.attachments.index') ? route('dentist.attachments.index') : '#',
                    'active' => request()->routeIs('dentist.attachments.*'),
                    'enabled' => Route::has('dentist.attachments.index'),
                ],
                [
                    'label' => 'Follow-Ups',
                    'route' => Route::has('dentist.followups.index') ? route('dentist.followups.index') : '#',
                    'active' => request()->routeIs('dentist.followups.*'),
                    'enabled' => Route::has('dentist.followups.index'),
                ],
            ],
        ],
        [
            'title' => 'Account',
            'items' => [
                [
                    'label' => 'Profile',
                    'route' => Route::has('dentist.profile.edit') ? route('dentist.profile.edit') : '#',
                    'active' => request()->routeIs('dentist.profile.*'),
                    'enabled' => Route::has('dentist.profile.edit'),
                ],
            ],
        ],
    ];
@endphp

<style>
    .dentist-sidebar {
        background: #ffffff;
        border-right: 1px solid #edf2f7;
        padding: 26px 18px 22px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-height: 100%;
        box-sizing: border-box;
    }

    .dentist-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px 18px;
        border-bottom: 1px solid #edf2f7;
    }

    .dentist-sidebar-logo {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #0f766e, #0d9488);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        flex-shrink: 0;
    }

    .dentist-sidebar-brand-text {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .dentist-sidebar-brand-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .dentist-sidebar-section {
        display: grid;
        gap: 10px;
    }

    .dentist-sidebar-section-title {
        padding: 0 12px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .dentist-sidebar-nav {
        display: grid;
        gap: 8px;
    }

    .dentist-sidebar-link,
    .dentist-sidebar-disabled {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: 0.18s ease;
        box-sizing: border-box;
    }

    .dentist-sidebar-link {
        color: #334155;
        background: transparent;
    }

    .dentist-sidebar-link:hover {
        background: #f8fafc;
        color: #0f766e;
    }

    .dentist-sidebar-link.active {
        background: linear-gradient(135deg, #0f766e, #0d9488);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(13, 148, 136, 0.20);
    }

    .dentist-sidebar-disabled {
        color: #94a3b8;
        background: #f8fafc;
        border: 1px dashed #dbe2ea;
        cursor: not-allowed;
    }

    .dentist-sidebar-icon {
        width: 18px;
        text-align: center;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .dentist-sidebar-footer {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #edf2f7;
    }

    .dentist-sidebar-logout {
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
        cursor: pointer;
    }

    .dentist-sidebar-logout:hover {
        background: #fee2e2;
    }
</style>

<aside class="dentist-sidebar">
    <div class="dentist-sidebar-brand">
        <div class="dentist-sidebar-logo">Dr</div>
        <div>
            <div class="dentist-sidebar-brand-text">
                {{ $dentistName !== '' ? $dentistName : 'Dentist User' }}
            </div>
            <div class="dentist-sidebar-brand-sub">Dentist Workspace</div>
        </div>
    </div>

    @foreach($dentistNavSections as $section)
        <div class="dentist-sidebar-section">
            <div class="dentist-sidebar-section-title">{{ $section['title'] }}</div>

            <nav class="dentist-sidebar-nav">
                @foreach($section['items'] as $item)
                    @if($item['enabled'])
                        <a href="{{ $item['route'] }}"
                           class="dentist-sidebar-link {{ $item['active'] ? 'active' : '' }}">
                            <span class="dentist-sidebar-icon">•</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <div class="dentist-sidebar-disabled">
                            <span class="dentist-sidebar-icon">•</span>
                            <span>{{ $item['label'] }}</span>
                        </div>
                    @endif
                @endforeach
            </nav>
        </div>
    @endforeach

    <div class="dentist-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dentist-sidebar-logout">
                Log Out
            </button>
        </form>
    </div>
</aside>
