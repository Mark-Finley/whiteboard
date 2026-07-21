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
                    <div class="input-group">
                        <input type="color" name="color_code" class="form-control form-control-color" style="width: 80px; cursor: pointer;" value="#6b7280" required title="Click to choose a color">
                        <input type="text" class="form-control" placeholder="#000000" title="Enter hex color code (e.g., #dc2626)" readonly>
                    </div>
                    <small class="text-muted d-block mt-2">Quick presets:</small>
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        @foreach($colorOptions as $label => $colorCode)
                            <button type="button" class="btn btn-sm" style="background: {{ $colorCode }}; color: #fff; border: 2px solid {{ $colorCode }};" onclick="document.querySelector('input[name=color_code]').value='{{ $colorCode }}'; document.querySelector('input[type=text]').value='{{ $colorCode }}';" title="{{ $label }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
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
                                        <input type="color" name="color_code" value="{{ $ward->color_code }}" class="form-control form-control-color form-control-sm" style="width: 80px; cursor: pointer;" title="Click to choose a color">
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

@push('scripts')
<script>
    // Update hex display when color picker changes
    document.querySelectorAll('input[type="color"][name="color_code"]').forEach(colorInput => {
        const updateHexDisplay = () => {
            const textInput = colorInput.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = colorInput.value.toUpperCase();
            }
        };
        colorInput.addEventListener('change', updateHexDisplay);
        colorInput.addEventListener('input', updateHexDisplay);
        // Set initial value
        updateHexDisplay();
    });
</script>
@endpush

@endsection
