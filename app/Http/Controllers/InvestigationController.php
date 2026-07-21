<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\PatientInvestigation;
use App\Models\PatientInvestigationUpdate;
use App\Models\SystemNotification;
use App\Models\InvestigationCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvestigationController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse|JsonResponse
    {
        if (!$request->user() || !$request->user()->canAssignProcedures()) {
            abort(403, 'Unauthorized to assign investigations.');
        }

        // Preprocess custom selection
        $type = $request->input('investigation_type_select');
        if (str_contains((string)$type, 'Custom') && $request->filled('custom_investigation_type')) {
            $type = $request->input('custom_investigation_type');
        }
        $request->merge(['investigation_type' => $type]);

        $validated = $request->validate([
            'investigation_type' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['Laboratory', 'Imaging', 'Procedures'])],
            'priority' => ['required', Rule::in(['Routine', 'Urgent', 'Stat'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $investigation = DB::transaction(function () use ($request, $patient, $validated) {
            $inv = PatientInvestigation::create([
                'patient_id' => $patient->id,
                'investigation_type' => $validated['investigation_type'],
                'category' => $validated['category'],
                'priority' => $validated['priority'],
                'status' => 'Pending',
                'notes' => $validated['notes'] ?? null,
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
            ]);

            PatientInvestigationUpdate::create([
                'patient_investigation_id' => $inv->id,
                'status' => 'Pending',
                'updated_by' => $request->user()->id,
                'comments' => 'Initial assignment',
                'created_at' => now(),
            ]);

            // Notifications
            $isUrgent = in_array($validated['priority'], ['Urgent', 'Stat'], true);
            $type = $isUrgent ? 'urgent' : 'assigned';
            $priorityLabel = $isUrgent ? 'URGENT ' : '';
            $message = sprintf(
                '%sInvestigation [%s] assigned to patient %s (%s) by %s',
                $priorityLabel,
                $validated['investigation_type'],
                $patient->patient_name,
                $patient->ghims_number,
                $request->user()->name
            );

            SystemNotification::create([
                'type' => $type,
                'patient_id' => $patient->id,
                'patient_investigation_id' => $inv->id,
                'message' => $message,
                'is_read' => false,
            ]);

            // Audit log
            AuditLog::create([
                'user_id' => $request->user()->id,
                'patient_id' => $patient->id,
                'ward_id' => $patient->ward_id,
                'team_id' => $patient->team_id,
                'action' => 'investigation_assigned',
                'description' => sprintf(
                    'Investigation [%s] (%s) assigned with %s priority by %s',
                    $validated['investigation_type'],
                    $validated['category'],
                    $validated['priority'],
                    $request->user()->name
                ),
                'ip_address' => $request->ip(),
            ]);

            return $inv;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Investigation assigned successfully.',
                'data' => $investigation,
            ]);
        }

        return back()->with('success', 'Investigation assigned successfully.');
    }

    public function updateStatus(Request $request, PatientInvestigation $investigation): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'Sample Taken', 'Sent', 'In Progress', 'Completed', 'Cancelled'])],
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($request, $investigation, $validated, $user) {
            $oldStatus = $investigation->status;
            $newStatus = $validated['status'];

            $updateData = [
                'status' => $newStatus,
            ];

            if ($newStatus === 'Completed') {
                $updateData['completed_by'] = $user->id;
                $updateData['completed_at'] = now();
            }

            $investigation->update($updateData);

            PatientInvestigationUpdate::create([
                'patient_investigation_id' => $investigation->id,
                'status' => $newStatus,
                'updated_by' => $user->id,
                'comments' => $validated['comments'] ?? sprintf('Status changed from %s to %s', $oldStatus, $newStatus),
                'created_at' => now(),
            ]);

            // Notification if marked Completed
            if ($newStatus === 'Completed') {
                SystemNotification::create([
                    'type' => 'completed',
                    'patient_id' => $investigation->patient_id,
                    'patient_investigation_id' => $investigation->id,
                    'message' => sprintf(
                        'Investigation [%s] for %s (%s) marked Completed by %s',
                        $investigation->investigation_type,
                        $investigation->patient->patient_name,
                        $investigation->patient->ghims_number,
                        $user->name
                    ),
                    'is_read' => false,
                ]);
            }

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'patient_id' => $investigation->patient_id,
                'ward_id' => $investigation->patient->ward_id,
                'team_id' => $investigation->patient->team_id,
                'action' => 'investigation_status_updated',
                'description' => sprintf(
                    'Investigation [%s] status changed from %s to %s by %s. Comments: %s',
                    $investigation->investigation_type,
                    $oldStatus,
                    $newStatus,
                    $user->name,
                    $validated['comments'] ?? 'None'
                ),
                'ip_address' => $request->ip(),
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        }

        return back()->with('success', 'Status updated successfully.');
    }

    public function getNotifications(): JsonResponse
    {
        $unreadCount = SystemNotification::query()->where('is_read', false)->count();
        $recentNotifications = SystemNotification::query()
            ->with(['patient'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recentNotifications->map(fn($notif) => [
                'id' => $notif->id,
                'type' => $notif->type,
                'message' => $notif->message,
                'is_read' => $notif->is_read,
                'created_at' => $notif->created_at->diffForHumans(),
                'patient_id' => $notif->patient_id,
            ]),
        ]);
    }

    public function markRead(SystemNotification $notification): JsonResponse
    {
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        SystemNotification::query()->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
