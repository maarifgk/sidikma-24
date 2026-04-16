@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Informasi Pembayaran</h5>
                    <a href="/pembayaran" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="/pembayaran/info/update" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ Helper::apk()->id ?? '' }}">

                        <div class="mb-3">
                            <label class="form-label">Konten Informasi Pembayaran (HTML)</label>
                            <textarea name="info_pembayaran" id="info_pembayaran" rows="12" class="form-control">{{ old('info_pembayaran', Helper::apk()->info_pembayaran ?? '') }}</textarea>
                            <small class="form-text text-muted">Gunakan HTML sederhana atau salin tabel dari halaman saat ini.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Optional: enable a simple WYSIWYG in future. For now a raw textarea.
    </script>
@endsection
