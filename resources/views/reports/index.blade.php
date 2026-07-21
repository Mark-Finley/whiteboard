@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Reports</h3>
        <div class="muted-label">Generate system-wide reports for patients, wards, and specialties with downloadable output.</div>
    </div>
</div>

<form class="card section-card p-4 mb-4" method="GET">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Ward</label>
            <select name="ward" class="form-select">
                <option value="">All wards</option>
                @foreach($wardOptions as $wardOption)
                    <option value="{{ $wardOption->name }}" @selected($selectedWard === $wardOption->name)>{{ $wardOption->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Specialty</label>
            <select name="specialty" class="form-select">
                <option value="">All specialties</option>
                @foreach($specialtyOptions as $specialtyOption)
                    <option value="{{ $specialtyOption->name }}" @selected($selectedSpecialty === $specialtyOption->name)>{{ $specialtyOption->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach($statusOptions as $statusOption)
                    <option value="{{ $statusOption }}" @selected($selectedStatus === $statusOption)>{{ ucfirst($statusOption) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Generate</button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" name="export" value="csv" class="btn btn-outline-secondary">Download CSV</button>
            <button type="submit" name="export" value="xlsx" class="btn btn-outline-secondary">Download XLSX</button>
            <button type="submit" name="export" value="pdf" class="btn btn-outline-secondary">Download PDF</button>
        </div>
    </div>
</form>

<div class="kpi-grid mb-4">
    <div class="card metric-card p-4">
        <div class="metric-label">Total Patients</div>
        <div class="metric-value">{{ $totalPatients }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Active</div>
        <div class="metric-value">{{ $activeCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Admitted</div>
        <div class="metric-value">{{ $admittedCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Discharged</div>
        <div class="metric-value">{{ $dischargedCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Deceased</div>
        <div class="metric-value text-danger">{{ $deceasedCount }}</div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card section-card h-100">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Ward Summary</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ward</th>
                            <th>Active</th>
                            <th>Admitted</th>
                            <th>Transferred</th>
                            <th>Discharged</th>
                            <th>Deceased</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wardSummary as $ward)
                            <tr>
                                <td>{{ $ward['name'] }}</td>
                                <td>{{ $ward['active'] }}</td>
                                <td>{{ $ward['admitted'] }}</td>
                                <td>{{ $ward['transferred'] }}</td>
                                <td>{{ $ward['discharged'] }}</td>
                                <td>{{ $ward['deceased'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card section-card h-100">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Specialty Summary</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Specialty</th>
                            <th>Patients</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specialtySummary as $team)
                            <tr>
                                <td>{{ $team['name'] }}</td>
                                <td>{{ $team['patients'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">

<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Patient Report</h5>
            <div class="muted-label">List of patients matching the selected report filters.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Specialty</th>
                    <th>Status</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>{{ $patient->ghims_number }}</td>
                        <td>{{ $patient->patient_name }}</td>
                        <td>{{ $patient->ward?->name ?? 'Unassigned' }}</td>
                        <td>{{ $patient->team?->name ?? 'Unassigned' }}</td>
                        <td>{{ ucfirst($patient->status) }}</td>
                        <td>{{ optional($patient->time_in)->format('d M Y, g:i a') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No patients found for this report.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $patients->links() }}</div>
</div>
@endsection
