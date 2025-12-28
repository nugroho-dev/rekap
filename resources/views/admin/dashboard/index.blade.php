@extends('layouts.tableradmin')
@section('content')       
              <h2 class="mb-4">Statistik Highlight {{ date('Y') }}</h2>
              <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3 mb-4">
                        <div class="col">
                          <div class="card bg-red-lt">
                            <div class="card-body d-flex align-items-center">
                              <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-red" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg></span>
                              <div>
                                <div class="subheader">Total Insentif (Tahun Ini)</div>
                                <div class="h1 mb-0">{{ number_format($totalInsentif) }}</div>
                                <div class="text-muted small">Update terakhir:</div>
                                <div class="text-muted small">{{ $lastUpdateInsentif ?? '-' }}</div>
                                <div class="text-muted small">{{ $lastUpdateInsentifDate ?? '-' }}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <div class="col">
                        <div class="card bg-green-lt">
                          <div class="card-body d-flex align-items-center">
                            <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-green" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg></span>
                            <div>
                              <div class="subheader">Total Fasilitasi (Tahun Ini)</div>
                              <div class="h1 mb-0">{{ number_format($totalFasilitasi) }}</div>
                              <div class="text-muted small">Update terakhir:</div>
                              <div class="text-muted small">{{ $lastUpdateFasilitasi ?? '-' }}</div>
                              <div class="text-muted small">{{ $lastUpdateFasilitasiDate ?? '-' }}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col">
                      <div class="card bg-indigo-lt">
                        <div class="card-body d-flex align-items-center">
                          <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                          <div>
                            <div class="subheader">Total Bimtek (Tahun Ini)</div>
                            <div class="h1 mb-0">{{ number_format($totalBimtek) }}</div>
                            <div class="text-muted small">Update terakhir:</div>
                            <div class="text-muted small">{{ $lastUpdateBimtek ?? '-' }}</div>
                            <div class="text-muted small">{{ $lastUpdateBimtekDate ?? '-' }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <div class="col">
                    <div class="card bg-orange-lt">
                      <div class="card-body d-flex align-items-center">
                        <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-gray" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg></span>
                        <div>
                          <div class="subheader">Pengawasan (Tahun Ini)</div>
                          <div class="h1 mb-0">{{ number_format($totalPengawasan) }}</div>
                          <div class="text-muted small">Update terakhir:</div>
                          <div class="text-muted small">{{ $lastUpdatePengawasan ?? '-' }}</div>
                          <div class="text-muted small">{{ $lastUpdatePengawasanDate ?? '-' }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="card bg-yellow-lt">
                      <div class="card-body d-flex align-items-center">
                        <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-yellow" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg></span>
                        <div>
                          <div class="subheader">Pameran (Tahun Ini)</div>
                          <div class="h1 mb-0">{{ number_format($totalExpo) }}</div>
                          <div class="text-muted small">Update terakhir:</div>
                          <div class="text-muted small">{{ $lastUpdateExpo ?? '-' }}</div>
                          <div class="text-muted small">{{ $lastUpdateExpoDate ?? '-' }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="card bg-blue-lt">
                      <div class="card-body d-flex align-items-center">
                        <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-blue" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                        <div>
                          <div class="subheader">Business Meeting (Tahun Ini)</div>
                          <div class="h1 mb-0">{{ number_format($totalBusinessMeeting) }}</div>
                          <div class="text-muted small">Update terakhir:</div>
                          <div class="text-muted small">{{ $lastUpdateBusinessMeeting ?? '-' }}</div>
                          <div class="text-muted small">{{ $lastUpdateBusinessMeetingDate ?? '-' }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="card bg-lime-lt">
                      <div class="card-body d-flex align-items-center">
                        <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-purple" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg></span>
                        <div>
                          <div class="subheader">Total LOI (Tahun Ini)</div>
                          <div class="h1 mb-0">{{ number_format($totalLoi) }}</div>
                          <div class="text-muted small">Update terakhir:</div>
                          <div class="text-muted small">{{ $lastUpdateLoi ?? '-' }}</div>
                          <div class="text-muted small">{{ $lastUpdateLoiDate ?? '-' }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-cyan-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg></span>
                      <div>
                        <div class="subheader">Total Berusaha</div>
                        <div class="h1 mb-0">{{ number_format($totalBerusaha) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateBerusaha ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateBerusahaDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-pink-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg></span>
                      <div>
                        <div class="subheader">Total Izin</div>
                        <div class="h1 mb-0">{{ number_format($totalIzin) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateIzin ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateIzinDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-teal-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-warning" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total Proyek</div>
                        <div class="h1 mb-0">{{ number_format($totalProyek) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateProyek ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateProyekDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-gray-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total NIB</div>
                        <div class="h1 mb-0">{{ number_format($totalNib) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateNib ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateNibDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-success-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg></span>
                      <div>
                        <div class="subheader">Izin Terbit SiCantik</div>
                        <div class="h1 mb-0">{{ number_format($totalIzinTerbitSicantik) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateIzinTerbitSicantik ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateIzinTerbitSicantikDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-primary-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total PBG (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalPbg) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdatePbg ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdatePbgDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-info-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total SIMPEL (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalSimpel) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateSimpel ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateSimpelDate ?? '-' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-warning-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-warning" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total MPPD (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalMppd) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateMppd ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateMppdDate ?? '-' }}</div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-teal-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-teal" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total Komitmen (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalKomitmen) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateKomitmen ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateKomitmenDate ?? '-' }}</div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-cyan-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-cyan" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total Konsultasi (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalKonsultasi) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateKonsultasi ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateKonsultasiDate ?? '-' }}</div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-lime-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-lime" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total Informasi (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalInformasi) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateInformasi ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateInformasiDate ?? '-' }}</div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-danger-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-danger" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg></span>
                      <div>
                        <div class="subheader">Total Pengaduan (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalPengaduan) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdatePengaduan ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdatePengaduanDate ?? '-' }}</div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-pink-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-pink" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total Produk Hukum (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalProdukHukum) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdateProdukHukum ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdateProdukHukumDate ?? '-' }}</div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                  <div class="card bg-orange-lt">
                    <div class="card-body d-flex align-items-center">
                      <span class="me-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-orange" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M8 9h8" /><path d="M8 13h6" /></svg></span>
                      <div>
                        <div class="subheader">Total Peta Potensi (Tahun Ini)</div>
                        <div class="h1 mb-0">{{ number_format($totalPetaPotensi) }}</div>
                        <div class="text-muted small">Update terakhir:</div>
                        <div class="text-muted small">{{ $lastUpdatePetaPotensi ?? '-' }}</div>
                        <div class="text-muted small">{{ $lastUpdatePetaPotensiDate ?? '-' }}</div>
                        
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
        <h3 class="card-title">Grafik Bulanan Pengawasan</h3>
      </div>
      <div class="card-body">
        <div id="apexchart-pengawasan"></div>
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
        <h3 class="card-title">Grafik Bulanan Komitmen</h3>
      </div>
      <div class="card-body">
        <div id="apexchart-komitmen"></div>
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
<script src="/js/chart-dashboard.js"></script>
@endsection


