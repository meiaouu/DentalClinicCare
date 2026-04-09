@extends('dentist.layouts.app')

@section('page_title', 'Availability')

@section('dentist_content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <h3>Weekly Availability</h3>

            <form method="POST" action="{{ route('dentist.availability.store') }}">
                @csrf

                @foreach($dayLabels as $dayValue => $dayLabel)
                    @php $schedule = $schedules[$dayValue] ?? null; @endphp

                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>{{ $dayLabel }}</strong>
                            <label>
                                <input type="checkbox" name="days[{{ $dayValue }}][is_available]" value="1"
                                    {{ old("days.$dayValue.is_available", $schedule->is_available ?? false) ? 'checked' : '' }}>
                                Available
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label>Start</label>
                                <input type="time" name="days[{{ $dayValue }}][start_time]" class="form-control"
                                    value="{{ old("days.$dayValue.start_time", isset($schedule->start_time) ? substr($schedule->start_time,0,5) : '') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>End</label>
                                <input type="time" name="days[{{ $dayValue }}][end_time]" class="form-control"
                                    value="{{ old("days.$dayValue.end_time", isset($schedule->end_time) ? substr($schedule->end_time,0,5) : '') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Max Patients</label>
                                <input type="number" name="days[{{ $dayValue }}][max_patients]" class="form-control"
                                    value="{{ old("days.$dayValue.max_patients", $schedule->max_patients ?? 20) }}">
                            </div>
                        </div>
                    </div>
                @endforeach

                <button class="btn btn-primary" type="submit">Save Weekly Availability</button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4 mb-4">
            <h3>Block Date / Time</h3>

            <form method="POST" action="{{ route('dentist.unavailable-dates.store') }}">
                @csrf
                <div class="mb-3">
                    <label>Date</label>
                    <input type="date" name="unavailable_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control">
                </div>
                <div class="mb-3">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Reason</label>
                    <input type="text" name="reason" class="form-control">
                </div>
                <button class="btn btn-outline-danger" type="submit">Add Block</button>
            </form>
        </div>

        <div class="card p-4">
            <h3>Blocked Dates</h3>

            @forelse($unavailableDates as $item)
                <div class="border rounded p-3 mb-2">
                    <strong>{{ $item->unavailable_date }}</strong>
                    <div>{{ $item->start_time }} - {{ $item->end_time }}</div>
                    <small>{{ $item->reason }}</small>

                    <form method="POST" action="{{ route('dentist.unavailable-dates.destroy', $item->unavailable_id) }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Remove</button>
                    </form>
                </div>
            @empty
                <p class="mb-0">No blocked dates.</p>
            @endforelse

            {{ $unavailableDates->links() }}
        </div>
    </div>
</div>
@endsection
