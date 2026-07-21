@extends('layouts.auth')

@section('content')
<div class="auth-card row g-0">
    <div class="col-lg-5 auth-hero d-flex flex-column justify-content-between">
        <div>
            <div class="brand-mark mb-4">KEPTS</div>
            <h1 class="display-6 fw-bold mb-3">KATH Emergency Live Patient Tracking System</h1>
            <p class="text-white-50 mb-0">
                Real-time emergency patient visibility for triage, ward teams, specialty doctors, and hospital command staff.
            </p>
        </div>

        <div class="mt-5 pt-4 border-top border-white border-opacity-25">
            <div class="d-flex gap-3 align-items-center mb-2">
                <i class="fa-solid fa-shield-heart fa-lg"></i>
                <strong>Secure internal access</strong>
            </div>
            <div class="small text-white-50">Role-based dashboards · live polling every 3 seconds · audit trails</div>
        </div>
    </div>

    <div class="col-lg-7 auth-form">
        <h2 class="fw-bold mb-2">Sign in</h2>
        <p class="text-muted mb-4">Use your hospital account to enter KEPTS.</p>

        <form method="POST" action="{{ route('login.submit') }}" class="vstack gap-3">
            @csrf
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="name@kath.gov.gh">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Login to KEPTS</button>
            <div class="text-center pt-2">
                <span class="text-muted">Need an account?</span>
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1">Sign up</a>
            </div>
        </form>
    </div>
</div>
@endsection
