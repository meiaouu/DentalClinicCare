@extends('staff.layouts.app')

@section('content')
<div style="padding:32px; max-width:1100px; margin:0 auto;">

    <div style="margin-bottom:24px;">
        <h1 style="margin:0 0 6px; font-size:28px; font-weight:800; color:#0f172a;">
            Appointment Request Queue
        </h1>
        <p style="margin:0; color:#64748b; font-size:14px;">
            Review and manage incoming appointment requests
        </p>
    </div>

    @if(session('success'))
        <div style="
            margin-bottom:20px;
            padding:14px 16px;
            border-radius:12px;
            background:#ecfdf5;
            color:#166534;
            border:1px solid #bbf7d0;
            font-weight:600;
        ">
            {{ session('success') }}
        </div>
    @endif

    <form id="filterForm" method="GET" style="
        display:flex;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:20px;
        padding:14px;
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:14px;
    ">
        <select
            name="sort"
            onchange="document.getElementById('filterForm').submit()"
            style="
                height:42px;
                padding:0 12px;
                border:1px solid #cbd5e1;
                border-radius:10px;
                background:#ffffff;
                color:#0f172a;
                font-size:14px;
            "
        >
            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest to Oldest</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest to Newest</option>
        </select>

        <select
            name="service_id"
            onchange="document.getElementById('filterForm').submit()"
            style="
                height:42px;
                padding:0 12px;
                border:1px solid #cbd5e1;
                border-radius:10px;
                background:#ffffff;
                color:#0f172a;
                font-size:14px;
            "
        >
            <option value="">All Services</option>
            @foreach($services as $service)
                <option value="{{ $service->service_id }}" {{ request('service_id') == $service->service_id ? 'selected' : '' }}>
                    {{ $service->service_name }}
                </option>
            @endforeach
        </select>

        <a
            href="{{ route('staff.appointment-requests.index') }}"
            style="
                height:42px;
                display:inline-flex;
                align-items:center;
                padding:0 14px;
                border-radius:10px;
                background:#f1f5f9;
                color:#334155;
                text-decoration:none;
                font-size:14px;
                font-weight:600;
            "
        >
            Reset
        </a>
    </form>

    <div style="display:grid; gap:12px;">
        @forelse($requests as $request)
            @php
                $patientName = $request->patient
                    ? trim(($request->patient->first_name ?? '') . ' ' . ($request->patient->last_name ?? ''))
                    : trim(($request->guest_first_name ?? '') . ' ' . ($request->guest_last_name ?? ''));

                $status = strtolower((string) $request->request_status);

                $statusStyle = match($status) {
                    'pending' => 'background:#fef3c7;color:#92400e;border:1px solid #fde68a;',
                    'confirmed' => 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;',
                    'rejected' => 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;',
                    'rescheduled' => 'background:#e0e7ff;color:#4338ca;border:1px solid #c7d2fe;',
                    'under_review' => 'background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;',
                    default => 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;',
                };
            @endphp

            <div style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:16px;
                padding:16px 18px;
                background:#ffffff;
                border:1px solid #e2e8f0;
                border-radius:14px;
            ">

                <div style="
                    width:42px;
                    height:42px;
                    border-radius:50%;
                    background:#ecfdf5;
                    color:#0f9d8a;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:16px;
                    flex-shrink:0;
                ">
                    👤
                </div>

                <div style="flex:1; min-width:0;">
                    <div style="
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        gap:12px;
                        flex-wrap:wrap;
                        margin-bottom:6px;
                    ">
                        <div style="font-size:16px; font-weight:700; color:#0f172a;">
                            {{ $patientName ?: 'Unknown Patient' }}
                        </div>

                        @php
    $statusClass = match($status) {
        'pending' => 'bg-yellow',
        'confirmed' => 'bg-green',
        'rejected' => 'bg-red',
        default => 'bg-gray'
    };
@endphp

<span class="{{ $statusClass }}">
    {{ strtoupper($status) }}
</span>
                    </div>

                    <div style="
                        display:flex;
                        gap:14px;
                        flex-wrap:wrap;
                        margin-bottom:8px;
                        font-size:13px;
                        color:#64748b;
                    ">
                        <span>Code: <strong style="color:#334155;">{{ $request->request_code }}</strong></span>
                        <span>
                            Submitted:
                            <strong
                                class="live-request-time"
                                data-request-time="{{ optional($request->created_at)->timezone('Asia/Manila')->format('Y-m-d H:i:s') }}"
                                style="color:#334155;"
                            >
                                {{ optional($request->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                            </strong>
                        </span>
                    </div>

                    <div style="
                        display:flex;
                        gap:24px;
                        flex-wrap:wrap;
                        font-size:14px;
                        color:#334155;
                    ">
                        <div>
                            <div style="font-size:12px; color:#64748b; margin-bottom:2px;">Service</div>
                            <div style="font-weight:600;">{{ $request->service?->service_name ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <div style="font-size:12px; color:#64748b; margin-bottom:2px;">Date</div>
                            <div style="font-weight:600;">
                                {{ $request->preferred_date ? \Carbon\Carbon::parse($request->preferred_date)->timezone('Asia/Manila')->format('M d, Y') : '—' }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size:12px; color:#64748b; margin-bottom:2px;">Time</div>
                            <div style="font-weight:600;">
                                {{ $request->preferred_start_time ? \Carbon\Carbon::parse($request->preferred_start_time)->timezone('Asia/Manila')->format('h:i A') : '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="flex-shrink:0;">
                    <a
                        href="{{ route('staff.appointment-requests.show', $request->request_id) }}"
                        style="
                            display:inline-flex;
                            align-items:center;
                            justify-content:center;
                            min-width:110px;
                            height:40px;
                            padding:0 16px;
                            background:#2563eb;
                            color:#ffffff;
                            border-radius:999px;
                            text-decoration:none;
                            font-size:14px;
                            font-weight:700;
                        "
                    >
                        Review
                    </a>
                </div>
            </div>
        @empty
            <div style="
                text-align:center;
                padding:40px 20px;
                background:#ffffff;
                border:1px solid #e2e8f0;
                border-radius:14px;
                color:#64748b;
            ">
                No appointment requests found.
            </div>
        @endforelse
    </div>

    <div style="margin-top:20px;">
        {{ $requests->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const timeElements = document.querySelectorAll('.live-request-time');

    function formatTimeAgo(dateString) {
        const created = new Date(dateString.replace(' ', 'T'));
        const now = new Date();

        const diffMs = now - created;
        const diffSeconds = Math.floor(diffMs / 1000);
        const diffMinutes = Math.floor(diffSeconds / 60);
        const diffHours = Math.floor(diffMinutes / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffSeconds < 60) {
            return 'Just now';
        }

        if (diffMinutes < 60) {
            return diffMinutes === 1 ? '1 min ago' : `${diffMinutes} mins ago`;
        }

        if (diffHours < 24) {
            return diffHours === 1 ? '1 hr ago' : `${diffHours} hrs ago`;
        }

        if (diffDays < 7) {
            return diffDays === 1 ? '1 day ago' : `${diffDays} days ago`;
        }

        return created.toLocaleString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    function updateLiveTimes() {
        timeElements.forEach(function (element) {
            const rawTime = element.dataset.requestTime;
            if (!rawTime) return;
            element.textContent = formatTimeAgo(rawTime);
        });
    }

    updateLiveTimes();
    setInterval(updateLiveTimes, 60000);
});
</script>
@endsection
