@extends('layouts.app')

@section('content')
@php
    $liveUrl = match ($ward->name) {
        'RED' => route('api.red-patients'),
        'ORANGE' => route('api.orange-patients'),
        'YELLOW' => route('api.yellow-patients'),
        default => route('api.triege-patients'),
    };
    $userWardColor = optional(auth()->user()->ward)->color_code ?? $ward->color_code;
@endphp

<div class="card section-card mb-4" style="border: 2px solid {{ $userWardColor }};">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">{{ $ward->name }} Ward</h3>
            <div class="muted-label">{{ $ward->name }} patients live view.</div>
        </div>
        <span class="badge" style="background: {{ $userWardColor }}; color: #fff;">{{ $ward->name }}</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable" style="border-top: 3px solid {{ $userWardColor }};" data-live-url="{{ $liveUrl }}" data-table-kind="ward-board">
            <thead style="background: {{ $userWardColor }}; color: #fff;">
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
