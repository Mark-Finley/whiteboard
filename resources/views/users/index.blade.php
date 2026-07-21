@extends('layouts.app')

@section('content')
<div class="card section-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Users</h3>
            <div class="muted-label">Manage hospital staff access, duties, and ward assignments.</div>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">New user</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Duty</th>
                    <th>Ward</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @php($wardClass = strtolower(str_replace(' ', '-', $user->ward?->name ?? 'unassigned')))
                    <tr class="patient-ward-row patient-ward-row-{{ $wardClass }}">
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>{{ $user->role?->name ?? '-' }}</td>
                        <td>{{ $user->team?->name ?? '-' }}</td>
                        <td>
                            @if($user->ward)
                                <span class="badge" style="background: {{ $user->ward->color_code }}; color: #fff;">{{ $user->ward->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge {{ $user->status === 'active' ? 'badge-soft-green' : 'badge-soft-gray' }}">{{ ucfirst($user->status) }}</span></td>
                        <td class="text-nowrap">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" data-confirm="Delete this user account?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection
