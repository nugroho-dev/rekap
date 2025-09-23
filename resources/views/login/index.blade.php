@extends('layouts.tablerlogin')
@section('content')
<div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          
        </div>
        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4">Login to your account</h2>

            {{-- Fortify expects POST to route('login') and standard field names: email, password, remember --}}
            @if(session('status'))
              <div class="alert alert-success" role="alert">
                {{ session('status') }}
              </div>
            @endif

            @if(session('loginError'))
              <div class="alert alert-danger" role="alert">
                {{ session('loginError') }}
              </div>
            @endif

            <form id="loginForm" action="{{ route('login') }}" method="post" autocomplete="off" novalidate>
                @csrf

              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email"
                       name="email"
                       autocomplete="username"
                       autofocus
                       required
                       class="form-control @if($errors->has('email')) is-invalid @endif"
                       placeholder="your@email.com"
                       value="{{ old('email') }}">
                  @if($errors->has('email'))
                     <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                     </div>
                  @endif
              </div>

              <div class="mb-2">
                <label class="form-label">Password</label>
                <div class="input-group input-group-flat">
                  <input type="password"
                         name="password"
                         autocomplete="current-password"
                         class="form-control @if($errors->has('password')) is-invalid @endif"
                         placeholder="Your password">
                  <span class="input-group-text">
                    <a href="#" class="link-secondary" data-bs-toggle="tooltip" aria-label="Show password" data-bs-original-title="Show password">
                      <!-- svg -->
                    </a>
                  </span>
                </div>
                @if($errors->has('password'))
                  <div class="invalid-feedback d-block">
                    {{ $errors->first('password') }}
                  </div>
                @endif
              </div>

              <div class="mb-3 d-flex justify-content-between align-items-center">
                <label class="form-check mb-0">
                  <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                  <span class="form-check-label">Remember me</span>
                </label>

                {{-- Forgot password link (only show if route exists) --}}
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}" class="link-secondary small">Lupa password?</a>
                @endif
              </div>
              
              <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.sitekey') }}"></div>
                @if(session('recaptcha_error'))
                    <div class="text-danger small mt-1">{{ session('recaptcha_error') }}</div>
                @endif
              </div>

              <div class="form-footer">
                <button id="submitBtn" type="submit" class="btn btn-primary w-100">Sign in</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="{{ asset('sweetalert2/js/sweetalert2.all.js') }}"></script>

    {{-- keep old session toast if used elsewhere --}}
    @if(session()->has('loginError'))
    <script>
        Swal.fire({
                title: 'Kesalahan',
                text: '{{ session('loginError') }}',
                icon: 'error',
                confirmButtonText: 'Tutup',
                showConfirmButton: true,
                timer: 6500
        })
    </script>
     @endif

    {{-- load reCAPTCHA script --}}
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function (e) {
            const token = document.querySelector('[name="g-recaptcha-response"]')?.value || '';
            if (! token.trim()) {
                e.preventDefault();
                // tampilkan pesan langsung
                alert('Silakan isi reCAPTCHA terlebih dahulu.');
                if (window.grecaptcha && typeof grecaptcha.reset === 'function') grecaptcha.reset();
                return;
            }
            // disable tombol untuk cegah double submit
            submitBtn.disabled = true;
        });

        // jika server mengembalikan recaptcha_error (timeout-or-duplicate), reset widget agar user dapat mencoba lagi
        @if(session('recaptcha_error'))
            if (window.grecaptcha && typeof grecaptcha.reset === 'function') grecaptcha.reset();
        @endif
    });
    </script>
@endsection