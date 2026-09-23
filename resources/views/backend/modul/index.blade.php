@extends('backend.layout.base')

@section('content')

    {{-- Alert sukses --}}
    @if(session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif

    {{-- Alert error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Upload Modul --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="font-size: 32px"><b>Upload Modul Baru</b></h5>
        </div>
        <div class="card-body">
            <form action="{{ route('modul.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label>Kelas</label>
                        <input type="text" name="kelas" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Jenis Modul</label>
                        <input type="text" name="jenis_modul" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label>Semester</label>
                        <select name="semester" class="form-control" required>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Mata Pelajaran</label>
                        <input type="text" name="mapel" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label>BAB</label>
                        <input type="text" name="bab" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Upload File Modul</label>
                    <input type="file" name="file" class="form-control" required>
                    <small class="text-muted">Format: PDF/DOC/PPT/XLS (maksimal 1 GB)</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>

    {{-- Table Modul --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="font-size: 32px"><b>Daftar Modul</b></h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Kelas</th>
                        <th>Jenis Modul</th>
                        <th>Semester</th>
                        <th>Mata Pelajaran</th>
                        <th>BAB</th>
                        <th>File</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modul as $key => $m)
                        <tr>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td>{{ $m->kelas }}</td>
                            <td>{{ $m->jenis_modul }}</td>
                            <td class="text-center">{{ $m->semester }}</td>
                            <td>{{ $m->mapel }}</td>
                            <td>{{ $m->bab }}</td>
                            <td>
                                <a href="{{ asset($m->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                    Download
                                </a>
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($m->created_at)->format('d-m-Y H:i') }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('modul.destroy', $m->id) }}" method="POST" 
                                      onsubmit="return confirm('Yakin hapus modul ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada modul.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
