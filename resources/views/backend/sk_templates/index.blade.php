@extends('backend.layout.base')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0" style="font-size: 32px;"><b>{{ $title }}</b></h5>
                <small class="text-muted">Kelola banyak jenis template SK yayasan dan generate PDF dari data users.</small>
            </div>
            <a href="{{ route('sk-templates.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Buat Template
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nama Template</th>
                            <th>Judul Dokumen</th>
                            <th>Kertas</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th width="260">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $template->name }}</div>
                                    <small class="text-muted">{{ $template->description ?: 'Tanpa deskripsi' }}</small>
                                </td>
                                <td>{{ $template->document_title }}</td>
                                <td>{{ strtoupper($template->paper_size) }} / {{ ucfirst($template->orientation) }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $template->is_active ? 'success' : 'secondary' }}">
                                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>{{ $template->created_at ? $template->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('sk-templates.show', $template) }}" class="btn btn-primary btn-sm">Generate</a>
                                    <a href="{{ route('sk-templates.edit', $template) }}" class="btn btn-success btn-sm">Edit</a>
                                    <a href="{{ route('sk-templates.delete', $template) }}" class="btn btn-danger btn-sm" onclick="return confirm('Hapus template ini?')">Hapus</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada template SK.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
