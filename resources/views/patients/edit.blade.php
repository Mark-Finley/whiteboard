@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit patient</h3>
        <div class="muted-label">Update patient details and triage assignment.</div>
    </div>

    @include('patients._form', [
        'action' => route('patients.update', $patient),
        'method' => 'PUT',
        'patient' => $patient,
        'submitLabel' => 'Update patient',
        'wards' => $wards,
        'teams' => $teams,
    ])
</div>
@endsection
