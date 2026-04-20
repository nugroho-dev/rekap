@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">API</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">
  <div class="row row-deck row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body"><div class="text-muted">Total Request</div><div class="h1 mb-0">{{ $summary['total_requests'] }}</div></div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body"><div class="text-muted">Sukses</div><div class="h1 mb-0 text-success">{{ $summary['success_requests'] }}</div></div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body"><div class="text-muted">Error</div><div class="h1 mb-0 text-danger">{{ $summary['error_requests'] }}</div></div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body"><div class="text-muted">Rata-rata Durasi</div><div class="h1 mb-0">{{ $summary['average_duration'] }} <span class="fs-4">ms</span></div></div></div>
    </div>
  </div>

  <div class="row row-deck row-cards mb-3">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title">Top Client</h3></div>
        <div class="table-responsive">
          <table class="table table-sm card-table">
            <tbody>
              @forelse($summary['top_clients'] as $client)
                <tr><td>{{ $client->client_name }}</td><td class="text-end">{{ $client->total }}</td></tr>
              @empty
                <tr><td colspan="2" class="text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title">Status Code</h3></div>
        <div class="table-responsive">
          <table class="table table-sm card-table">
            <tbody>
              @forelse($summary['status_breakdown'] as $status)
                <tr><td>{{ $status->status_code }}</td><td class="text-end">{{ $status->total }}</td></tr>
              @empty
                <tr><td colspan="2" class="text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title">Request Per Hari</h3></div>
        <div class="table-responsive">
          <table class="table table-sm card-table">
            <tbody>
              @forelse($summary['daily_counts'] as $tanggal => $total)
                <tr><td>{{ $tanggal }}</td><td class="text-end">{{ $total }}</td></tr>
              @empty
                <tr><td colspan="2" class="text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="get" class="row g-2">
        <div class="col-md-2">
          <input type="text" name="client" class="form-control" placeholder="Client" value="{{ request('client') }}">
        </div>
        <div class="col-md-2">
          <select name="group" class="form-select">
            <option value="">Semua Group</option>
            @foreach($groups as $group)
              <option value="{{ $group }}" @selected(request('group') === $group)>{{ $group }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1">
          <select name="method" class="form-select">
            <option value="">Semua Method</option>
            @foreach(['GET','POST','PUT','PATCH','DELETE'] as $method)
              <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1">
          <input type="number" name="status" class="form-control" placeholder="Status" value="{{ request('status') }}">
        </div>
        <div class="col-md-2">
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Filter</button>
          <a href="{{ route('api.audits.index') }}" class="btn btn-secondary">Reset</a>
        </div>
      </form>
      @can('api.audit.export')
        <div class="mt-3">
          <a href="{{ route('api.audits.export', request()->query()) }}" class="btn btn-outline-primary">Export CSV</a>
        </div>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Riwayat Akses API</h3>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Waktu</th>
            <th>Client</th>
            <th>Group</th>
            <th>Method</th>
            <th>Path</th>
            <th>Status</th>
            <th>Durasi</th>
            <th>User</th>
            <th>IP</th>
            <th>Error</th>
            <th>Detail</th>
          </tr>
        </thead>
        <tbody>
          @forelse($entries as $entry)
            <tr>
              <td>{{ optional($entry->created_at)->translatedFormat('d M Y H:i:s') }}</td>
              <td>
                <div>{{ $entry->client_name ?? '-' }}</div>
                <div class="text-muted small">{{ $entry->token_name ?? '-' }}</div>
              </td>
              <td>{{ $entry->api_group }}</td>
              <td><span class="badge bg-{{ $entry->method === 'GET' ? 'blue' : 'orange' }}">{{ $entry->method }}</span></td>
              <td class="text-muted small">{{ $entry->path }}</td>
              <td>{{ $entry->status_code }}</td>
              <td>{{ $entry->duration_ms }} ms</td>
              <td>{{ $entry->user_id ?? '-' }}</td>
              <td>{{ $entry->ip_address ?? '-' }}</td>
              <td class="text-danger small">{{ $entry->error_message ? \Illuminate\Support\Str::limit($entry->error_message, 80) : '-' }}</td>
              <td><a href="{{ route('api.audits.show', $entry) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-muted">Belum ada log API.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">
      {{ $entries->links() }}
    </div>
  </div>
</div>
@endsection