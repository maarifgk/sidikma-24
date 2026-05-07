@extends('backend.layout.base')

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="font-size: 30px;"><b>{{ $title }}</b></h5>
                </div>
                <div class="card-body">
                    <form action="{{ $formAction }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Template</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Judul Dokumen / Nama File PDF</label>
                                <input type="text" name="document_title" class="form-control" value="{{ old('document_title', $template->document_title) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Deskripsi</label>
                                <input type="text" name="description" class="form-control" value="{{ old('description', $template->description) }}" placeholder="Contoh: SK pengangkatan guru tetap yayasan">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Ukuran Kertas</label>
                                <select name="paper_size" class="form-select" required>
                                    @foreach(['A4', 'legal', 'letter'] as $paper)
                                        <option value="{{ $paper }}" {{ old('paper_size', $template->paper_size) === $paper ? 'selected' : '' }}>{{ strtoupper($paper) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Orientasi</label>
                                <select name="orientation" class="form-select" required>
                                    @foreach(['portrait' => 'Portrait', 'landscape' => 'Landscape'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('orientation', $template->orientation) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Template aktif</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">CSS Custom</label>
                                <textarea name="custom_css" rows="14" class="form-control" style="font-family: monospace;">{{ old('custom_css', $template->custom_css) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Konten Template HTML</label>
                                <textarea name="content" rows="24" class="form-control" style="font-family: monospace;" required>{{ old('content', $template->content) }}</textarea>
                                <small class="text-muted">Gunakan placeholder seperti <code>&#123;&#123;nama_lengkap&#125;&#125;</code>, <code>&#123;&#123;nama_kelas&#125;&#125;</code>, dan lain-lain.</small>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-3 mb-0">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                            <a href="{{ route('sk-templates.index') }}" class="btn btn-success">Kembali</a>
                            @if ($isEdit)
                                <a href="{{ route('sk-templates.show', $template) }}" class="btn btn-outline-primary">Buka Generator</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Placeholder Data Users</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($placeholders as $placeholder)
                            <div class="list-group-item px-0">
                                <div class="fw-semibold"><code>&#123;&#123;{{ $placeholder['key'] }}&#125;&#125;</code></div>
                                <small class="text-muted">{{ $placeholder['label'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
