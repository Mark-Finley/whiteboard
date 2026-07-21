<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    private Collection $patients;

    public function __construct(Collection $patients)
    {
        $this->patients = $patients;
    }

    public function collection(): Collection
    {
        return $this->patients->map(fn ($patient) => [
            'GHIMS' => $patient->ghims_number,
            'Patient Name' => $patient->patient_name,
            'Ward' => $patient->ward?->name ?? 'Unassigned',
            'Specialty' => $patient->team?->name ?? 'Unassigned',
            'Status' => ucfirst($patient->status),
            'Time In' => optional($patient->time_in)->format('Y-m-d H:i:s') ?? 'N/A',
            'Created At' => $patient->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function headings(): array
    {
        return [
            'GHIMS',
            'Patient Name',
            'Ward',
            'Specialty',
            'Status',
            'Time In',
            'Created At',
        ];
    }
}
