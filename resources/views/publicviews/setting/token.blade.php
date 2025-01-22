@extends('layouts.tablerpublic')
@section('content')
<!-- Page body -->

<div class="card">
  <div class="row g-0">
    <div class="col-12 col-md-3 border-end">
      <div class="card-body">
        <h4 class="subheader">Settings</h4>
        <div class="list-group list-group-transparent">
          <a href="{{ url('/setting') }}" class="list-group-item list-group-item-action d-flex align-items-center">My Account</a>
          <a href="{{ url('/token') }}" class="list-group-item list-group-item-action d-flex align-items-center active">Token</a>

        </div>

      </div>
    </div>
    <div class="col-12 col-md-9 d-flex flex-column">
      <form action="{{ url('/generate') }}" method="post">
        @csrf
      <div class="card-body">
        <h3 class="card-title">Project ID</h3>
          <p class="card-subtitle">Used when interacting with the API.</p>
          <p class="card-subtitle">Experied at. {{ $tokenuser->expires_at }}</p>
            <div class="input-icon">
               <input type="text" value="{{ $tokenuser->token }}" class="form-control" placeholder="Search…" readonly="">
               <input type="hidden" value="{{ auth()->user()->email}}" name="email">
                 <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/files -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 3v4a1 1 0 0 0 1 1h4"></path><path d="M18 17h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h4l5 5v7a2 2 0 0 1 -2 2z"></path><path d="M16 17v2a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h2"></path></svg>
                  </span>
              </div>
          </div>
          <div class="card-footer">
            <div class="row align-items-center">
              <div class="col">Learn more about <a href="#">Project ID</a></div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-primary">
                    Generate
                  </button>
                </div>
            </div>
        </div>
      </form>
      </div>
  </div>

  @endsection