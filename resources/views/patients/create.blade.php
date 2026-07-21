@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Register patient</h3>
        <div class="muted-label">Create a new emergency patient record.</div>
    </div>

    @include('patients._form', [
        'action' => route('patients.store'),
        'method' => 'POST',
        'patient' => null,
        'submitLabel' => 'Register patient',
        'wards' => $wards,
        'teams' => $teams,
         'showCondition' => false,
    ])
</div>
@endsection
