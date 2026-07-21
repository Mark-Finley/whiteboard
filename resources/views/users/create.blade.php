@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Create user</h3>
        <div class="muted-label">Provision a secure KEPTS account.</div>
    </div>

    @include('users._form', [
        'action' => route('users.store'),
        'method' => 'POST',
        'user' => null,
        'submitLabel' => 'Create user',
        'roles' => $roles,
        'teams' => $teams,
        'wards' => $wards,
    ])
</div>
@endsection
