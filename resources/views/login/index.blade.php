@extends('layouts.tablerlogin')
@section('content')
<div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          
        </div>
        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4">Login to your account</h2>
            <form action="{{ url('/login') }}" method="post" autocomplete="off" novalidate="">
                @csrf
              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" autocomplete="off" name="email" autofocus required value="{{ old('email') }}">
                  @error('email')
                     <div class="invalid-feedback">
                        {{ $message }}
                     </div>
                  @enderror
              </div>
              <div class="mb-2">
                <label class="form-label">
                  Password
                  
                </label>
                <div class="input-group input-group-flat">
                  <input type="password" class="form-control" placeholder="Your password" autocomplete="off" name="password" >
                  <span class="input-group-text">
                    <a href="#" class="link-secondary" data-bs-toggle="tooltip" aria-label="Show password" data-bs-original-title="Show password"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path></svg>
                    </a>
                  </span>
                </div>
              </div>
              
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
              </div>
            </form>
          </div>
         
         
        </div>
        
      </div>
    </div>
    <script src="{{ asset('sweetalert2/js/sweetalert2.all.js') }}"></script>
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
@endsection