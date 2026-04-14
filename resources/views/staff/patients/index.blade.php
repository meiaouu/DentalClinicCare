@extends('staff.layouts.app')

@section('content')
<style>
    .patients-page {
        display: grid;
        gap: 18px;
    }

    .patients-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
    }

    .patients-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .patients-header p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
    }

    .btn-create {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        background: #0f9d8a;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
    }

    .alert-success {
        background: #ecfdf5;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 14px 16px;
        border-radius: 14px;
    }

    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
    }

    .form-input,
    .form-select {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 14px;
        background: #fff;
        color: #0f172a;
        box-sizing: border-box;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #0f9d8a;
        box-shadow: 0 0 0 3px rgba(15, 157, 138, 0.10);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-filter,
    .btn-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid #dbe4ea;
    }

    .btn-filter {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #fff;
    }

    .btn-reset {
        background: #fff;
        color: #334155;
    }

    .patients-list {
        display: grid;
        gap: 12px;
    }

    .patient-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
    }

    .patient-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .patient-name {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .patient-code {
        font-size: 12px;
        color: #64748b;
    }

    .patient-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        background: #ecfdf5;
        color: #166534;
    }

    .patient-meta {
        font-size: 13px;
        color: #475569;
        line-height: 1.7;
    }

    .patient-summary {
        margin-top: 12px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .summary-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
    }

    .summary-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .summary-value {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .patient-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .btn-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #dbe4ea;
        background: #fff;
        color: #334155;
    }

    .btn-link.primary {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .empty-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        color: #64748b;
    }

    @media (max-width: 900px) {
        .filter-form {
            grid-template-columns: 1fr;
        }

        .patient-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .patient-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="patients-page">
    <div class="patients-header">
        <div>
            <h1>Patients</h1>
            <p>Search, sort, and review patient appointment history.</p>
        </div>

        <a href="{{ route('staff.patients.create') }}" class="btn-create">Create Patient</a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('staff.patients.index') }}" class="filter-form" id="patientFilterForm">

    <input
        type="text"
        name="search"
        id="searchInput"
        class="form-input"
        placeholder="Search patient..."
        value="{{ request('search') }}"
    >

    <select name="status" class="form-select auto-submit">
        <option value="">All Statuses</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
    </select>

    <select name="sort" class="form-select auto-submit">
        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
        <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
        <option value="name_asc" @selected(request('sort') === 'name_asc')>Name A-Z</option>
        <option value="name_desc" @selected(request('sort') === 'name_desc')>Name Z-A</option>
    </select>

    <a href="{{ route('staff.patients.index') }}" class="btn-reset">Reset</a>
</form>
    </div>

    <div class="patients-list">
        @forelse($patients as $patient)
            @php
                $summary = $patient->appointment_status_summary ?? [
                    'total_times_set_appointment' => 0,
                    'total_actual_appointments' => 0,
                    'statuses' => [
                        'completed' => 0,
                        'cancelled' => 0,
                        'no_show' => 0,
                    ],
                ];
            @endphp

            <div class="patient-card">
    <div class="patient-top">
        <div>
            <div class="patient-name">
                {{ trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')) }}
            </div>
            <div class="patient-code">
                {{ $patient->patient_code ?? '—' }}
            </div>
        </div>

        <span class="patient-status-badge">
            {{ strtoupper($patient->profile_status ?? 'active') }}
        </span>
    </div>

    <div class="patient-meta">
        {{ $patient->contact_number ?? '—' }} • {{ $patient->email ?? '—' }}
    </div>

    <div style="margin-top:8px; font-size:13px; color:#64748b;">
        Visits: {{ $patient->appointments_count ?? 0 }} |
        Completed: {{ $summary['statuses']['completed'] ?? 0 }} |
        No-show: {{ $summary['statuses']['no_show'] ?? 0 }}
    </div>

    <div class="patient-actions">
        @if(\Illuminate\Support\Facades\Route::has('staff.patients.show'))
            <a href="{{ route('staff.patients.show', $patient->patient_id) }}" class="btn-link primary">
                View
            </a>
        @endif
    </div>
</div>
        @empty
            <div class="empty-card">
                No patient records found.
            </div>
        @endforelse
    </div>

    <div>
        {{ $patients->withQueryString()->links() }}
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('patientFilterForm');
    const searchInput = document.getElementById('searchInput');
    const autoSelects = document.querySelectorAll('.auto-submit');

    let debounceTimer;

    // Debounced search (typing)
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            form.submit();
        }, 500); // 500ms delay
    });

    // Instant submit for dropdowns
    autoSelects.forEach(select => {
        select.addEventListener('change', function () {
            form.submit();
        });
    });
});
</script>
@endsection
