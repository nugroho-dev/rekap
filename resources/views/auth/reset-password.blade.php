
@extends('layouts.tablerlogin')

@section('content')
<div class="page page-center">
  <div class="container container-tight py-4">
    <div class="card card-md">
      <div class="card-body">
        <h2 class="h2 text-center mb-4">Reset Password</h2>

        @if(session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
          @csrf

          <input type="hidden" name="token" value="{{ old('token', request()->route('token')) }}">

          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" value="{{ old('email', request('email')) }}" required autocomplete="username" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">New password</label>
            <input type="password" name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" placeholder="New password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-control" placeholder="Confirm password">
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
          </div>
        </form>

        <div class="text-center mt-3">
          <a href="{{ route('login') }}" class="link-secondary">Back to login</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection