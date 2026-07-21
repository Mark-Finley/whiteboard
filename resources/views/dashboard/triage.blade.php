@extends('layouts.app')

@section('content')
<div class="card section-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Triage Holding</h3>
            <div class="muted-label">Patients awaiting assignment to RED, ORANGE, or YELLOW.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable" data-live-url="{{ route('api.triage-patients') }}" data-table-kind="ward-board">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient Name</th>
                    <th>Assigned Team</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr>
                        <td>{{ $patient->ghims_number }}</td>
                        <td><div class="patient-name">{{ $patient->patient_name }}</div></td>
                        <td>{{ $patient->team?->name ?? 'Unassigned' }}</td>
                        <td>{{ optional($patient->time_in)->format('d M Y, H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
