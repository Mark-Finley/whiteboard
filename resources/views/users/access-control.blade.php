@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Access Control</h3>
            <div class="muted-label">Assign user roles, duties, wards, and account status from one place.</div>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">New user</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Duty</th>
                    <th>Ward</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @php($formId = 'access-form-'.$user->id)
                    @php($wardClass = strtolower(str_replace(' ', '-', $user->ward?->name ?? 'unassigned')))
                    <tr class="patient-ward-row patient-ward-row-{{ $wardClass }}">
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td style="min-width: 220px;">
                            <select name="role_id" form="{{ $formId }}" class="form-select form-select-sm" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected($user->role_id == $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="min-width: 220px;">
                            <select name="team_id" form="{{ $formId }}" class="form-select form-select-sm">
                                <option value="">No duty</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" @selected($user->team_id == $team->id)>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="min-width: 220px;">
                            <select name="ward_id" form="{{ $formId }}" class="form-select form-select-sm">
                                <option value="">No ward</option>
                                @foreach($wards as $ward)
                                    <option value="{{ $ward->id }}" @selected($user->ward_id == $ward->id)>{{ $ward->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="min-width: 160px;">
                            <select name="status" form="{{ $formId }}" class="form-select form-select-sm" required>
                                <option value="active" @selected($user->status === 'active')>Active</option>
                                <option value="inactive" @selected($user->status === 'inactive')>Inactive</option>
                            </select>
                        </td>
                        <td class="text-nowrap">
                            <form id="{{ $formId }}" action="{{ route('users.update', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="phone" value="{{ $user->phone }}">
                                <input type="hidden" name="password" value="">
                                <input type="hidden" name="password_confirmation" value="">
                                <button type="submit" class="btn btn-sm btn-primary">Save access</button>
                            </form>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Full edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
