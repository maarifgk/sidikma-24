@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>
                <div class="card-body">
                    <form action="/tagihan/update/{{ $tagihan->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="user_id">Nama Siswa</label>
                                    <select class="form-control" name="user_id" id="user_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($siswa as $s)
                                            <option value="{{ $s->id }}" {{ $tagihan->user_id == $s->id ? 'selected' : '' }}>{{ $s->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="thajaran_id">Tahun Ajaran</label>
                                    <select class="form-control" name="thajaran_id" id="thajaran_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($thajaran as $s)
                                            <option value="{{ $s->id }}" {{ $tagihan->thajaran_id == $s->id ? 'selected' : '' }}>{{ $s->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Asal Madrasah</label>
                                    <select class="form-control" name="kelas_id" id="kelas_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($kelas as $s)
                                            <option value="{{ $s->id }}" {{ $tagihan->kelas_id == $s->id ? 'selected' : '' }}>{{ $s->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="jenis_pembayaran">Jenis Pembayaran</label>
                                    <select class="form-control" name="jenis_pembayaran" id="jenis_pembayaran" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($jnpembayaran as $j)
                                            <option value="{{ $j->id }}" {{ $tagihan->jenis_pembayaran == $j->id ? 'selected' : '' }}>{{ $j->pembayaran }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="nilai">Nilai</label>
                                    <input type="text" class="form-control" name="nilai" id="nilai" value="Rp. {{ number_format($tagihan->nilai) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="status">Status</label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="Belum Lunas" {{ $tagihan->status == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                        <option value="Lunas" {{ $tagihan->status == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="keterangan">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan">{{ $tagihan->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="k_iuran_2024">Upload File</label>
                                    <input type="file" class="form-control" name="k_iuran_2024" id="k_iuran_2024">
                                    @if($tagihan->k_iuran_2024)
                                        <small>Current file: {{ $tagihan->k_iuran_2024 }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <br>
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="/tagihan" type="button" class="btn btn-success">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
