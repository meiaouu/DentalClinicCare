<aside class="sidebar">
    <div class="brand">Dentist Dashboard</div>

    <a href="{{ route('dentist.dashboard') }}"
       class="nav-link {{ request()->routeIs('dentist.dashboard') ? 'active' : '' }}">
        Dashboard
    </a>

    <a href="{{ route('dentist.availability.index') }}"
       class="nav-link {{ request()->routeIs('dentist.availability.*') ? 'active' : '' }}">
        My Availability
    </a>
</aside>
