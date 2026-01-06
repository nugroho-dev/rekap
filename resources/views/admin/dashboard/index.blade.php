@extends('layouts.tableradmin')
@section('content')
<style>
  .stats-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  
  .stats-header h2 {
    color: white;
    margin: 0;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .stat-card {
    border-radius: 16px;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
    height: 100%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  
  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, transparent, currentColor, transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
  }
  
  .stat-card:hover::before {
    opacity: 0.8;
  }
  
  .stat-card .card-body {
    padding: 1.5rem;
  }
  
  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
  }
  
  .stat-icon::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 16px;
    background: inherit;
    opacity: 0.3;
    filter: blur(8px);
    z-index: -1;
  }
  
  .stat-content {
    flex: 1;
  }
  
  .stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .stat-value {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
    line-height: 1;
    background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .stat-update {
    font-size: 0.75rem;
    color: #94a3b8;
    line-height: 1.4;
  }
  
  .stat-update strong {
    color: #64748b;
    display: block;
    margin-bottom: 0.25rem;
  }
  
  .stat-date {
    font-weight: 500;
    color: #475569;
  }
  
  /* Color variations */
  .stat-card.red-variant { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
  .stat-card.red-variant .stat-icon { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
  
  .stat-card.green-variant { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
  .stat-card.green-variant .stat-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
  
  .stat-card.indigo-variant { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); }
  .stat-card.indigo-variant .stat-icon { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; }
  
  .stat-card.orange-variant { background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%); }
  .stat-card.orange-variant .stat-icon { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; }
  
  .stat-card.yellow-variant { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
  .stat-card.yellow-variant .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
  
  .stat-card.blue-variant { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
  .stat-card.blue-variant .stat-icon { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
  
  .stat-card.lime-variant { background: linear-gradient(135deg, #ecfccb 0%, #d9f99d 100%); }
  .stat-card.lime-variant .stat-icon { background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%); color: white; }
  
  .stat-card.cyan-variant { background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); }
  .stat-card.cyan-variant .stat-icon { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; }
  
  .stat-card.pink-variant { background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); }
  .stat-card.pink-variant .stat-icon { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white; }
  
  .stat-card.teal-variant { background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); }
  .stat-card.teal-variant .stat-icon { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); color: white; }
  
  .stat-card.purple-variant { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); }
  .stat-card.purple-variant .stat-icon { background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); color: white; }
  
  .chart-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
  }
  
  .chart-card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.12);
  }
  
  .chart-card .card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid #e2e8f0;
    padding: 1.25rem 1.5rem;
  }
  
  .chart-card .card-title {
    font-weight: 700;
    color: #1e293b;
    margin: 0;
  }
  
  /* Tab Navigation Styles */
  .chart-section {
    margin-top: 2rem;
  }
  
  .chart-section .card {
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  }
  
  .chart-section .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0;
  }
  
  .chart-section .nav-tabs {
    border: none;
    margin: 0;
  }
  
  .chart-section .nav-tabs .nav-link {
    border: none;
    color: rgba(255,255,255,0.7);
    font-weight: 600;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    background: transparent;
    border-bottom: 3px solid transparent;
  }
  
  .chart-section .nav-tabs .nav-link:hover {
    color: white;
    background: rgba(255,255,255,0.1);
    border-bottom-color: rgba(255,255,255,0.3);
  }
  
  .chart-section .nav-tabs .nav-link.active {
    color: white;
    background: rgba(255,255,255,0.15);
    border-bottom-color: white;
  }
  
  .chart-section .card-body {
    padding: 2rem;
    background: #f8fafc;
  }
  
  .chart-section .tab-content .card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
  }
  
  .chart-section .tab-content .card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid #e2e8f0;
    padding: 1rem 1.5rem;
  }
  
  .chart-section .tab-content .card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
  }
</style>

