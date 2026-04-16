@extends('backend.layout.base')

@section('content')
<div class="card">
  <div class="card-body text-center">
    <h5 class="card-title">Selamat datang, {{ request()->user()->nama_lengkap }}</h5>
    <p class="card-text">Halaman mobile khusus untuk pengguna dengan peran Guru/Pegawai (role 2).</p>

    <div class="row">
      <div class="col-6 mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100">Dashboard</a>
      </div>
      <div class="col-6 mb-3">
        <a href="{{ route('tenaga') }}" class="btn btn-outline-primary w-100">Informasi Guru/Pegawai</a>
      </div>
      <div class="col-6 mb-3">
        <a href="{{ route('pembayaran') }}" class="btn btn-outline-primary w-100">Pembayaran SK</a>
      </div>
      <div class="col-6 mb-3">
        <a href="{{ route('upload-sk.index') }}" class="btn btn-outline-primary w-100">File SK</a>
      </div>
      <div class="col-12">
        <a href="{{ route('profile') }}" class="btn btn-primary w-100">Lihat Profile</a>
      </div>
    </div>
  </div>
</div>
@endsection
