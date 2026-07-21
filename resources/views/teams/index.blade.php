@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card section-card h-100">
            <h3 class="fw-bold mb-3">Create specialty team</h3>
            <form action="{{ route('teams.store') }}" method="POST" class="vstack gap-3">
                @csrf
                <div>
                    <label class="form-label">Team name</label>
                    <input type="text" name="name" class="form-control" placeholder="Emergency Medicine" required>
                </div>
                <button class="btn btn-primary" type="submit">Save team</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card section-card">
            <h3 class="fw-bold mb-3">Specialty teams</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teams as $team)
                            <tr>
                                <td class="fw-semibold">{{ $team->name }}</td>
                                <td class="text-nowrap">
                                    <form action="{{ route('teams.update', $team) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $team->name }}" class="form-control form-control-sm" style="width: 220px;">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                                    </form>
                                    <form action="{{ route('teams.destroy', $team) }}" method="POST" class="d-inline" data-confirm="Delete this team?">
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
        </div>
    </div>
</div>
@endsection
