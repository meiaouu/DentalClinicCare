@extends('dentist.layouts.app')

@section('content')
    <h1 style="margin-top:0;">My Availability</h1>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        <div class="card">
            <h2 style="margin-top:0;">Weekly Availability</h2>

            <form method="POST" action="{{ route('dentist.availability.store') }}">
                @csrf

                @foreach($dayLabels as $dayValue => $dayLabel)
                    @php
                        $schedule = $schedules[$dayValue] ?? null;
                    @endphp

                    <div style="border:1px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:14px;">
                        <div style="font-weight:700;margin-bottom:10px;">{{ $dayLabel }}</div>

                        <input type="hidden" name="schedules[{{ $dayValue }}][day_of_week]" value="{{ $dayValue }}">

                        <label style="display:block;margin-bottom:10px;">
                            <input type="checkbox"
                                   name="schedules[{{ $dayValue }}][is_available]"
                                   value="1"
                                   {{ old("schedules.$dayValue.is_available", $schedule?->is_available) ? 'checked' : '' }}>
                            Available
                        </label>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                            <div>
                                <label>Start Time</label>
                                <input type="time"
                                       name="schedules[{{ $dayValue }}][start_time]"
                                       value="{{ old("schedules.$dayValue.start_time", $schedule?->start_time) }}"
                                       style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;">
                            </div>

                            <div>
                                <label>End Time</label>
                                <input type="time"
                                       name="schedules[{{ $dayValue }}][end_time]"
                                       value="{{ old("schedules.$dayValue.end_time", $schedule?->end_time) }}"
                                       style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;">
                            </div>

                            <div>
                                <label>Max Patients</label>
                                <input type="number"
                                       name="schedules[{{ $dayValue }}][max_patients]"
                                       value="{{ old("schedules.$dayValue.max_patients", $schedule?->max_patients ?? 20) }}"
                                       min="1"
                                       style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;">
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="submit" style="padding:12px 16px;background:#2563eb;color:white;border:none;border-radius:10px;font-weight:700;">
                    Save Availability
                </button>
            </form>
        </div>

        <div style="display:grid;gap:24px;">
            <div class="card">
                <h2 style="margin-top:0;">Block Specific Date</h2>

                <form method="POST" action="{{ route('dentist.unavailable-dates.store') }}">
                    @csrf

                    <div style="margin-bottom:12px;">
                        <label>Date</label>
                        <input type="date" name="unavailable_date" required style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;">
                    </div>

                    <div style="margin-bottom:12px;">
                        <label>Start Time</label>
                        <input type="time" name="start_time" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;">
                    </div>

                    <div style="margin-bottom:12px;">
                        <label>End Time</label>
                        <input type="time" name="end_time" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;">
                    </div>

                    <div style="margin-bottom:12px;">
                        <label>Reason</label>
                        <textarea name="reason" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;"></textarea>
                    </div>

                    <button type="submit" style="width:100%;padding:12px 16px;background:#dc2626;color:white;border:none;border-radius:10px;font-weight:700;">
                        Add Unavailable Date
                    </button>
                </form>
            </div>

            <div class="card">
                <h2 style="margin-top:0;">Blocked Dates</h2>

                @forelse($unavailableDates as $item)
                    <div style="border:1px solid #e2e8f0;border-radius:12px;padding:12px;margin-bottom:10px;">
                        <strong>{{ $item->unavailable_date }}</strong><br>
                        {{ $item->start_time ?: 'Whole day' }}{{ $item->end_time ? ' - '.$item->end_time : '' }}<br>
                        {{ $item->reason ?: 'No reason provided' }}

                        <form method="POST" action="{{ route('dentist.unavailable-dates.destroy', $item->unavailable_id) }}" style="margin-top:10px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding:8px 12px;background:#334155;color:white;border:none;border-radius:8px;">
                                Remove
                            </button>
                        </form>
                    </div>
                @empty
                    <p>No blocked dates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
