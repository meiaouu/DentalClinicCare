@extends('staff.layouts.app')

@section('content')
<style>
    .patient-create-page {
        max-width: 980px;
        display: grid;
        gap: 18px;
    }

    .patient-create-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .patient-create-header p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .patient-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #fff;
        color: #0f172a;
        box-sizing: border-box;
        outline: none;
    }

    .form-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #0f9d8a;
        box-shadow: 0 0 0 3px rgba(15, 157, 138, 0.10);
    }

    .alert-box {
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .alert-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #0f9d8a;
        color: #fff;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #334155;
    }

    @media (max-width: 700px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: auto;
        }
    }
</style>

<div class="patient-create-page">
    <div class="patient-create-header">
        <h1>Create Patient</h1>
        <p>Staff can manually register a patient profile for walk-ins, phone inquiries, or guest conversion.</p>
    </div>

    @if($errors->any())
        <div class="alert-box">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="patient-card">
        <form method="POST" action="{{ route('staff.patients.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-input" value="{{ old('middle_name') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-select" required>
                        <option value="">Select Sex</option>
                        <option value="male" @selected(old('sex') === 'male')>Male</option>
                        <option value="female" @selected(old('sex') === 'female')>Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Birth Date</label>
                    <input type="date" name="birth_date" class="form-input" value="{{ old('birth_date') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Civil Status</label>
                    <input type="text" name="civil_status" class="form-input" value="{{ old('civil_status') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Occupation</label>
                    <input type="text" name="occupation" class="form-input" value="{{ old('occupation') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-input" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-input" value="{{ old('emergency_contact_name') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" class="form-input" value="{{ old('emergency_contact_number') }}">
                </div>

                <div class="form-group full">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-input" value="{{ old('address') }}">
                </div>

                <div class="form-group full">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Patient</button>
                <a href="{{ route('staff.patients.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
