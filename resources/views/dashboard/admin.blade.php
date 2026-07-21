@extends('layouts.app')

@section('content')
<div class="kpi-grid mb-4">
    <div class="card metric-card p-4">
        <div class="metric-label">Total Active Patients</div>
        <div class="metric-value">{{ $totalActivePatients }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">RED</div>
        <div class="metric-value" style="color:{{ $redColor }};">{{ $redCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">ORANGE</div>
        <div class="metric-value" style="color:{{ $orangeColor }};">{{ $orangeCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">YELLOW</div>
        <div class="metric-value" style="color:{{ $yellowColor }};">{{ $yellowCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">TRIAGE HOLDING</div>
        <div class="metric-value" style="color:{{ $triageColor }};">{{ $triageCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Deceased</div>
        <div class="metric-value text-danger">{{ $deceasedCount }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Discharged Today</div>
        <div class="metric-value">{{ $dischargedToday }}</div>
    </div>
    <div class="card metric-card p-4">
        <div class="metric-label">Admitted Today</div>
        <div class="metric-value">{{ $admittedToday }}</div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">Ward Distribution</h5>
                <span class="loading-pill"><i class="fa-solid fa-chart-pie"></i> Live overview</span>
            </div>
            <canvas id="wardChart" height="260"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <h5 class="mb-3 fw-bold">Specialty Distribution</h5>
            <canvas id="specialtyChart" height="260"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <h5 class="mb-3 fw-bold">Daily Activity</h5>
            <canvas id="activityChart" height="260"></canvas>
        </div>
    </div>
</div>

<div class="card section-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h5 class="fw-bold mb-1">Recent patients</h5>
            <div class="muted-label">Filter by date, ward, specialty, or patient name.</div>
        </div>
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-auto"><input type="date" name="date" value="{{ request('date') }}" class="form-control"></div>
            <div class="col-auto">
                <select name="ward" class="form-select">
                    <option value="">All wards</option>
                    @foreach($wardOptions as $wardOption)
                        <option value="{{ $wardOption->name }}" @selected(request('ward') === $wardOption->name)>{{ $wardOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="specialty" class="form-select">
                    <option value="">All specialties</option>
                    @foreach($specialtyOptions as $specialtyOption)
                        <option value="{{ $specialtyOption->name }}" @selected(request('specialty') === $specialtyOption->name)>{{ $specialtyOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search"></div>
            <div class="col-auto"><button class="btn btn-primary" type="submit">Apply</button></div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>GHIMS</th>
                    <th>Patient</th>
                    <th>Specialty</th>
                    <th>Status</th>
                    <th>Time In</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr class="patient-ward-row patient-ward-row-{{ strtolower(str_replace(' ', '-', $patient->ward?->name ?? 'unassigned')) }}">
                        <td>{{ $patient->ghims_number }}</td>
                        <td><div class="patient-name">{{ $patient->patient_name }}</div></td>
                        <td>{{ $patient->team?->name ?? 'Unassigned' }}</td>
                        <td><span class="badge {{ match($patient->status) { 'active' => 'badge-soft-green', 'transferred' => 'badge-soft-orange', 'admitted' => 'badge-soft-yellow', default => 'badge-soft-gray' } }}">{{ ucfirst($patient->status) }}</span></td>
                        <td>{{ optional($patient->time_in)->format('d M Y, g:i a') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $patients->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
const wardCtx = document.getElementById('wardChart');
if (wardCtx) {
    new Chart(wardCtx, {
        type: 'doughnut',
        data: {
            labels: @json($wardDistribution->pluck('name')),
            datasets: [{
                data: @json($wardDistribution->pluck('count')),
                backgroundColor: @json($wardDistribution->pluck('color')),
                borderWidth: 0,
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}

const specialtyCtx = document.getElementById('specialtyChart');
if (specialtyCtx) {
    new Chart(specialtyCtx, {
        type: 'bar',
        data: {
            labels: @json($specialtyDistribution->pluck('name')),
            datasets: [{
                label: 'Patients',
                data: @json($specialtyDistribution->pluck('count')),
                backgroundColor: '{{ $triageColor }}',
                borderRadius: 12,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
}

const activityCtx = document.getElementById('activityChart');
if (activityCtx) {
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: @json($activityLabels),
            datasets: [
                {
                    label: 'Registered',
                    data: @json($activityCreated),
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15,118,110,0.14)',
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Discharged',
                    data: @json($activityDischarged),
                    borderColor: '{{ $orangeColor }}',
                    backgroundColor: 'rgba(249,115,22,0.14)',
                    tension: 0.35,
                    fill: true,
                }
            ]
        },
        options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
}
</script>
@endpush
