@extends('layouts.tablerpublic')
@section('content')
  
  <style>
    .category-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
      padding: 1rem 1.25rem !important;
      font-size: 1.1rem;
      letter-spacing: 0.3px;
      border-left: 4px solid #ffd700;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .table-header-row {
      background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 2px solid #dee2e6;
    }
    
    .table-header-row th {
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      padding: 0.875rem 0.75rem !important;
      color: #495057;
      border-bottom: 2px solid #dee2e6 !important;
    }
    
    .data-row {
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }
    
    .data-row:hover {
      background-color: #f8f9fa !important;
      border-left-color: #667eea;
      transform: translateX(2px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .data-row td {
      padding: 1rem 0.75rem !important;
      vertical-align: middle;
    }
    
    .info-label {
      font-weight: 600;
      color: #2c3e50;
      font-size: 0.95rem;
    }
    
    .status-badge {
      padding: 0.4rem 0.9rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .status-badge-success {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      color: white;
    }
    
    .status-badge-warning {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }
    
    .status-badge-danger {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      color: white;
    }
    
    .status-badge-secondary {
      background: linear-gradient(135deg, #a8a8a8 0%, #c0c0c0 100%);
      color: white;
    }
    
    .date-text {
      color: #6c757d;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .data-count {
      font-weight: 700;
      font-size: 1.05rem;
      color: #667eea;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .action-btn {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 500;
      transition: all 0.2s ease;
      border: 2px solid;
    }
    
    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-available {
      border-color: #10b981;
      color: #10b981;
      background: white;
    }
    
    .btn-available:hover {
      background: #10b981;
      color: white;
    }
    
    .btn-stats {
      border-color: #3b82f6;
      color: #3b82f6;
      background: white;
    }
    
    .btn-stats:hover {
      background: #3b82f6;
      color: white;
    }
    
    .btn-unavailable {
      border-color: #d1d5db;
      color: #9ca3af;
      background: #f9fafb;
      cursor: not-allowed;
    }
    
    .empty-state {
      padding: 2rem;
      color: #9ca3af;
      font-style: italic;
    }
    
    .modern-card {
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.05);
      border: none;
      overflow: hidden;
    }
    
    .status-icon {
      font-size: 0.7rem;
    }
  </style>
  
  <div class="col-12">
    <div class="card modern-card">
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap mb-0">
          @foreach($kategori as $kat)
            <thead>
              <tr>
                <th colspan="6" class="category-header">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2" style="display: inline-block; vertical-align: middle;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12l0 .01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
                  {{ $kat->nama }}
                </th>
              </tr>
              <tr class="table-header-row">
                <th class="text-uppercase" style="width: 25%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>
                  Jenis Informasi
                </th>
                <th class="text-uppercase text-center" style="width: 15%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4l2 2" /></svg>
                  Status Update
                </th>
                <th class="text-uppercase text-center" style="width: 15%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                  Tanggal Update
                </th>
                <th class="text-uppercase text-center" style="width: 12%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                  Jumlah Data
                </th>
                <th class="text-uppercase text-center" style="width: 13%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
                  Data Set
                </th>
                <th class="text-uppercase text-center" style="width: 13%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                  Statistik
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse($kat->jenisInformasi as $jenis)
                <tr class="data-row">
                  <td>
                    <div class="info-label">{{ $jenis->label }}</div>
                  </td>
                  <td class="text-center">
                    @if($jenis->updated_model_at)
                      @php
                        $updated = \Carbon\Carbon::parse($jenis->updated_model_at);
                        $now = \Carbon\Carbon::now();
                        $diffDays = $updated->diffInDays($now);
                        if ($diffDays < 14) {
                          $badge = 'success';
                          $icon = '✓';
                        } elseif ($diffDays < 30) {
                          $badge = 'warning';
                          $icon = '⚠';
                        } else {
                          $badge = 'danger';
                          $icon = '⚠';
                        }
                      @endphp
                      <span class="status-badge status-badge-{{ $badge }}">
                        <span class="status-icon">{{ $icon }}</span>
                        {{ $updated->diffForHumans() }}
                      </span>
                    @else
                      <span class="status-badge status-badge-secondary">
                        <span class="status-icon">•</span>
                        Belum diupdate
                      </span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="date-text">
                      {{ $jenis->updated_model_at ? \Carbon\Carbon::parse($jenis->updated_model_at)->format('d M Y H:i') : '-' }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="data-count">{{ number_format($jenis->jumlah ?? 0) }}</span>
                    <span class="text-muted" style="font-size: 0.8rem;"> data</span>
                  </td>
                  <td class="text-center">
                    @if($jenis->dataset)
                      <a href="{{ url($jenis->dataset) }}" class="action-btn btn-available" target="_self">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1" style="display: inline-block; vertical-align: middle;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        Tersedia
                      </a>
                    @else
                      <span class="action-btn btn-unavailable">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1" style="display: inline-block; vertical-align: middle;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                        Tidak Ada
                      </span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($jenis->link_api)
                      <a href="{{ url($jenis->link_api) }}" class="action-btn btn-stats" target="_self">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1" style="display: inline-block; vertical-align: middle;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                        Tersedia
                      </a>
                    @else
                      <span class="action-btn btn-unavailable">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1" style="display: inline-block; vertical-align: middle;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                        Tidak Ada
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-muted"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 10l.01 0" /><path d="M15 10l.01 0" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /></svg>
                    <div>Belum ada data jenis informasi</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          @endforeach
        </table>
      </div>
    </div>
  </div>
@endsection