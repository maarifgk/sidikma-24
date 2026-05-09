@extends('backend.layout.base')

@section('content')
    <style>
        .sk-template-preview-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .sk-template-preview-zoom {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sk-template-preview-zoom-label {
            min-width: 56px;
            text-align: center;
            font-weight: 600;
            color: #5f6b7c;
        }

        .sk-template-preview-scroll {
            overflow: auto;
            border: 1px solid rgba(67, 89, 113, .16);
            border-radius: 14px;
            background: #eef2f8;
            padding: 14px;
        }

        .sk-template-preview-frame {
            width: 100%;
            height: 880px;
            border: 0;
            border-radius: 10px;
            background: #eef2f8;
        }
    </style>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="font-size: 30px;"><b>{{ $title }}</b></h5>
                </div>
                <div class="card-body">
                    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <input type="hidden" name="content" id="content">
                        <input type="hidden" name="custom_css" id="custom_css">
                        <input type="hidden" name="logo_url" id="logo_url" value="{{ old('logo_url', $builderData['logo_url']) }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Template</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Judul Dokumen / Nama File PDF</label>
                                <input type="text" name="document_title" id="document_title" class="form-control" value="{{ old('document_title', $template->document_title) }}" required>
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

                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <h6 class="mb-0">Kepala Surat</h6>
                                <small class="text-muted">Isi langsung teks yang tampil di bagian atas SK.</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Baris Atas</label>
                                <input type="text" name="header_topline" class="form-control builder-field" value="{{ old('header_topline', $builderData['header_topline']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Baris Atas</label>
                                <input type="number" name="header_topline_font_size" class="form-control builder-field" value="{{ old('header_topline_font_size', $builderData['header_topline_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Judul Lembaga</label>
                                <input type="text" name="header_title" class="form-control builder-field" value="{{ old('header_title', $builderData['header_title']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Judul</label>
                                <input type="number" name="header_title_font_size" class="form-control builder-field" value="{{ old('header_title_font_size', $builderData['header_title_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="header_address" class="form-control builder-field" value="{{ old('header_address', $builderData['header_address']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Alamat</label>
                                <input type="number" name="header_address_font_size" class="form-control builder-field" value="{{ old('header_address_font_size', $builderData['header_address_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nomor WhatsApp</label>
                                <input type="text" name="header_phone" class="form-control builder-field" value="{{ old('header_phone', $builderData['header_phone']) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Kop Surat</label>
                                <input type="text" name="header_email" class="form-control builder-field" value="{{ old('header_email', $builderData['header_email']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Kontak</label>
                                <input type="number" name="header_contact_font_size" class="form-control builder-field" value="{{ old('header_contact_font_size', $builderData['header_contact_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Upload Logo Kop Surat</label>
                                <input type="file" name="logo_file" id="logo_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                                <small class="text-muted">Logo ini akan dipakai di kop surat. Jika tidak diupload, sistem memakai logo yang sudah tersimpan.</small>
                                <div class="mt-3">
                                    <img
                                        src="{{ old('logo_url', $builderData['logo_url']) }}"
                                        alt="Preview logo kop surat"
                                        id="logo_preview_image"
                                        style="max-height: 96px; max-width: 100%; object-fit: contain; border: 1px solid rgba(67, 89, 113, .18); border-radius: 10px; padding: 8px; background: #fff;"
                                    >
                                </div>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <h6 class="mb-0">Judul Keputusan</h6>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Judul SK</label>
                                <input type="text" name="decision_title" class="form-control builder-field" value="{{ old('decision_title', $builderData['decision_title']) }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Font Judul SK</label>
                                <input type="number" name="decision_title_font_size" class="form-control builder-field" value="{{ old('decision_title_font_size', $builderData['decision_title_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nomor SK</label>
                                {{-- Use a safe placeholder for nomor_sk to avoid nested Blade parsing in the view. The real number is generated at document generation time. --}}
                                <input type="text" name="nomor_sk" class="form-control builder-field" value="{{ old('nomor_sk', '%%nomor_sk%%') }}" readonly required>
                                <small class="text-muted">Otomatis mengikuti Pengaturan SK Yayasan sesuai periode user saat generate.</small>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Font Nomor SK</label>
                                <input type="number" name="decision_number_font_size" class="form-control builder-field" value="{{ old('decision_number_font_size', $builderData['decision_number_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Kalimat Pembuka</label>
                                <input type="text" name="opening_line" class="form-control builder-field" value="{{ old('opening_line', $builderData['opening_line']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Pembuka</label>
                                <input type="number" name="opening_line_font_size" class="form-control builder-field" value="{{ old('opening_line_font_size', $builderData['opening_line_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <h6 class="mb-0">Konsideran</h6>
                                <small class="text-muted">Boleh memakai placeholder seperti <code>&#123;&#123;nama_kelas&#125;&#125;</code>, <code>&#123;&#123;ketugasan&#125;&#125;</code>, dan lain-lain.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Menimbang</label>
                                <textarea name="menimbang_text" rows="5" class="form-control builder-field" required>{{ old('menimbang_text', $builderData['menimbang_text']) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mengingat</label>
                                <textarea name="mengingat_text" rows="5" class="form-control builder-field" required>{{ old('mengingat_text', $builderData['mengingat_text']) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Memperhatikan</label>
                                <textarea name="memperhatikan_text" rows="4" class="form-control builder-field" required>{{ old('memperhatikan_text', $builderData['memperhatikan_text']) }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Konsideran</label>
                                <input type="number" name="consideration_font_size" class="form-control builder-field" value="{{ old('consideration_font_size', $builderData['consideration_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <h6 class="mb-0">Isi Keputusan</h6>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Judul Tengah</label>
                                <input type="text" name="memutuskan_title" class="form-control builder-field" value="{{ old('memutuskan_title', $builderData['memutuskan_title']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Judul Tengah</label>
                                <input type="number" name="memutuskan_title_font_size" class="form-control builder-field" value="{{ old('memutuskan_title_font_size', $builderData['memutuskan_title_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Teks Pertama Sebelum Data Guru</label>
                                <input type="text" name="pertama_intro" class="form-control builder-field" value="{{ old('pertama_intro', $builderData['pertama_intro']) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Teks Pertama Setelah Data Guru</label>
                                <textarea name="pertama_penutup" rows="5" class="form-control builder-field" required>{{ old('pertama_penutup', $builderData['pertama_penutup']) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Teks Kedua</label>
                                <textarea name="kedua_text" rows="4" class="form-control builder-field" required>{{ old('kedua_text', $builderData['kedua_text']) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Teks Ketiga</label>
                                <textarea name="ketiga_text" rows="3" class="form-control builder-field" required>{{ old('ketiga_text', $builderData['ketiga_text']) }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Isi Keputusan</label>
                                <input type="number" name="decision_body_font_size" class="form-control builder-field" value="{{ old('decision_body_font_size', $builderData['decision_body_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Data Guru</label>
                                <input type="number" name="identity_table_font_size" class="form-control builder-field" value="{{ old('identity_table_font_size', $builderData['identity_table_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <h6 class="mb-0">Area Tanda Tangan</h6>
                                <small class="text-muted">Bagian ini hanya area teks penetapan, tanpa gambar tanda tangan.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ditetapkan di</label>
                                <input type="text" name="signature_city" class="form-control builder-field" value="{{ old('signature_city', $builderData['signature_city']) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Label Tanggal</label>
                                <input type="text" name="signature_date_label" class="form-control builder-field" value="{{ old('signature_date_label', $builderData['signature_date_label']) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Penetapan</label>
                                <input type="text" name="signature_date" class="form-control builder-field" value="{{ old('signature_date', $builderData['signature_date']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Baris Pengurus</label>
                                <input type="text" name="signature_body_top" class="form-control builder-field" value="{{ old('signature_body_top', $builderData['signature_body_top']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="signature_role" class="form-control builder-field" value="{{ old('signature_role', $builderData['signature_role']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nama Penanda Tangan</label>
                                <input type="text" name="signature_name" class="form-control builder-field" value="{{ old('signature_name', $builderData['signature_name']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Area Tanda Tangan</label>
                                <input type="number" name="signature_font_size" class="form-control builder-field" value="{{ old('signature_font_size', $builderData['signature_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <h6 class="mb-0">Tembusan</h6>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Judul Tembusan</label>
                                <input type="text" name="tembusan_title" class="form-control builder-field" value="{{ old('tembusan_title', $builderData['tembusan_title']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Judul Tembusan</label>
                                <input type="number" name="tembusan_title_font_size" class="form-control builder-field" value="{{ old('tembusan_title_font_size', $builderData['tembusan_title_font_size']) }}" min="8" max="40" step="0.5" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Daftar Tembusan</label>
                                <textarea name="tembusan_items" rows="4" class="form-control builder-field" required>{{ old('tembusan_items', $builderData['tembusan_items']) }}</textarea>
                                <small class="text-muted">Satu baris untuk satu item tembusan.</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Font Isi Tembusan</label>
                                <input type="number" name="tembusan_items_font_size" class="form-control builder-field" value="{{ old('tembusan_items_font_size', $builderData['tembusan_items_font_size']) }}" min="8" max="40" step="0.5" required>
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
                            <button type="button" class="btn btn-outline-primary" id="apply_standard_template">Gunakan Format SK Standar</button>
                            <a href="{{ route('sk-templates.index') }}" class="btn btn-success">Kembali</a>
                            @if ($isEdit)
                                <a href="{{ route('sk-templates.show', $template) }}" class="btn btn-outline-primary">Buka Generator</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Preview Hasil PDF</h5>
                    <small class="text-muted">Preview otomatis menggunakan data contoh.</small>
                </div>
                <div class="card-body">
                    <div class="sk-template-preview-toolbar">
                        <small class="text-muted mb-0">Gunakan zoom untuk melihat detail layout.</small>
                        <div class="sk-template-preview-zoom">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="preview_zoom_out">-</button>
                            <span class="sk-template-preview-zoom-label" id="preview_zoom_label">100%</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="preview_zoom_in">+</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="preview_zoom_reset">Reset</button>
                        </div>
                    </div>
                    <div class="sk-template-preview-scroll">
                        <iframe id="pdf_preview_frame" class="sk-template-preview-frame"></iframe>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
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

@section('js')
<script>
    const previewFrame = document.getElementById('pdf_preview_frame');
    const previewZoomInButton = document.getElementById('preview_zoom_in');
    const previewZoomOutButton = document.getElementById('preview_zoom_out');
    const previewZoomResetButton = document.getElementById('preview_zoom_reset');
    const previewZoomLabel = document.getElementById('preview_zoom_label');
    const cssInput = document.getElementById('custom_css');
    const titleInput = document.getElementById('document_title');
    const contentInput = document.getElementById('content');
    const logoUrlInput = document.getElementById('logo_url');
    const logoFileInput = document.getElementById('logo_file');
    const logoPreviewImage = document.getElementById('logo_preview_image');
    const builderFields = Array.from(document.querySelectorAll('.builder-field'));
    const previewSamples = @json($previewSamples);
    const applyStandardTemplateButton = document.getElementById('apply_standard_template');
    const presetTitle = @json($presetTitle);
    const presetCss = @json($presetCss);
    const presetBuilderData = @json($presetBuilderData);
    const whatsappIconUrl = @json($whatsappIconUrl);
    const emailIconUrl = @json($emailIconUrl);
    let previewZoomLevel = 1;

    function replacePlaceholders(template) {
        let output = template || '';
        const openToken = '{' + '{';
        const closeToken = '}' + '}';

        Object.entries(previewSamples).forEach(([key, value]) => {
            const token = openToken + key + closeToken;
            output = output.split(token).join(value ?? '');
        });

        return output;
    }

    function placeholderToken(key) {
        return '{' + '{' + key + '}' + '}';
    }

    function renderPreview() {
        const renderedTitle = replacePlaceholders(titleInput.value || 'Preview Template SK');
        const customCss = presetCss || '';
        cssInput.value = customCss;

        // Update preview image element for the form side
        logoPreviewImage.src = logoUrlInput.value || previewSamples.logo_url || '';

        // Compute logo display dimensions client-side to mirror server logic
        const logoSrc = logoUrlInput.value || previewSamples.logo_url || '';
        computeLogoDisplayDimensions(logoSrc).then((logoDimensions) => {
            const renderedContent = buildPreviewContent(logoDimensions);
            contentInput.value = renderedContent;

            const previewHtml = `
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>${renderedTitle}</title>
                    <style>
                        body {
                            margin: 0;
                            padding: 24px;
                            background: #e9eef6;
                            font-family: Arial, sans-serif;
                            color: #111827;
                        }

                        .preview-sheet {
                            width: 794px;
                            min-height: 1123px;
                            margin: 0 auto;
                            background: #fff;
                            box-shadow: 0 12px 38px rgba(15, 23, 42, .15);
                            padding: 36px 42px;
                            box-sizing: border-box;
                        }

                        ${customCss}
                    </style>
                </head>
                <body>
                    <div class="preview-sheet">
                        ${renderedContent}
                    </div>
                </body>
                </html>
            `;

            previewFrame.srcdoc = previewHtml;
            applyPreviewZoom();
        });
    }

    function applyPreviewZoom() {
        previewFrame.style.zoom = previewZoomLevel;
        previewZoomLabel.textContent = `${Math.round(previewZoomLevel * 100)}%`;
    }

    function setPreviewZoom(nextZoom) {
        previewZoomLevel = Math.max(0.5, Math.min(2, nextZoom));
        applyPreviewZoom();
    }

    function escapeHtml(value) {
        return (value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatText(value) {
        return replacePlaceholders(escapeHtml(value || '')).replace(/\r|\r/g, '<br>');
    }

    function formatAttr(value) {
        return replacePlaceholders(escapeHtml(value || ''));
    }

    function getFieldValue(name) {
        const field = document.querySelector(`[name="${name}"]`);
        return field ? field.value : '';
    }

    function fontSizePx(name, fallback) {
        const raw = parseFloat(getFieldValue(name));
        const size = Number.isFinite(raw) ? Math.min(40, Math.max(8, raw)) : fallback;
        return `${size}px`;
    }

    function buildPreviewContent(logoDimensions) {
        const tembusanItems = (getFieldValue('tembusan_items') || '')
            .split(/\r\n|\n|\r/)
            .map(item => item.trim())
            .filter(Boolean)
            .map(item => `<li>${formatText(item)}</li>`)
            .join('');

        // Provide defaults if dimensions not computed yet.
        logoDimensions = logoDimensions || { cell_width: 268, wrap_width: 250, wrap_height: 125, image_width: 250, image_height: 125 };

        return `
            <div class="document">
                <table class="header-table">
                    <tr>
                        <td class="header-logo-cell" style="width: ${logoDimensions.cell_width}px;">
                            <div class="header-logo-wrap" style="width: ${logoDimensions.wrap_width}px; text-align:center;">
                                <img src="${formatAttr(logoUrlInput.value)}" alt="Logo" style="display:block;margin:0 auto;width:${logoDimensions.image_width}px;height:${logoDimensions.image_height}px;">
                            </div>
                        </td>
                        <td class="header-text-cell">
                            <div class="header-topline" style="font-size:${fontSizePx('header_topline_font_size', 13)};">${formatText(getFieldValue('header_topline'))}</div>
                            <div class="header-title" style="font-size:${fontSizePx('header_title_font_size', 26)};">${formatText(getFieldValue('header_title'))}</div>
                            <div class="header-address" style="font-size:${fontSizePx('header_address_font_size', 11)};">${formatText(getFieldValue('header_address'))}</div>
                            <table class="header-contact-table">
                                <tr>
                                    <td class="header-contact-text" style="font-size:${fontSizePx('header_contact_font_size', 11)};">${formatText(getFieldValue('header_phone'))}</td>
                                    <td class="header-contact-icon-cell"><img src="${formatAttr(whatsappIconUrl)}" alt="WhatsApp" class="header-contact-icon"></td>
                                </tr>
                                <tr>
                                    <td class="header-contact-text" style="font-size:${fontSizePx('header_contact_font_size', 11)};">${formatText(getFieldValue('header_email'))}</td>
                                    <td class="header-contact-icon-cell"><img src="${formatAttr(emailIconUrl)}" alt="Email" class="header-contact-icon"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="header-divider"></div>

                <div class="text-center decision-heading">
                    <div class="decision-title" style="font-size:${fontSizePx('decision_title_font_size', 13)};">${formatText(getFieldValue('decision_title'))}</div>
                    <div class="decision-number" style="font-size:${fontSizePx('decision_number_font_size', 12)};">Nomor : ${formatText(getFieldValue('nomor_sk'))}</div>
                </div>

                <div class="content-block">
                    <p class="opening-line" style="font-size:${fontSizePx('opening_line_font_size', 11.5)};">${formatText(getFieldValue('opening_line'))}</p>

                    <table class="consideration-table" style="font-size:${fontSizePx('consideration_font_size', 11.5)};">
                        <tr><td class="label">Menimbang</td><td class="colon">:</td><td><div class="justified-text">${formatText(getFieldValue('menimbang_text'))}</div></td></tr>
                        <tr><td class="label">Mengingat</td><td class="colon">:</td><td><div class="justified-text">${formatText(getFieldValue('mengingat_text'))}</div></td></tr>
                        <tr><td class="label">Memperhatikan</td><td class="colon">:</td><td><div class="justified-text">${formatText(getFieldValue('memperhatikan_text'))}</div></td></tr>
                    </table>

                    <div class="memutuskan-title" style="font-size:${fontSizePx('memutuskan_title_font_size', 15)};">${formatText(getFieldValue('memutuskan_title'))}</div>

                    <table class="decision-body-table" style="font-size:${fontSizePx('decision_body_font_size', 11.5)};">
                        <tr><td class="label">Menetapkan</td><td class="colon">:</td><td></td></tr>
                        <tr>
                            <td class="label">Pertama</td>
                            <td class="colon">:</td>
                            <td>
                                ${formatText(getFieldValue('pertama_intro'))}
                                <table class="identity-table" style="font-size:${fontSizePx('identity_table_font_size', 11.5)};">
                                    <tr><td class="num">1.</td><td class="field">Nama</td><td class="colon">:</td><td>${formatText(placeholderToken('nama_lengkap'))}</td></tr>
                                    <tr><td class="num">2.</td><td class="field">Tempat, tanggal lahir</td><td class="colon">:</td><td>${formatText(placeholderToken('tempat_lahir') + ', ' + placeholderToken('tgl_lahir'))}</td></tr>
                                    <tr><td class="num">3.</td><td class="field">NUPTK/NPK</td><td class="colon">:</td><td>${formatText(placeholderToken('nuptk'))}</td></tr>
                                    <tr><td class="num">4.</td><td class="field">EWANUGK/KARTANU</td><td class="colon">:</td><td>${formatText(placeholderToken('nis'))}</td></tr>
                                    <tr><td class="num">5.</td><td class="field">TMT Pertama</td><td class="colon">:</td><td>${formatText(placeholderToken('tmt'))}</td></tr>
                                    <tr><td class="num">6.</td><td class="field">Pendidikan, tahun lulus</td><td class="colon">:</td><td>${formatText(placeholderToken('ptt_lulus'))}</td></tr>
                                    <tr><td class="num">7.</td><td class="field">Program Studi</td><td class="colon">:</td><td>${formatText(placeholderToken('p_studi'))}</td></tr>
                                    <tr><td class="num">8.</td><td class="field">Status Kepegawaian</td><td class="colon">:</td><td>${formatText(placeholderToken('nama_jurusan'))}</td></tr>
                                </table>
                                <div class="justified-text">${formatText(getFieldValue('pertama_penutup'))}</div>
                            </td>
                        </tr>
                        <tr><td class="label">Kedua</td><td class="colon">:</td><td><div class="justified-text">${formatText(getFieldValue('kedua_text'))}</div></td></tr>
                        <tr><td class="label">Ketiga</td><td class="colon">:</td><td><div class="justified-text">${formatText(getFieldValue('ketiga_text'))}</div></td></tr>
                    </table>
                    <div class="signature-section" style="font-size:${fontSizePx('signature_font_size', 11.5)};">
                        <table class="signature-table">
                            <tr><td class="signature-label">Ditetapkan di</td><td class="colon">:</td><td>${formatText(getFieldValue('signature_city'))}</td></tr>
                            <tr><td class="signature-label">${formatText(getFieldValue('signature_date_label'))}</td><td class="colon">:</td><td>${formatText(getFieldValue('signature_date'))}</td></tr>
                            <tr><td colspan="3" class="signature-body-top">${formatText(getFieldValue('signature_body_top'))}</td></tr>
                            <tr><td colspan="3" class="signature-role">${formatText(getFieldValue('signature_role'))}</td></tr>
                            <tr><td colspan="3" class="signature-space"></td></tr>
                            <tr><td colspan="3" class="signature-name">${formatText(getFieldValue('signature_name'))}</td></tr>
                        </table>
                    </div>
                    <div class="footer-section">
                        <div class="tembusan-block">
                            <div style="font-size:${fontSizePx('tembusan_title_font_size', 11.5)};">${formatText(getFieldValue('tembusan_title'))}</div>
                            <ol style="font-size:${fontSizePx('tembusan_items_font_size', 11.5)};">${tembusanItems}</ol>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function computeLogoDisplayDimensions(src) {
        return new Promise((resolve) => {
            const defaultDimensions = { cell_width: 268, wrap_width: 250, wrap_height: 125, image_width: 250, image_height: 125 };
            if (!src) {
                resolve(defaultDimensions);
                return;
            }

            const img = new Image();
            img.onload = function () {
                const sourceWidth = img.naturalWidth || 96;
                const sourceHeight = img.naturalHeight || 96;
                const aspectRatio = sourceHeight > 0 ? (sourceWidth / sourceHeight) : 1;
                let maxW = 138;
                let maxH = 118;
                let cellPadding = 14;

                if (aspectRatio >= 1.3) {
                    maxW = 250;
                    maxH = 125;
                    cellPadding = 18;
                } else if (aspectRatio <= 0.85) {
                    maxW = 108;
                    maxH = 118;
                    cellPadding = 12;
                }

                const ratio = Math.min(maxW / sourceWidth, maxH / sourceHeight);
                const imageWidth = Math.max(1, Math.round(sourceWidth * ratio));
                const imageHeight = Math.max(1, Math.round(sourceHeight * ratio));
                const wrapWidth = imageWidth;
                const wrapHeight = imageHeight;
                resolve({
                    cell_width: wrapWidth + cellPadding,
                    wrap_width: wrapWidth,
                    wrap_height: wrapHeight,
                    image_width: imageWidth,
                    image_height: imageHeight,
                });
            };

            img.onerror = function () {
                resolve(defaultDimensions);
            };

            // Ensure data URIs and same-origin images load
            img.src = src;
            // In case the image is cached and onload may not fire in some browsers, check dimensions
            if (img.complete && img.naturalWidth) {
                img.onload();
            }
        });
    }

    [...builderFields, titleInput].forEach((element) => {
        element.addEventListener('input', renderPreview);
    });

    logoFileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) {
            renderPreview();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            logoUrlInput.value = event.target?.result || '';
            renderPreview();
        };
        reader.readAsDataURL(file);
    });

    applyStandardTemplateButton.addEventListener('click', function () {
        titleInput.value = presetTitle;
        Object.entries(presetBuilderData).forEach(([key, value]) => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = value;
            }
        });
        renderPreview();
    });

    previewZoomOutButton.addEventListener('click', function () {
        setPreviewZoom(previewZoomLevel - 0.1);
    });

    previewZoomInButton.addEventListener('click', function () {
        setPreviewZoom(previewZoomLevel + 0.1);
    });

    previewZoomResetButton.addEventListener('click', function () {
        setPreviewZoom(1);
    });

    renderPreview();
</script>
@endsection
