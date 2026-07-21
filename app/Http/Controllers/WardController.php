<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class WardController extends Controller
{
    public function index(): View
    {
        return view('wards.index', [
            'wards' => Ward::query()->orderBy('name')->get(),
            'colorOptions' => Ward::colorPresets(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:wards,name'],
            'color_code' => ['required', 'string', Rule::in(Ward::colorCodes())],
        ]);

        $validated['color_code'] = Ward::colorForName($validated['name']);

        Ward::create($validated);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'ward_created',
            'description' => sprintf('Ward %s created', $validated['name']),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Ward created successfully.');
    }

    public function update(Request $request, Ward $ward): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:wards,name,' . $ward->id],
            'color_code' => ['required', 'string', Rule::in(Ward::colorCodes())],
        ]);

        $validated['color_code'] = Ward::colorForName($validated['name']);

        $ward->update($validated);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'ward_updated',
            'description' => sprintf('Ward %s updated', $ward->name),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Ward updated successfully.');
    }

    public function destroy(Request $request, Ward $ward): RedirectResponse
    {
        $wardName = $ward->name;
        $ward->delete();

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'ward_deleted',
            'description' => sprintf('Ward %s deleted', $wardName),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Ward deleted successfully.');
    }
}
