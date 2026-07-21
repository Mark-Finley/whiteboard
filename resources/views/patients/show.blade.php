@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="fw-bold">Patient: {{ $patient->patient_name }}</h3>
            <div class="muted-label">GHIMS: {{ $patient->ghims_number }} • Age {{ $patient->age }} • Status: <strong>{{ ucfirst($patient->status) }}</strong></div>
        </div>
        <div>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h5 class="mb-2">Details</h5>
                <p><strong>Ward:</strong> {{ $patient->ward?->name ?? 'Unassigned' }}</p>
                <p><strong>Specialty Teams:</strong> {{ $patient->teams->pluck('name')->join(', ') ?: ($patient->team?->name ?? 'Unassigned') }}</p>
                <p><strong>Time In:</strong> {{ optional($patient->time_in)->format('d M Y, H:i') }} • <strong>LOS:</strong> {{ $patient->ward_time_spent ?? '—' }}</p>
                <p><strong>Chief Complaint:</strong><br>{{ $patient->chief_complaint }}</p>
            </div>

            <div class="card p-3 mb-3">
                <h5 class="mb-2">Nurse Notes</h5>
                <div class="mb-2">{{ $patient->nurse_notes ? nl2br(e($patient->nurse_notes)) : '<span class="text-muted">No notes recorded.</span>' }}</div>
                @can('update', $patient)
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#notesModal">Edit Notes</button>
                @endcan
            </div>
        </div>
        <div class="col-md-6">
            <!-- Investigations Panel -->
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-flask me-2 text-teal"></i>Investigations & Procedures</h5>
                    @if(auth()->user()?->canAssignProcedures())
                        <button class="btn btn-sm btn-outline-teal rounded-pill" id="assignInvestigationBtn" data-patient-id="{{ $patient->id }}" data-patient-name="{{ $patient->patient_name }}">
                            <i class="fa-solid fa-plus me-1"></i>Assign
                        </button>
                    @endif
                </div>

                @if($patient->investigations && $patient->investigations->count())
                    <div class="list-group list-group-flush">
                        @foreach($patient->investigations->sortByDesc('assigned_at') as $inv)
                            @php
                                $statusClass = match($inv->status) {
                                    'Pending' => 'badge-soft-yellow',
                                    'Sample Taken' => 'badge-soft-orange',
                                    'Sent' => 'badge-soft-orange',
                                    'In Progress' => 'badge-soft-primary',
                                    'Completed' => 'badge-soft-green',
                                    'Cancelled' => 'badge-soft-red',
                                    default => 'badge-soft-gray',
                                };
                                $priorityClass = match($inv->priority) {
                                    'Stat' => 'bg-danger text-white',
                                    'Urgent' => 'bg-warning text-dark',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $inv->investigation_type }}
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 0.8rem;">
                                            Category: <span class="fw-semibold text-secondary">{{ $inv->category }}</span> | 
                                            Priority: <span class="badge {{ $priorityClass }}">{{ $inv->priority }}</span>
                                        </div>
                                        @if($inv->notes)
                                            <div class="text-secondary small mt-1 bg-light p-2 rounded" style="border-left: 3px solid #0f766e; font-size: 0.8rem;">
                                                {{ $inv->notes }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge cursor-pointer status-badge {{ $statusClass }}" 
                                              data-id="{{ $inv->id }}" 
                                              data-name="{{ $inv->investigation_type }}" 
                                              data-status="{{ $inv->status }}" 
                                              data-notes="{{ $inv->notes }}"
                                              data-timeline='@json($inv->getTimelineData())'
                                              style="font-size: 0.8rem; padding: 0.4em 0.8em; cursor: pointer;">
                                            {{ $inv->status }}
                                        </span>
                                        <div class="text-muted small mt-1" style="font-size: 0.7rem; line-height: 1.2;">
                                            Assigned by {{ $inv->assignedBy?->name ?? 'System' }}<br>
                                            {{ optional($inv->assigned_at)->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small py-3 text-center">No investigations or procedures assigned yet.</div>
                @endif
            </div>

            <!-- Movements Panel -->
            <div class="card p-3">
                <h5 class="mb-2 fw-bold">Movements</h5>
                @if($patient->movements && $patient->movements->count())
                    <ul class="list-group">
                        @foreach($patient->movements as $m)
                            <li class="list-group-item">
                                <div><strong>{{ $m->action }}</strong> by {{ $m->movedBy?->name ?? 'System' }} on {{ optional($m->created_at)->format('d M Y, H:i') }}</div>
                                @if($m->notes)
                                    <div class="muted-label">{{ $m->notes }}</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted">No movements recorded.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('partials.investigation-modals')


<div class="mt-3">
    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#timeBreakdownModal">View LOS Breakdown</button>
</div>

<!-- Time breakdown modal -->
<div class="modal fade" id="timeBreakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                        <h5 class="modal-title">LOS Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php($timeline = $patient->ward_timeline)
                @if($timeline && count($timeline))
                    <ul class="list-group">
                        @foreach($timeline as $segment)
                            <li class="list-group-item">
                                <div>
                                    <div><strong>{{ $segment['ward'] }}</strong></div>
                                    <div class="muted-label">{{ $segment['start'] }} → {{ $segment['end'] }}</div>
                                    <div class="muted-label">Duration: {{ $segment['duration'] }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted">No time data available.</div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Notes modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Nurse Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('patients.notes', $patient) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea name="nurse_notes" rows="6" class="form-control">{{ old('nurse_notes', $patient->nurse_notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save notes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
