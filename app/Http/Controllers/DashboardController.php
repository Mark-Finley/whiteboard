<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Team;
use App\Models\Ward;
use App\Models\InvestigationCatalog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match (true) {
            $user?->isAdmin() => redirect()->route('admin.dashboard'),
            $user?->isTriage() => redirect()->route('triage.dashboard'),
            $user?->isWard() => redirect()->route('ward.dashboard', ['ward' => 'RED']),
            $user?->isSpecialtyDoctor() => redirect()->route('specialty.dashboard', ['team' => $user->team?->name ?? 'Emergency Medicine']),
            default => redirect()->route('login'),
        };
    }

    public function admin(Request $request): View
    {
        $query = Patient::query()->with(['ward', 'team', 'teams']);

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('ward')) {
            $query->whereHas('ward', fn ($wardQuery) => $wardQuery->where('name', $request->string('ward')));
        }

        if ($request->filled('specialty')) {
            $spec = $request->string('specialty');
            $query->where(function ($q) use ($spec) {
                $q->whereHas('team', fn ($teamQuery) => $teamQuery->where('name', $spec))
                  ->orWhereHas('teams', fn ($teamQuery) => $teamQuery->where('name', $spec));
            });
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($patientQuery) use ($search): void {
                $patientQuery->where('ghims_number', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        $wardCards = Ward::query()->orderBy('name')->get()->mapWithKeys(function (Ward $ward): array {
            return [$ward->name => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('ward_id', $ward->id)->count()];
        });

        $specialtyCards = Team::query()->orderBy('name')->get()->mapWithKeys(function (Team $team): array {
            return [$team->name => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('team_id', $team->id)->count()];
        });

        $wardDistribution = Ward::query()->orderBy('name')->get()->map(fn (Ward $ward): array => [
            'name' => $ward->name,
            'count' => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('ward_id', $ward->id)->count(),
            'color' => $ward->color_code,
        ]);

        $specialtyDistribution = Team::query()->orderBy('name')->get()->map(fn (Team $team): array => [
            'name' => $team->name,
            'count' => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('team_id', $team->id)->count(),
        ]);

        $dateLabels = [];
        $activityCreated = [];
        $activityDischarged = [];

        foreach (CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->endOfDay()) as $date) {
            $label = $date->format('Y-m-d');
            $dateLabels[] = $date->format('D');
            $activityCreated[] = Patient::query()->whereDate('created_at', $label)->count();
            $activityDischarged[] = Patient::query()->whereDate('time_out', $label)->where('status', 'discharged')->count();
        }

        return view('dashboard.admin', [
            'totalActivePatients' => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->count(),
            'redCount' => $wardCards->get('RED', 0),
            'orangeCount' => $wardCards->get('ORANGE', 0),
            'yellowCount' => $wardCards->get('YELLOW', 0),
            'triageCount' => $wardCards->get('TRIAGE HOLDING', 0),
            'deceasedCount' => Patient::query()->where('status', 'deceased')->count(),
            'dischargedToday' => Patient::query()->whereDate('time_out', today())->where('status', 'discharged')->count(),
            'admittedToday' => Patient::query()->whereDate('time_in', today())->where('status', 'admitted')->count(),
            'patients' => $patients,
            'wardCards' => $wardCards,
            'specialtyCards' => $specialtyCards,
            'wardOptions' => Ward::query()->orderBy('name')->get(['name']),
            'specialtyOptions' => Team::query()->orderBy('name')->get(['name']),
            'redColor' => Ward::RED_COLOR,
            'orangeColor' => Ward::ORANGE_COLOR,
            'yellowColor' => Ward::YELLOW_COLOR,
            'triageColor' => Ward::TRIAGE_COLOR,
            'wardDistribution' => $wardDistribution,
            'specialtyDistribution' => $specialtyDistribution,
            'activityLabels' => $dateLabels,
            'activityCreated' => $activityCreated,
            'activityDischarged' => $activityDischarged,
        ]);
    }

    public function overview(Request $request): View
    {
        $query = Patient::query()->with(['ward', 'team', 'teams']);

        if ($request->filled('ward')) {
            $query->whereHas('ward', fn ($wardQuery) => $wardQuery->where('name', $request->string('ward')));
        }

        if ($request->filled('specialty')) {
            $spec = $request->string('specialty');
            $query->where(function ($q) use ($spec) {
                $q->whereHas('team', fn ($teamQuery) => $teamQuery->where('name', $spec))
                  ->orWhereHas('teams', fn ($teamQuery) => $teamQuery->where('name', $spec));
            });
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($patientQuery) use ($search): void {
                $patientQuery->where('ghims_number', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        $wardCards = Ward::query()->orderBy('name')->get()->mapWithKeys(function (Ward $ward): array {
            return [$ward->name => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('ward_id', $ward->id)->count()];
        });

        $specialtyCards = Team::query()->orderBy('name')->get()->mapWithKeys(function (Team $team): array {
            return [$team->name => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('team_id', $team->id)->count()];
        });

        $wardDistribution = Ward::query()->orderBy('name')->get()->map(fn (Ward $ward): array => [
            'name' => $ward->name,
            'count' => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('ward_id', $ward->id)->count(),
            'color' => $ward->color_code,
        ]);

        $specialtyDistribution = Team::query()->orderBy('name')->get()->map(fn (Team $team): array => [
            'name' => $team->name,
            'count' => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->where('team_id', $team->id)->count(),
        ]);

        return view('dashboard.overview', [
            'totalActivePatients' => Patient::query()->whereIn('status', ['active', 'transferred', 'admitted'])->count(),
            'redCount' => $wardCards->get('RED', 0),
            'orangeCount' => $wardCards->get('ORANGE', 0),
            'yellowCount' => $wardCards->get('YELLOW', 0),
            'triageCount' => $wardCards->get('TRIAGE HOLDING', 0),
            'deceasedCount' => Patient::query()->where('status', 'deceased')->count(),
            'totalDischarged' => Patient::query()->where('status', 'discharged')->count(),
            'currentAdmitted' => Patient::query()->where('status', 'admitted')->count(),
            'patients' => $patients,
            'wardCards' => $wardCards,
            'specialtyCards' => $specialtyCards,
            'wardOptions' => Ward::query()->orderBy('name')->get(['name']),
            'specialtyOptions' => Team::query()->orderBy('name')->get(['name']),
            'redColor' => Ward::RED_COLOR,
            'orangeColor' => Ward::ORANGE_COLOR,
            'yellowColor' => Ward::YELLOW_COLOR,
            'triageColor' => Ward::TRIAGE_COLOR,
            'wardDistribution' => $wardDistribution,
            'specialtyDistribution' => $specialtyDistribution,
        ]);
    }

    public function whiteBoard(): View
    {
        $triagePatients = $this->serializePatients(Ward::TRIAGE_HOLDING);
        $redPatients = $this->serializePatients(Ward::RED);
        $orangePatients = $this->serializePatients(Ward::ORANGE);
        $yellowPatients = $this->serializePatients(Ward::YELLOW);

        return view('dashboard.white-board', [
            'title' => 'White Board',
            'triagePatients' => $triagePatients,
            'redPatients' => $redPatients,
            'orangePatients' => $orangePatients,
            'yellowPatients' => $yellowPatients,
        ]);
    }

    public function triage(): View
    {
        $patients = Patient::query()
            ->with(['ward', 'team', 'teams'])
            ->whereHas('ward', fn ($wardQuery) => $wardQuery->where('name', 'TRIAGE HOLDING'))
            ->whereIn('status', ['active', 'transferred', 'admitted'])
            ->latest('time_in')
            ->get();

        return view('dashboard.triage', [
            'title' => 'TRIAGE HOLDING',
            'patients' => $patients,
        ]);
    }

    public function ward(string $ward): View
    {
        $wardModel = Ward::query()->where('name', Auth::user()->ward->name)->firstOrFail();

        $patients = Patient::query()
            ->with(['ward', 'team', 'teams'])
            ->where('ward_id', $wardModel->id)
            ->whereIn('status', ['active', 'transferred', 'admitted'])
            ->latest('time_in')
            ->get();

        return view('dashboard.ward', [
            'ward' => $wardModel,
            'patients' => $patients,
        ]);
    }

    public function specialty(string $team): View
    {
        $teamModel = Team::query()->where('name', urldecode($team))->firstOrFail();

        $patients = Patient::query()
            ->with(['ward', 'team', 'teams'])
            ->where(function ($query) use ($teamModel) {
                $query->where('team_id', $teamModel->id)
                      ->orWhereHas('teams', fn ($q) => $q->where('teams.id', $teamModel->id));
            })
            ->whereIn('status', ['active', 'transferred', 'admitted'])
            ->latest('time_in')
            ->get();

        return view('dashboard.specialty', [
            'team' => $teamModel,
            'patients' => $patients,
        ]);
    }

    public function redPatients(): JsonResponse
    {
        return response()->json(['data' => $this->serializePatients('RED')]);
    }

    public function orangePatients(): JsonResponse
    {
        return response()->json(['data' => $this->serializePatients('ORANGE')]);
    }

    public function yellowPatients(): JsonResponse
    {
        return response()->json(['data' => $this->serializePatients('YELLOW')]);
    }

    public function triagePatients(): JsonResponse
    {
        return response()->json(['data' => $this->serializePatients('TRIAGE HOLDING')]);
    }

    public function teamPatients(string $team): JsonResponse
    {
        $teamModel = Team::query()->where('name', urldecode($team))->firstOrFail();

        $patients = Patient::query()
            ->with(['ward', 'team', 'teams', 'investigations.assignedBy', 'investigations.completedBy', 'investigations.updates.updatedBy'])
            ->where(function ($query) use ($teamModel) {
                $query->where('team_id', $teamModel->id)
                      ->orWhereHas('teams', fn ($q) => $q->where('teams.id', $teamModel->id));
            })
            ->whereIn('status', ['active', 'transferred', 'admitted'])
            ->orderBy('time_in')
            ->get();

        return response()->json(['data' => $patients->map(fn (Patient $patient) => $this->transformPatient($patient))]);
    }

    public function proceduresBoard(Request $request): View
    {
        $categories = InvestigationCatalog::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('dashboard.procedures-board', [
            'title' => 'Patient Procedures & Investigations Board',
            'categories' => $categories,
        ]);
    }

    public function proceduresBoardData(Request $request): JsonResponse
    {
        $patients = Patient::query()
            ->with(['ward', 'team', 'teams', 'investigations.assignedBy', 'investigations.completedBy', 'investigations.updates.updatedBy'])
            ->whereIn('status', ['active', 'transferred', 'admitted'])
            ->orderBy('time_in')
            ->get()
            ->map(fn (Patient $patient) => $this->transformPatient($patient));

        return response()->json(['data' => $patients]);
    }

    private function serializePatients(string $wardName)
    {
        return Patient::query()
            ->with(['ward', 'team', 'teams', 'investigations.assignedBy', 'investigations.completedBy', 'investigations.updates.updatedBy'])
            ->whereHas('ward', fn ($wardQuery) => $wardQuery->where('name', $wardName))
            ->whereIn('status', ['active', 'transferred', 'admitted'])
            ->orderBy('time_in')
            ->get()
            ->map(fn (Patient $patient) => $this->transformPatient($patient));
    }

    private function transformPatient(Patient $patient): array
    {
        return [
            'id' => $patient->id,
            'ghims_number' => $patient->ghims_number,
            'patient_name' => $patient->patient_name,
            'age' => $patient->age,
            'chief_complaint' => $patient->chief_complaint,
            'condition' => $patient->condition,
            'assigned_team' => $patient->teams->pluck('name')->join(', ') ?: ($patient->team?->name ?? 'Unassigned'),
            'current_ward' => $patient->ward?->name,
            'ward_color' => $patient->ward?->color_code ?? Ward::TRIAGE_COLOR,
            'time_in' => optional($patient->time_in)->format('d M Y, g:i a'),
            'nurse_notes' => $patient->nurse_notes,
            'ward_time' => $patient->ward_time_spent,
            'ward_time_breakdown' => collect($patient->cumulative_ward_times ?? [])->map(fn($v, $k) => sprintf('%s: %s', $k, $v))->values()->join('; '),
            'status' => $patient->status,
            'investigations' => $patient->investigations->map(fn($inv) => [
                'id' => $inv->id,
                'investigation_type' => $inv->investigation_type,
                'category' => $inv->category,
                'priority' => $inv->priority,
                'status' => $inv->status,
                'notes' => $inv->notes,
                'assigned_by_name' => $inv->assignedBy?->name ?? 'System',
                'assigned_at' => $inv->assigned_at ? $inv->assigned_at->format('d M Y, g:i a') : null,
                'completed_by_name' => $inv->completedBy?->name ?? null,
                'completed_at' => $inv->completed_at ? $inv->completed_at->format('d M Y, g:i a') : null,
                'updates' => $inv->updates->map(fn($u) => [
                    'status' => $u->status,
                    'updated_by_name' => $u->updatedBy?->name ?? 'System',
                    'comments' => $u->comments,
                    'created_at' => $u->created_at->format('d M Y, g:i a'),
                ])->toArray(),
            ])->toArray(),
        ];
    }
}

