@php
    $currentPatient = $patient ?? null;
@endphp

<form method="POST" action="{{ $action }}" class="row g-3">
    @csrf
@if(isset($method) && $method !== 'POST')
    <input type="hidden" name="_method" value="{{ $method }}">
@endif

    <div class="col-md-6">
        <label class="form-label">GHIMS Number</label>
        <input type="text" name="ghims_number" value="{{ old('ghims_number', $currentPatient?->ghims_number) }}" class="form-control @error('ghims_number') is-invalid @enderror" required>
        @error('ghims_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Patient Full Name</label>
        <input type="text" name="patient_name" value="{{ old('patient_name', $currentPatient?->patient_name) }}" class="form-control @error('patient_name') is-invalid @enderror" required>
        @error('patient_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Age</label>
        <input id="patient-age" type="number" min="0" max="150" name="age" value="{{ old('age', $currentPatient?->age) }}" class="form-control @error('age') is-invalid @enderror" required>
        @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input id="patient-date-of-birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($currentPatient?->date_of_birth)->format('Y-m-d')) }}" class="form-control @error('date_of_birth') is-invalid @enderror" required>
        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Triage Outcome</label>
        <select name="triage_outcome" class="form-select @error('triage_outcome') is-invalid @enderror" required>
            <option value="">Select outcome</option>
            <option value="alive" @selected(old('triage_outcome') === 'alive')>Alive — admit and treat</option>
            <option value="dead" @selected(old('triage_outcome') === 'dead')>Brought in dead</option>
        </select>
        @error('triage_outcome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
    <label class="form-label">Specialty Teams</label>
<div class="row g-2">
    @php
        $selectedTeamIds = old('team_ids', $currentPatient ? $currentPatient->teams->pluck('id')->toArray() : []);
    @endphp
    @foreach($teams as $team)
<div class="col-6">
            <div class="form-check w-100">>
                <input class="form-check-input" type="checkbox" name="team_ids[]" value="{{ $team->id }}" id="team_{{ $team->id }}" @if(in_array($team->id, $selectedTeamIds)) checked @endif>
                <label class="form-check-label" for="team_{{ $team->id }}">
                    {{ $team->name }}
                </label>
            </div>
        </div>
    @endforeach
</div>
@error('team_ids')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Chief Complaint</label>
        <textarea name="chief_complaint" rows="4" class="form-control @error('chief_complaint') is-invalid @enderror" required>{{ old('chief_complaint', $currentPatient?->chief_complaint) }}</textarea>
        @error('chief_complaint')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Nurse Notes</label>
        <textarea name="nurse_notes" rows="4" class="form-control @error('nurse_notes') is-invalid @enderror" placeholder="Enter nurse notes or observations">{{ old('nurse_notes', $currentPatient?->nurse_notes) }}</textarea>
        @error('nurse_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if($showCondition ?? true)
        <div class="col-md-4">
            <label class="form-label">Condition</label>
            <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                <option value="stable" @selected(old('condition', $currentPatient?->condition ?? 'stable') === 'stable')>Stable</option>
                <option value="moderate" @selected(old('condition', $currentPatient?->condition ?? '') === 'moderate')>Moderate</option>
                <option value="serious" @selected(old('condition', $currentPatient?->condition ?? '') === 'serious')>Serious</option>
                <option value="critical" @selected(old('condition', $currentPatient?->condition ?? '') === 'critical')>Critical</option>
            </select>
            @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">{{ $submitLabel ?? 'Save Patient' }}</button>
        <a href="{{ route('patients.index') }}" class="btn btn-light">Cancel</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ageInput = document.getElementById('patient-age');
            const dobInput = document.getElementById('patient-date-of-birth');

            if (!ageInput || !dobInput) {
                return;
            }

            const formatDate = (date) => date.toISOString().slice(0, 10);

            const updateDobFromAge = () => {
                const age = parseInt(ageInput.value, 10);
                if (Number.isNaN(age) || age < 0) {
                    return;
                }

                const today = new Date();
                const dob = new Date(today.getFullYear() - age, today.getMonth(), today.getDate());
                dobInput.value = formatDate(dob);
            };

            const updateAgeFromDob = () => {
                const dob = new Date(dobInput.value);
                if (Number.isNaN(dob.getTime())) {
                    return;
                }

                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                const dayDiff = today.getDate() - dob.getDate();

                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                    age -= 1;
                }

                ageInput.value = age >= 0 ? age : 0;
            };

            ageInput.addEventListener('input', updateDobFromAge);
            dobInput.addEventListener('change', updateAgeFromDob);
        });
    </script>
</form>
