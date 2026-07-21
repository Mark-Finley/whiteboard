@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit user</h3>
        <div class="muted-label">Update account details and role access.</div>
    </div>

    @include('users._form', [
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'user' => $user,
        'submitLabel' => 'Update user',
        'roles' => $roles,
        'teams' => $teams,
        'wards' => $wards,
    ])
</div>
@endsection
