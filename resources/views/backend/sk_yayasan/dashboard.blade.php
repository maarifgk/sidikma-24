@extends('backend.layout.base')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1"><b>{{ $title }}</b></h4>
                        <small class="text-muted">Dashboard SK Yayasan untuk template, generate, upload file bertanda tangan, dan import massal.</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('sk-templates.create') }}" class="btn btn-primary">Buat Template</a>
                        <a href="{{ route('sk-templates.settings') }}" class="btn btn-outline-primary">Pengaturan Nomor SK</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">Total Template</div>
                    <div style="font-size: 34px; font-weight: 800;">{{ $stats['template_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">Total Dokumen Upload</div>
                    <div style="font-size: 34px; font-weight: 800;">{{ $stats['document_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">Dokumen Tahun {{ $defaultYear }}</div>
                    <div style="font-size: 34px; font-weight: 800;">{{ $stats['document_count_current_year'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">User Terhubung SK</div>
                    <div style="font-size: 34px; font-weight: 800;">{{ $stats['user_count_with_document'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Template SK</h5>
                        <small class="text-muted">Template yang dipakai untuk generate SK Yayasan.</small>
                    </div>
                    <a href="{{ route('sk-templates.create') }}" class="btn btn-sm btn-primary">Tambah</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Kertas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $template->name }}</div>
                                            {{-- <small class="text-muted">{{ $template->description ?: '-' }}</small> --}}
                                        </td>
                                        <td>{!! $template->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' !!}</td>
                                        <td>{{ strtoupper($template->paper_size ?: 'A4') }} / {{ ucfirst($template->orientation ?: 'portrait') }}</td>
                                        <td class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('sk-templates.show', $template) }}" class="btn btn-sm btn-primary">Generate</a>
                                            <a href="{{ route('sk-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada template SK.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Upload SK Per User</h5>
                    <small class="text-muted">Upload file SK yang sudah ditandatangani untuk satu user tertentu.</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('sk-yayasan.upload-single') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->nama_lengkap }} - {{ $user->nama_kelas ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tahun SK</label>
                                <input type="number" name="tahun_sk" class="form-control" min="2000" max="2100" value="{{ $defaultYear }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Template SK</label>
                                <select name="sk_template_id" class="form-select">
                                    <option value="">Tanpa Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">File SK</label>
                            <input type="file" name="file_sk" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success w-100">Upload & Hubungkan ke User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Import Banyak File SK</h5>
                    <small class="text-muted">Sistem akan mencocokkan nama file ke user berdasarkan nama lengkap, NIP, NUPTK, atau NIS yang ada di nama file.</small>
                </div>
                <div class="card-body">
                    @if($importResult)
                        <div class="alert alert-info">
                            <div><b>Total file:</b> {{ $importResult['total_files'] }}</div>
                            <div><b>Dokumen baru:</b> {{ $importResult['matched_count'] }}</div>
                            <div><b>Dokumen update:</b> {{ $importResult['updated_count'] }}</div>
                            <div><b>Tidak cocok:</b> {{ count($importResult['unmatched_files']) }}</div>
                            <div><b>Ambigu:</b> {{ count($importResult['ambiguous_files']) }}</div>
                            @if(!empty($importResult['unmatched_files']))
                                <div class="mt-2"><b>File tidak cocok:</b> {{ implode(', ', $importResult['unmatched_files']) }}</div>
                            @endif
                            @if(!empty($importResult['ambiguous_files']))
                                <div class="mt-2"><b>File ambigu:</b> {{ implode(', ', $importResult['ambiguous_files']) }}</div>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('sk-yayasan.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tahun SK</label>
                                <input type="number" name="tahun_sk" class="form-control" min="2000" max="2100" value="{{ $defaultYear }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Template SK</label>
                                <select name="sk_template_id" class="form-select">
                                    <option value="">Tanpa Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Batasi ke Sekolah/Madrasah</label>
                                <select name="kelas_id" class="form-select">
                                    <option value="">Semua Sekolah/Madrasah</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">File SK Banyak User</label>
                                <input type="file" name="files[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                                <small class="text-muted">Contoh nama file yang mudah dicocokkan: <code>sk-heru-agung-nugroho-2026.pdf</code> atau mengandung NIP/NUPTK/NIS.</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning">Import & Cocokkan Otomatis</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dokumen SK Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Sekolah</th>
                                    <th>Tahun</th>
                                    <th>Template</th>
                                    <th>Sumber</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>{{ $document->nama_lengkap }}</td>
                                        <td>{{ $document->nama_kelas ?? '-' }}</td>
                                        <td>{{ $document->tahun_sk }}</td>
                                        <td>{{ $document->template_name ?? '-' }}</td>
                                        <td>{{ ucfirst($document->source_type) }}{{ $document->matched_by ? ' / ' . $document->matched_by : '' }}</td>
                                        <td>
                                            <a href="{{ route('sk-yayasan.documents.download', $document->id) }}" class="btn btn-sm btn-outline-primary">
                                                {{ $document->original_filename }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada dokumen SK yang diupload.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
