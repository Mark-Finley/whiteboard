@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card section-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h3 class="fw-bold mb-1">White Board</h3>
                    <div class="muted-label">Patients enter the triage queue first, then move automatically into their assigned ward board.</div>
                </div>
                <span class="loading-pill"><i class="fa-solid fa-layer-group"></i> Live patient board</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" data-live-url="{{ route('api.triage-patients') }}" data-table-kind="white-board">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Ward</th>
                            <th>Assigned Team</th>
                            <th>Nurse Notes</th>
                            <th>Condition</th>
                            <th>LOS</th>
                            <th>Status</th>
                            <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($triagePatients as $patient)
                            @php
                                $statusClass = match($patient['status']) {
                                    'active' => 'badge-soft-green',
                                    'transferred' => 'badge-soft-orange',
                                    'admitted' => 'badge-soft-yellow',
                                    'deceased' => 'bg-dark',
                                    default => 'badge-soft-gray',
                                };
                            @endphp
                            <tr class="patient-ward-row patient-ward-row-triage-holding">
                                <td>{{ $patient['ghims_number'] }}</td>
                                <td>
                                    <div class="patient-name">{{ $patient['patient_name'] }}</div>
                                    @if(isset($patient['investigations']) && count($patient['investigations']))
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            @foreach(collect($patient['investigations'])->take(5) as $inv)
                                                @php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                @endphp
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="{{ $inv['id'] }}" 
                                                      data-name="{{ $inv['investigation_type'] }}" 
                                                      data-status="{{ $inv['status'] }}" 
                                                      data-notes="{{ $inv['notes'] ?? '' }}"
                                                      data-timeline='@json($inv['updates'] ?? [])'
                                                      style="background: {{ $color }}; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="{{ $inv['investigation_type'] }} ({{ $inv['status'] }})">
                                                    {{ $inv['investigation_type'] }}
                                                </span>
                                            @endforeach
                                            @if(count($patient['investigations']) > 5)
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ count($patient['investigations']) - 5 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: {{ $patient['ward_color'] }}; color: #fff;">
                                        {{ $patient['current_ward'] ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td>{{ $patient['assigned_team'] ?? 'Unassigned' }}</td>
                                    <td @if($patient['nurse_notes']) data-bs-toggle="tooltip" title="{{ $patient['nurse_notes'] }}" @endif>{{ $patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—' }}</td>
                                <td>{{ ucfirst($patient['condition'] ?? 'unknown') }}</td>
                                <td @if(!empty($patient['ward_time_breakdown'])) data-bs-toggle="tooltip" title="{{ $patient['ward_time_breakdown'] }}" @endif>{{ $patient['ward_time'] ?? '—' }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ ucfirst($patient['status']) }}</span></td>
                                <td>{{ $patient['time_in'] }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold mb-0">RED Ward</h5>
                <span class="badge" style="background: {{ \App\Models\Ward::RED_COLOR }}; color: #fff;">RED</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-live-url="{{ route('api.red-patients') }}" data-table-kind="white-board-ward">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Assigned Team</th>
                                <th>Nurse Notes</th>
                                <th>LOS</th>
                                <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redPatients as $patient)
                            <tr class="patient-ward-row patient-ward-row-red">
                                <td>{{ $patient['ghims_number'] }}</td>
                                <td>
                                    <div class="patient-name">{{ $patient['patient_name'] }}</div>
                                    @if(isset($patient['investigations']) && count($patient['investigations']))
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            @foreach(collect($patient['investigations'])->take(5) as $inv)
                                                @php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                @endphp
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="{{ $inv['id'] }}" 
                                                      data-name="{{ $inv['investigation_type'] }}" 
                                                      data-status="{{ $inv['status'] }}" 
                                                      data-notes="{{ $inv['notes'] ?? '' }}"
                                                      data-timeline='@json($inv['updates'] ?? [])'
                                                      style="background: {{ $color }}; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="{{ $inv['investigation_type'] }} ({{ $inv['status'] }})">
                                                    {{ $inv['investigation_type'] }}
                                                </span>
                                            @endforeach
                                            @if(count($patient['investigations']) > 5)
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ count($patient['investigations']) - 5 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $patient['assigned_team'] ?? 'Unassigned' }}</td>
                                    <td @if($patient['nurse_notes']) data-bs-toggle="tooltip" title="{{ $patient['nurse_notes'] }}" @endif>{{ $patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—' }}</td>
                                    <td>{{ $patient['ward_time'] ?? '—' }}</td>
                                    <td>{{ $patient['time_in'] }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold mb-0">ORANGE Ward</h5>
                <span class="badge" style="background: {{ \App\Models\Ward::ORANGE_COLOR }}; color: #fff;">ORANGE</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-live-url="{{ route('api.orange-patients') }}" data-table-kind="white-board-ward">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Assigned Team</th>
                                <th>Nurse Notes</th>
                                <th>LOS</th>
                                <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orangePatients as $patient)
                            <tr class="patient-ward-row patient-ward-row-orange">
                                <td>{{ $patient['ghims_number'] }}</td>
                                <td>
                                    <div class="patient-name">{{ $patient['patient_name'] }}</div>
                                    @if(isset($patient['investigations']) && count($patient['investigations']))
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            @foreach(collect($patient['investigations'])->take(5) as $inv)
                                                @php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                @endphp
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="{{ $inv['id'] }}" 
                                                      data-name="{{ $inv['investigation_type'] }}" 
                                                      data-status="{{ $inv['status'] }}" 
                                                      data-notes="{{ $inv['notes'] ?? '' }}"
                                                      data-timeline='@json($inv['updates'] ?? [])'
                                                      style="background: {{ $color }}; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="{{ $inv['investigation_type'] }} ({{ $inv['status'] }})">
                                                    {{ $inv['investigation_type'] }}
                                                </span>
                                            @endforeach
                                            @if(count($patient['investigations']) > 5)
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ count($patient['investigations']) - 5 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $patient['assigned_team'] ?? 'Unassigned' }}</td>
                                    <td @if($patient['nurse_notes']) data-bs-toggle="tooltip" title="{{ $patient['nurse_notes'] }}" @endif>{{ $patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—' }}</td>
                                    <td>{{ $patient['ward_time'] ?? '—' }}</td>
                                    <td>{{ $patient['time_in'] }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold mb-0">YELLOW Ward</h5>
                <span class="badge" style="background: {{ \App\Models\Ward::YELLOW_COLOR }}; color: #fff;">YELLOW</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-live-url="{{ route('api.yellow-patients') }}" data-table-kind="white-board-ward">
                    <thead>
                        <tr>
                            <th>GHIMS</th>
                            <th>Patient</th>
                            <th>Assigned Team</th>
                                <th>Nurse Notes</th>
                                <th>LOS</th>
                                <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($yellowPatients as $patient)
                            <tr class="patient-ward-row patient-ward-row-yellow">
                                <td>{{ $patient['ghims_number'] }}</td>
                                <td>
                                    <div class="patient-name">{{ $patient['patient_name'] }}</div>
                                    @if(isset($patient['investigations']) && count($patient['investigations']))
                                        <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                            @foreach(collect($patient['investigations'])->take(5) as $inv)
                                                @php
                                                    $color = match($inv['status']) {
                                                        'Pending' => '#eab308',
                                                        'In Progress', 'Sample Taken', 'Sent' => '#2563eb',
                                                        'Completed' => '#16a34a',
                                                        'Cancelled' => '#dc2626',
                                                        default => '#6b7280',
                                                    };
                                                @endphp
                                                <span class="badge cursor-pointer status-badge" 
                                                      data-id="{{ $inv['id'] }}" 
                                                      data-name="{{ $inv['investigation_type'] }}" 
                                                      data-status="{{ $inv['status'] }}" 
                                                      data-notes="{{ $inv['notes'] ?? '' }}"
                                                      data-timeline='@json($inv['updates'] ?? [])'
                                                      style="background: {{ $color }}; color: #fff; font-size: 0.7rem; padding: 0.35em 0.7em;" 
                                                      title="{{ $inv['investigation_type'] }} ({{ $inv['status'] }})">
                                                    {{ $inv['investigation_type'] }}
                                                </span>
                                            @endforeach
                                            @if(count($patient['investigations']) > 5)
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ count($patient['investigations']) - 5 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $patient['assigned_team'] ?? 'Unassigned' }}</td>
                                    <td @if($patient['nurse_notes']) data-bs-toggle="tooltip" title="{{ $patient['nurse_notes'] }}" @endif>{{ $patient['nurse_notes'] ? \Illuminate\Support\Str::limit($patient['nurse_notes'], 80) : '—' }}</td>
                                    <td>{{ $patient['ward_time'] ?? '—' }}</td>
                                    <td>{{ $patient['time_in'] }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('partials.investigation-modals')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
@endpush