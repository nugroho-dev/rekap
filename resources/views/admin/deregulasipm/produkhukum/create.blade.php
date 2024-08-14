@extends('layouts.tableradmin')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Tambah Produk Hukum
            </h2>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-8">
            <div class="card card-lg">
              <div class="card-body">
                <div class="markdown">
                  <div class="col-lg-12">
                    <div class="row row-cards">
                      <div class="col-12">
                        <form class="card" method="post">
                          <div class="card-body">
                            <h3 class="card-title">Produk Hukum</h3>
                            <div class="row row-cards">
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Judul</label>
                                  <input type="text" class="form-control"  placeholder="Judul Produk Hukum" value="">
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Slug</label>
                                  <input type="text" class="form-control" placeholder="slug" value="" readonly>
                                </div>
                              </div>
                              
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">T.E.U</label>
                                  <input type="text" class="form-control" placeholder="T.E.U" value="">
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Nomor</label>
                                  <input type="text" class="form-control" placeholder="Nomor Produk Hukum" value="">
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Bentuk</label>
                                  <input type="text" class="form-control" placeholder="Bentuk Produk Hukum" value="">
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Bentuk Singkat</label>
                                  <input type="text" class="form-control" placeholder="Bentuk Singkat Produk Hukum" value="">
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-6 col-md-3">
                                  <div class="mb-3">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" placeholder="Tahun Produk Hukum">
                                  </div>
                                </div>
                              </div>
                              
                                <div class="col-sm-6 col-md-12">
                                  <div class="mb-3">
                                    <label class="form-label">Tempat Penetapan</label>
                                    <input type="text" class="form-control" placeholder="Tempat Penetapan Produk Hukum">
                                  </div>
                                </div>
                             
                              
                                <div class="col-sm-6 col-md-4">
                                  <div class="mb-3">
                                    <label class="form-label">Tanggal Penetapan</label>
                                    <input type="date" class="form-control" placeholder="">
                                  </div>
                               </div>
                                <div class="col-sm-6 col-md-4">
                                  <div class="mb-3">
                                    <label class="form-label">Tanggal Pegundangan</label>
                                    <input type="date" class="form-control" placeholder="">
                                  </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                  <div class="mb-3">
                                    <label class="form-label">Tanggal Berlaku</label>
                                    <input type="date" class="form-control" placeholder="ZIP Code">
                                  </div>
                                </div>
                              
                             
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Tipe Dokumen</label>
                                  <select class="form-control form-select">
                                    <option value="">Germany</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Subjek</label>
                                  <select class="form-control form-select">
                                    <option value="">Germany</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Status</label>
                                  <select class="form-control form-select">
                                    <option value="">Germany</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Bidang</label>
                                  <select class="form-control form-select">
                                    <option value="">Germany</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Bahasa</label>
                                  <input type="text" class="form-control" placeholder="City">
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Lokasi</label>
                                  <input type="text" class="form-control" placeholder="City">
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                              <div class="mb-3">
                                <label class="form-label">Tags input</label>
                                <select type="text" class="form-select tomselected ts-hidden-accessible" placeholder="Select tags" id="select-tags" value multiple="multiple" tabindex="-1">
                                  <option value="HTML">HTML</option>
                                  <option value="JavaScript">JavaScript</option>
                                  <option value="CSS">CSS</option>
                                  <option value="jQuery">jQuery</option>
                                  <option value="Bootstrap">Bootstrap</option>
                                  <option value="Ruby">Ruby</option>
                                  <option value="Python">Python</option>
                                </select>
                                <div class="ts-wrapper form-select multi focus input-active dropdown-active">
                                  <div class="ts-control">
                                    <input tabindex="0" role="combobox" aria-haspopup="listbox" aria-expanded="true" aria-controls="select-tags-ts-dropdown" id="select-tags-ts-control" placeholder="Select tags" type="select-multiple" aria-activedescendant="select-tags-opt-3">
                                  </div>
                                </div>
                              </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">File</label>
                                  <input type="file" class="form-control" placeholder="City">
                                </div>
                              </div>
                             
                            </div>
                          </div>
                          <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                          </div>
                        </form>
                      </div>
                     
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="me-3">
                    <!-- Download SVG icon from http://tabler-icons.io/i/scale -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 20l10 0" /><path d="M6 6l6 -1l6 1" /><path d="M12 3l0 17" /><path d="M9 12l-3 -6l-3 6a3 3 0 0 0 6 0" /><path d="M21 12l-3 -6l-3 6a3 3 0 0 0 6 0" /></svg>
                  </div>
                  <div>
                    <small class="text-muted">tabler/tabler is licensed under the</small>
                    <h3 class="lh-1">MIT License</h3>
                  </div>
                </div>
                <div class="text-muted mb-3">
                  A short and simple permissive license with conditions only requiring preservation of copyright and
                  license notices. Licensed works, modifications, and larger works may be distributed under different terms
                  and without source code.
                </div>
                <h4>Permissions</h4>
                <ul class="list-unstyled space-y-1">
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Commercial use</li>
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Modification</li>
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Distribution</li>
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Private use</li>
                </ul>
                <h4>Limitations</h4>
                <ul class="list-unstyled space-y-1">
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/x -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    Liability</li>
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/x -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    Warranty</li>
                </ul>
                <h4>Conditions</h4>
                <ul class="list-unstyled space-y-1">
                  <li><!-- Download SVG icon from http://tabler-icons.io/i/info-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                    License and copyright notice</li>
                </ul>
              </div>
              <div class="card-footer">
                This is not legal advice.
                <a href="#" target="_blank">Learn more about repository licenses.</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
  
@endsection