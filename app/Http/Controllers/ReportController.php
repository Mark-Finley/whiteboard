<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\PatientReportExport;
use App\Models\Patient;
use App\Models\Team;
use App\Models\Ward;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Stringable;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): Response|View|BinaryFileResponse
    {
        $fromDate = $request->date('from_date');
        $toDate = $request->date('to_date');
        $wardName = $this->normalizeReportFilter($request->string('ward'));
        $specialtyName = $this->normalizeReportFilter($request->string('specialty'));
        $status = $this->normalizeReportFilter($request->string('status'));
        $exportFormat = trim((string) $request->string('export')) ?: null;

        $patientQuery = Patient::with(['ward', 'team']);
        if ($fromDate) {
            $patientQuery->whereDate('time_in', '>=', $fromDate);
        }
        if ($toDate) {
            $patientQuery->whereDate('time_in', '<=', $toDate);
        }
        if ($wardName) {
            $patientQuery->whereHas('ward', fn ($query) => $query->where('name', $wardName));
        }
        if ($specialtyName) {
            $patientQuery->whereHas('team', fn ($query) => $query->where('name', $specialtyName));
        }
        if ($status) {
            $patientQuery->where('status', $status);
        }

        if (in_array($exportFormat, ['pdf', 'csv', 'xlsx'], true)) {
            $exportPatients = $patientQuery->latest('created_at')->get();
            $dateStamp = now()->format('Y-m-d_H-i-s');
            $fileName = "patient-report-{$dateStamp}.{$exportFormat}";

            if ($exportFormat === 'csv') {
                return response()->streamDownload(
                    function () use ($exportPatients) {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, ['GHIMS', 'Patient Name', 'Ward', 'Specialty', 'Status', 'Time In', 'Created At']);
                        foreach ($exportPatients as $patient) {
                            fputcsv($handle, [
                                $patient->ghims_number,
                                $patient->patient_name,
                                $patient->ward?->name ?? 'Unassigned',
                                $patient->team?->name ?? 'Unassigned',
                                ucfirst($patient->status),
                                optional($patient->time_in)->format('Y-m-d H:i:s') ?? 'N/A',
                                $patient->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                        fclose($handle);
                    },
                    $fileName,
                    ['Content-Type' => 'text/csv']
                );
            }

            if ($exportFormat === 'xlsx') {
                return Excel::download(new PatientReportExport($exportPatients), $fileName);
            }

            return Pdf::loadView('reports.export-pdf', [
                'patients' => $exportPatients,
                'fromDate' => $fromDate?->format('Y-m-d'),
                'toDate' => $toDate?->format('Y-m-d'),
                'wardName' => $wardName,
                'specialtyName' => $specialtyName,
                'status' => $status,
            ])->download($fileName);
        }

        $patients = $patientQuery->latest('created_at')->paginate(20)->withQueryString();

        $wardSummary = Ward::query()->orderBy('name')->get()->map(fn (Ward $ward): array => [
            'name' => $ward->name,
            'active' => Patient::query()->where('ward_id', $ward->id)->where('status', 'active')->count(),
            'admitted' => Patient::query()->where('ward_id', $ward->id)->where('status', 'admitted')->count(),
            'transferred' => Patient::query()->where('ward_id', $ward->id)->where('status', 'transferred')->count(),
            'discharged' => Patient::query()->where('ward_id', $ward->id)->where('status', 'discharged')->count(),
            'deceased' => Patient::query()->where('ward_id', $ward->id)->where('status', 'deceased')->count(),
        ]);

        $specialtySummary = Team::query()->orderBy('name')->get()->map(fn (Team $team): array => [
            'name' => $team->name,
            'patients' => Patient::query()->where('team_id', $team->id)->count(),
        ]);

        return view('reports.index', [
            'patients' => $patients,
            'wardSummary' => $wardSummary,
            'specialtySummary' => $specialtySummary,
            'wardOptions' => Ward::query()->orderBy('name')->get(['name']),
            'specialtyOptions' => Team::query()->orderBy('name')->get(['name']),
            'statusOptions' => Patient::query()->select('status')->distinct()->pluck('status'),
            'fromDate' => $fromDate?->format('Y-m-d'),
            'toDate' => $toDate?->format('Y-m-d'),
            'selectedWard' => $wardName,
            'selectedSpecialty' => $specialtyName,
            'selectedStatus' => $status,
            'totalPatients' => Patient::query()->count(),
            'activeCount' => Patient::query()->where('status', 'active')->count(),
            'admittedCount' => Patient::query()->where('status', 'admitted')->count(),
            'dischargedCount' => Patient::query()->where('status', 'discharged')->count(),
            'deceasedCount' => Patient::query()->where('status', 'deceased')->count(),
        ]);
    }

    private function normalizeReportFilter(Stringable|string|null $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);
        $clean = strtolower($stringValue);

        if ($clean === '' || str_starts_with($clean, 'all')) {
            return null;
        }

        return $stringValue;
    }
}
