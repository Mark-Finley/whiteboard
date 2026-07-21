<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2 { margin: 0 0 10px; }
        .meta { margin-bottom: 20px; }
        .meta span { display: inline-block; margin-right: 20px; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        tbody tr:nth-child(even) { background: #fafafa; }
    </style>
</head>
<body>
    <h1>Patient Report</h1>
    <div class="meta">
        @if($fromDate)
            <span>From: {{ $fromDate }}</span>
        @endif
        @if($toDate)
            <span>To: {{ $toDate }}</span>
        @endif
        @if($wardName)
            <span>Ward: {{ $wardName }}</span>
        @endif
        @if($specialtyName)
            <span>Specialty: {{ $specialtyName }}</span>
        @endif
        @if($status)
            <span>Status: {{ ucfirst($status) }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>GHIMS</th>
                <th>Patient Name</th>
                <th>Ward</th>
                <th>Specialty</th>
                <th>Status</th>
                <th>Time In</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->ghims_number }}</td>
                    <td>{{ $patient->patient_name }}</td>
                    <td>{{ $patient->ward?->name ?? 'Unassigned' }}</td>
                    <td>{{ $patient->team?->name ?? 'Unassigned' }}</td>
                    <td>{{ ucfirst($patient->status) }}</td>
                    <td>{{ optional($patient->time_in)->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                    <td>{{ $patient->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
