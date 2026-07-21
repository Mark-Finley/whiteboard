@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card section-card h-100">
            <h3 class="fw-bold mb-3">Create ward</h3>
            <form action="{{ route('wards.store') }}" method="POST" class="vstack gap-3">
                @csrf
                <div>
                    <label class="form-label">Ward name</label>
                    <input type="text" name="name" class="form-control" placeholder="RED" required>
                </div>
                <div>
                    <label class="form-label">Color code</label>
                    <select name="color_code" class="form-select" required>
                        <option value="">Choose a ward color</option>
                        @foreach($colorOptions as $label => $colorCode)
                            <option value="{{ $colorCode }}">{{ $label }} ({{ $colorCode }})</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary" type="submit">Save ward</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card section-card">
            <h3 class="fw-bold mb-3">Wards</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wards as $ward)
                            <tr>
                                <td class="fw-semibold">{{ $ward->name }}</td>
                                <td><span class="badge" style="background: {{ $ward->color_code }};">{{ $ward->color_code }}</span></td>
                                <td class="text-nowrap">
                                    <form action="{{ route('wards.update', $ward) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $ward->name }}" class="form-control form-control-sm" style="width: 130px;">
                                        <select name="color_code" class="form-select form-select-sm" style="width: 180px;">
                                            @foreach($colorOptions as $label => $colorCode)
                                                <option value="{{ $colorCode }}" @selected($ward->color_code === $colorCode)>{{ $label }} ({{ $colorCode }})</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                                    </form>
                                    <form action="{{ route('wards.destroy', $ward) }}" method="POST" class="d-inline" data-confirm="Delete this ward?">
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
