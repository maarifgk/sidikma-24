@extends('backend.layout.base')

@section('content')
    <style>
        .template-preview-box {
            min-height: 420px;
            border: 1px solid rgba(67, 89, 113, .16);
            border-radius: 12px;
            background: #fff;
            padding: 24px;
            overflow: auto;
        }
    </style>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="font-size: 28px;"><b>{{ $template->name }}</b></h5>
                    <small class="text-muted">{{ $template->description ?: 'Generate PDF SK dari template ini.' }}</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih User</label>
                        <select id="user_id" class="form-select">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string) optional($selectedUser)->id === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->nama_lengkap }} - {{ $user->nama_kelas ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary disabled" id="previewLink" target="_blank">Preview Halaman</a>
                        <a href="#" class="btn btn-success disabled" id="pdfLink" target="_blank">Generate PDF</a>
                        <a href="{{ route('sk-templates.edit', $template) }}" class="btn btn-outline-primary">Edit Template</a>
                        <a href="{{ route('sk-templates.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>

                    <hr>

                    <div>
                        <small class="text-muted d-block mb-2">Placeholder tersedia</small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($placeholders as $placeholder)
                                <span class="badge bg-label-primary">{{ '{{' . $placeholder['key'] . '}}' }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Preview Template</h5>
                </div>
                <div class="card-body">
                    @if($selectedUser && $renderedHtml)
                        <div class="mb-3 text-muted">
                            Preview untuk user: <strong>{{ $selectedUser->nama_lengkap }}</strong>
                        </div>
                        <div class="template-preview-box">
                            {!! $renderedHtml !!}
                        </div>
                    @else
                        <div class="text-muted">Pilih user terlebih dahulu untuk melihat hasil render template dan generate PDF.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    const userSelect = document.getElementById('user_id');
    const previewLink = document.getElementById('previewLink');
    const pdfLink = document.getElementById('pdfLink');
    const templateId = @json($template->id);

    function updateLinks() {
        const userId = userSelect.value;
        if (!userId) {
            previewLink.href = '#';
            pdfLink.href = '#';
            previewLink.classList.add('disabled');
            pdfLink.classList.add('disabled');
            return;
        }

        previewLink.href = `/sk-templates/${templateId}/preview/${userId}`;
        pdfLink.href = `/sk-templates/${templateId}/pdf/${userId}`;
        previewLink.classList.remove('disabled');
        pdfLink.classList.remove('disabled');
    }

    userSelect.addEventListener('change', function () {
        updateLinks();
        if (this.value) {
            window.location.href = `{{ route('sk-templates.show', $template) }}?user_id=${this.value}`;
        }
    });

    updateLinks();
</script>
@endsection