<div class="stats-header">
  <h2>
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2" style="display: inline-block; vertical-align: middle;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
    Statistik Highlight {{ date('Y') }}
  </h2>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
  <div class="col">
    <div class="card stat-card red-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Insentif (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalInsentif) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateInsentif ?? '-' }}</div>
            <div>{{ $lastUpdateInsentifDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card green-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Fasilitasi (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalFasilitasi) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateFasilitasi ?? '-' }}</div>
            <div>{{ $lastUpdateFasilitasiDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card indigo-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Bimtek (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalBimtek) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateBimtek ?? '-' }}</div>
            <div>{{ $lastUpdateBimtekDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card orange-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Pengawasan (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalPengawasan) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdatePengawasan ?? '-' }}</div>
            <div>{{ $lastUpdatePengawasanDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card yellow-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Pameran (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalExpo) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateExpo ?? '-' }}</div>
            <div>{{ $lastUpdateExpoDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card blue-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Business Meeting</div>
          <div class="stat-value">{{ number_format($totalBusinessMeeting) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateBusinessMeeting ?? '-' }}</div>
            <div>{{ $lastUpdateBusinessMeetingDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card lime-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">LOI (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalLoi) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateLoi ?? '-' }}</div>
            <div>{{ $lastUpdateLoiDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card cyan-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Berusaha</div>
          <div class="stat-value">{{ number_format($totalBerusaha) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateBerusaha ?? '-' }}</div>
            <div>{{ $lastUpdateBerusahaDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card pink-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Total Izin</div>
          <div class="stat-value">{{ number_format($totalIzin) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateIzin ?? '-' }}</div>
            <div>{{ $lastUpdateIzinDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card teal-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Total Proyek</div>
          <div class="stat-value">{{ number_format($totalProyek) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateProyek ?? '-' }}</div>
            <div>{{ $lastUpdateProyekDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card purple-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Total NIB</div>
          <div class="stat-value">{{ number_format($totalNib) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateNib ?? '-' }}</div>
            <div>{{ $lastUpdateNibDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card green-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Izin Terbit SiCantik</div>
          <div class="stat-value">{{ number_format($totalIzinTerbitSicantik) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateIzinTerbitSicantik ?? '-' }}</div>
            <div>{{ $lastUpdateIzinTerbitSicantikDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card indigo-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">PBG (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalPbg) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdatePbg ?? '-' }}</div>
            <div>{{ $lastUpdatePbgDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card blue-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">SIMPEL (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalSimpel) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateSimpel ?? '-' }}</div>
            <div>{{ $lastUpdateSimpelDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card orange-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">MPPD (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalMppd) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateMppd ?? '-' }}</div>
            <div>{{ $lastUpdateMppdDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card teal-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Komitmen (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalKomitmen) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateKomitmen ?? '-' }}</div>
            <div>{{ $lastUpdateKomitmenDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card cyan-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Konsultasi (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalKonsultasi) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateKonsultasi ?? '-' }}</div>
            <div>{{ $lastUpdateKonsultasiDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card lime-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Informasi (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalInformasi) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateInformasi ?? '-' }}</div>
            <div>{{ $lastUpdateInformasiDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card red-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Pengaduan (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalPengaduan) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdatePengaduan ?? '-' }}</div>
            <div>{{ $lastUpdatePengaduanDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card pink-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Produk Hukum (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalProdukHukum) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdateProdukHukum ?? '-' }}</div>
            <div>{{ $lastUpdateProdukHukumDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col">
    <div class="card stat-card orange-variant">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Peta Potensi (Tahun Ini)</div>
          <div class="stat-value">{{ number_format($totalPetaPotensi) }}</div>
          <div class="stat-update">
            <strong>Update terakhir:</strong>
            <div class="stat-date">{{ $lastUpdatePetaPotensi ?? '-' }}</div>
            <div>{{ $lastUpdatePetaPotensiDate ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
              
              
               
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  window.monthlyInsentif = @json($monthlyInsentif);
  window.monthlyPengawasan = @json($monthlyPengawasan);
  window.monthlyExpo = @json($monthlyExpo);
  window.monthlyBusinessMeeting = @json($monthlyBusinessMeeting);
  window.monthlyLoi = @json($monthlyLoi);
  window.monthlyBerusaha = @json($monthlyBerusaha);
  window.monthlyIzin = @json($monthlyIzin);
  window.monthlyProyek = @json($monthlyProyek);
  window.monthlyFasilitasi = @json($monthlyFasilitasi);
  window.monthlyBimtek = @json($monthlyBimtek);
  window.monthlyKomitmen = @json($monthlyKomitmen);
  window.monthlyPengaduan = @json($monthlyPengaduan);
  window.monthlyProdukHukum = @json($monthlyProdukHukum);
  window.monthlyPetaPotensi = @json($monthlyPetaPotensi);
  window.monthlyKonsultasi = @json($monthlyKonsultasi);
  window.monthlyInformasi = @json($monthlyInformasi);
  window.monthlyProyekVerification = @json($monthlyProyekVerification);
  window.monthlyPbg = @json($monthlyPbg);
  window.monthlySimpel = @json($monthlySimpel);
  window.monthlyMppd = @json($monthlyMppd);
  window.monthlyIzinTerbitSicantik = @json($monthlyIzinTerbitSicantik);
  window.monthlyNib = @json($monthlyNib);
</script>

<div class="chart-section">
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
        <li class="nav-item" role="presentation">
          <a href="#tabs-investasi" class="nav-link active" data-bs-toggle="tab" role="tab" aria-selected="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>
            Investasi & Promosi
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-perizinan" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
            Perizinan
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-layanan" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
            Layanan Publik
          </a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="tab-content">
        <!-- Tab Investasi & Promosi -->
        <div class="tab-pane active show" id="tabs-investasi" role="tabpanel">
          <div class="row">
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Berusaha</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-berusaha"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Insentif</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-insentif"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan LOI</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-loi"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Business Meeting</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-businessmeeting"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Expo</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-expo"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Pengawasan</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-pengawasan"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Perizinan -->
        <div class="tab-pane" id="tabs-perizinan" role="tabpanel">
          <div class="row">
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan NIB</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-nib"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Izin</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-izin"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Izin Terbit SiCantik</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-izinterbitsicantik"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan PBG</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-pbg"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan SIMPEL</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-simpel"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan MPPD</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-mppd"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Proyek</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-proyek"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Komitmen</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-komitmen"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Layanan Publik -->
        <div class="tab-pane" id="tabs-layanan" role="tabpanel">
          <div class="row">
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Fasilitasi</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-fasilitasi"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Bimtek</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-bimtek"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Konsultasi</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-konsultasi"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Informasi</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-informasi"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Pengaduan</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-pengaduan"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Produk Hukum</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-produkhukum"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Grafik Bulanan Peta Potensi</h3>
                </div>
                <div class="card-body">
                  <div id="apexchart-petapotensi"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('/js/chart-dashboard.js') }}"></script>
@endpush
@endsection


