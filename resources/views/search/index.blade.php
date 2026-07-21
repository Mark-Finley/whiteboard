@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Patient search</h3>
            <div class="muted-label">Search by GHIMS number or patient name.</div>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="search" name="query" value="{{ $query ?? request('query') }}" class="form-control" placeholder="GHIMS number or patient name">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Specialty</th>
                    <th>Complaint</th>
                    <th>Time In</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $patient)
                    <tr>
                        <td>{{ $patient->ghims_number }}</td>
                        <td class="fw-semibold">{{ $patient->patient_name }}</td>
                        <td>{{ $patient->ward?->name ?? 'Unassigned' }}</td>
                        <td>{{ $patient->team?->name ?? 'Unassigned' }}</td>
                        <td>{{ $patient->chief_complaint }}</td>
                        <td>{{ optional($patient->time_in)->format('d M Y, H:i') }}</td>
                        <td>{{ ucfirst($patient->status) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
