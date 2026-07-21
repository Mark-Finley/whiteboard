<?php

declare(strict_types=1);

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\InvestigationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/overview', [DashboardController::class, 'overview'])->name('overview.dashboard');
    Route::get('/white-board', [DashboardController::class, 'whiteBoard'])->name('white.board');
    Route::get('/procedures-board', [DashboardController::class, 'proceduresBoard'])->name('procedures.board');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::get('/api/procedures-board', [DashboardController::class, 'proceduresBoardData'])->name('api.procedures.board');

    Route::get('/api/red-patients', [DashboardController::class, 'redPatients'])->name('api.red-patients');
    Route::get('/api/orange-patients', [DashboardController::class, 'orangePatients'])->name('api.orange-patients');
    Route::get('/api/yellow-patients', [DashboardController::class, 'yellowPatients'])->name('api.yellow-patients');
    Route::get('/api/triage-patients', [DashboardController::class, 'triagePatients'])->name('api.triage-patients');
    Route::get('/api/team-patients/{team}', [DashboardController::class, 'teamPatients'])->where('team', '.*')->name('api.team-patients');

    Route::middleware('admin')->group(function (): void {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/admin/access-control', [UserController::class, 'accessControl'])->name('users.access-control');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/wards', [WardController::class, 'index'])->name('wards.index');
        Route::post('/wards', [WardController::class, 'store'])->name('wards.store');
        Route::put('/wards/{ward}', [WardController::class, 'update'])->name('wards.update');
        Route::delete('/wards/{ward}', [WardController::class, 'destroy'])->name('wards.destroy');

        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

        Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
    });

    Route::middleware('triage')->group(function (): void {
        Route::get('/triage/dashboard', [DashboardController::class, 'triage'])->name('triage.dashboard');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::post('/patients/{patient}/cancel', [PatientController::class, 'cancel'])->name('patients.cancel');
        Route::post('/patients/{patient}/redo', [PatientController::class, 'redo'])->name('patients.redo');
        Route::post('/patients/{patient}/movements', [MovementController::class, 'store'])->name('patients.move');
        Route::post('/patients/{patient}/discharge', [MovementController::class, 'discharge'])->name('patients.discharge');
        Route::post('/patients/{patient}/admit', [PatientController::class, 'admit'])->name('patients.admit');
        Route::post('/patients/{patient}/deceased', [PatientController::class, 'markDeceased'])->name('patients.deceased');
    });

    // Notes endpoint accessible to authorized users (policy enforced)
    Route::post('/patients/{patient}/notes', [PatientController::class, 'saveNotes'])->name('patients.notes');

    Route::post('/patients/{patient}/investigations', [InvestigationController::class, 'store'])->name('patients.investigations.store');
    Route::post('/investigations/{investigation}/status', [InvestigationController::class, 'updateStatus'])->name('investigations.status.update');
    Route::get('/api/notifications/unread', [InvestigationController::class, 'getNotifications'])->name('api.notifications.unread');
    Route::post('/api/notifications/{notification}/read', [InvestigationController::class, 'markRead'])->name('api.notifications.read');
    Route::post('/api/notifications/read-all', [InvestigationController::class, 'markAllRead'])->name('api.notifications.read-all');

    Route::middleware('ward')->group(function (): void {
        Route::get('/wards/{ward}', [DashboardController::class, 'ward'])->name('ward.dashboard');
    });

    Route::middleware('team')->group(function (): void {
        Route::get('/specialties/{team}', [DashboardController::class, 'specialty'])->where('team', '.*')->name('specialty.dashboard');
    });
});
