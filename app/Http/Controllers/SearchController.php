<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = trim((string) $request->input('query', ''));

        if ($query === '') {
            $empty = collect();

            return $request->wantsJson()
                ? response()->json(['data' => $empty])
                : view('search.index', ['query' => $query, 'results' => $empty]);
        }

        $results = Patient::query()
            ->with(['ward', 'team'])
            ->where('ghims_number', 'like', "%{$query}%")
            ->orWhere('patient_name', 'like', "%{$query}%")
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $results->map(fn (Patient $patient): array => [
                    'ghims_number' => $patient->ghims_number,
                    'patient_name' => $patient->patient_name,
                    'current_ward' => $patient->ward?->name,
                    'specialty_team' => $patient->team?->name,
                    'chief_complaint' => $patient->chief_complaint,
                    'time_in' => optional($patient->time_in)->format('Y-m-d H:i'),
                    'status' => $patient->status,
                ]),
            ]);
        }

        return view('search.index', compact('query', 'results'));
    }
}
