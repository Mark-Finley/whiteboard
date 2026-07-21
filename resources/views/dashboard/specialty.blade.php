@extends('layouts.app')

@section('content')
<div class="card section-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">{{ $team->name }}</h3>
            <div class="muted-label">Specialty team dashboard.</div>
        </div>
        <span class="badge badge-soft-green">Read only</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable" data-live-url="{{ route('api.team-patients', ['team' => $team->name]) }}" data-table-kind="specialty">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient Name</th>
                    <th>Current Ward</th>
                    <th>Chief Complaint</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr>
                        <td>{{ $patient->ghims_number }}</td>
                        <td><div class="patient-name">{{ $patient->patient_name }}</div></td>
                        <td>{{ $patient->ward?->name ?? 'Unassigned' }}</td>
                        <td>{{ $patient->chief_complaint }}</td>
                        <td>{{ optional($patient->time_in)->format('d M Y, H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
