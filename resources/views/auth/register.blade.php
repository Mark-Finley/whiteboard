@extends('layouts.auth')

@section('content')
<div class="auth-card row g-0">
    <div class="col-lg-5 auth-hero d-flex flex-column justify-content-between">
        <div>
            <div class="brand-mark mb-4">KEPTS</div>
            <h1 class="display-6 fw-bold mb-3">Create your hospital account</h1>
            <p class="text-white-50 mb-0">
                Hospital staff with a valid @kath.gov.gh email can request access to the KEPTS internal system.
            </p>
        </div>

        <div class="mt-5 pt-4 border-top border-white border-opacity-25">
            <div class="small text-white-50">Account requests are tied to your hospital domain and role defaults.</div>
        </div>
    </div>

    <div class="col-lg-7 auth-form">
        <h2 class="fw-bold mb-2">Sign up</h2>
        <p class="text-muted mb-4">Use your hospital email address to create an account.</p>

        <form method="POST" action="{{ route('register.submit') }}" class="vstack gap-3">
            @csrf
            <div>
                <label class="form-label">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Your full name">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="name@kath.gov.gh">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg @error('phone') is-invalid @enderror" placeholder="Phone number">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Team</label>
                <select name="team_id" class="form-select form-select-lg @error('team_id') is-invalid @enderror">
                    <option value="">Select your team</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('team_id') == $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
                @error('team_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Ward</label>
                <select name="ward_id" class="form-select form-select-lg @error('ward_id') is-invalid @enderror">
                    <option value="">Select your ward</option>
                    @foreach($wards as $ward)
                        <option value="{{ $ward->id }}" @selected(old('ward_id') == $ward->id)>{{ $ward->name }}</option>
                    @endforeach
                </select>
                @error('ward_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirm password">
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Create account</button>
            <div class="text-center pt-2">
                <span class="text-muted">Already have an account?</span>
                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1">Sign in</a>
            </div>
        </form>
    </div>
</div>
@endsection
