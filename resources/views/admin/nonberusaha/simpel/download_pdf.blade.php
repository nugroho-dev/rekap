@extends('layouts.tableradminfluid')
@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Download PDF ke Server</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('simpel.downloadPdf') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="url" class="form-label">URL File PDF</label>
                            <input type="url" class="form-control" id="url" name="url" required placeholder="https://example.com/file.pdf">
                        </div>
                        <button type="submit" class="btn btn-primary">Download & Simpan</button>
                    </form>
                    @if(session('success'))
                        <div class="alert alert-success mt-3">File berhasil disimpan: <a href="{{ session('file') }}" target="_blank">{{ session('file') }}</a></div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
