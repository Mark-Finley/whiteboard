@php($currentUser = $user ?? null)

<form method="POST" action="{{ $action }}" class="row g-3">
    @csrf
    @if(($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" value="{{ old('name', $currentUser?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $currentUser?->email) }}" class="form-control @error('email') is-invalid @enderror" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $currentUser?->phone) }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Role</label>
        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
            <option value="">Select role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $currentUser?->role_id) == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Duty / Specialty Team</label>
        <select name="team_id" class="form-select @error('team_id') is-invalid @enderror">
            <option value="">Optional</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected(old('team_id', $currentUser?->team_id) == $team->id)>{{ $team->name }}</option>
            @endforeach
        </select>
        @error('team_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Ward</label>
        <select name="ward_id" class="form-select @error('ward_id') is-invalid @enderror">
            <option value="">Optional</option>
            @foreach($wards as $ward)
                <option value="{{ $ward->id }}" @selected(old('ward_id', $currentUser?->ward_id) == $ward->id)>{{ $ward->name }}</option>
            @endforeach
        </select>
        @error('ward_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" @selected(old('status', $currentUser?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $currentUser?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Password {{ $currentUser ? '(leave blank to keep current)' : '' }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $currentUser ? '' : 'required' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">{{ $submitLabel ?? 'Save User' }}</button>
        <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
