@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">API</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <a href="{{ route('api.audits.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">
  <div class="row row-deck row-cards">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header"><h3 class="card-title">Ringkasan</h3></div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-4">Waktu</dt><dd class="col-8">{{ optional($apiAuditLog->created_at)->translatedFormat('d M Y H:i:s') }}</dd>
            <dt class="col-4">Client</dt><dd class="col-8">{{ $apiAuditLog->client_name ?? '-' }}</dd>
            <dt class="col-4">Token</dt><dd class="col-8">{{ $apiAuditLog->token_name ?? '-' }}</dd>
            <dt class="col-4">Route</dt><dd class="col-8">{{ $apiAuditLog->route_name ?? '-' }}</dd>
            <dt class="col-4">Method</dt><dd class="col-8">{{ $apiAuditLog->method }}</dd>
            <dt class="col-4">Path</dt><dd class="col-8">{{ $apiAuditLog->path }}</dd>
            <dt class="col-4">Status</dt><dd class="col-8">{{ $apiAuditLog->status_code }}</dd>
            <dt class="col-4">Durasi</dt><dd class="col-8">{{ $apiAuditLog->duration_ms }} ms</dd>
            <dt class="col-4">User</dt><dd class="col-8">{{ $apiAuditLog->user_id ?? '-' }}</dd>
            <dt class="col-4">IP</dt><dd class="col-8">{{ $apiAuditLog->ip_address ?? '-' }}</dd>
            <dt class="col-4">User Agent</dt><dd class="col-8 small">{{ $apiAuditLog->user_agent ?? '-' }}</dd>
            <dt class="col-4">Error</dt><dd class="col-8 text-danger">{{ $apiAuditLog->error_message ?? '-' }}</dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">Query Params</h3></div>
        <div class="card-body"><pre class="mb-0 small">{{ json_encode($apiAuditLog->query_params ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre></div>
      </div>
      <div class="card">
        <div class="card-header"><h3 class="card-title">Request Payload</h3></div>
        <div class="card-body"><pre class="mb-0 small">{{ json_encode($apiAuditLog->request_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre></div>
      </div>
      <div class="card mt-3">
        <div class="card-header"><h3 class="card-title">Response Excerpt</h3></div>
        <div class="card-body"><pre class="mb-0 small">{{ $apiAuditLog->response_excerpt ?? '-' }}</pre></div>
      </div>
    </div>
  </div>
</div>
@endsection