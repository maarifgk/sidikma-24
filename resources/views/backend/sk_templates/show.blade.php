@extends('backend.layout.base')

@section('content')
    <style>
        .template-preview-box {
            border: 1px solid rgba(67, 89, 113, .16);
            border-radius: 14px;
            background: #eef2f8;
            padding: 14px;
            overflow: auto;
        }

        .template-preview-frame {
            width: 100%;
            height: 880px;
            border: 0;
            border-radius: 10px;
            background: #eef2f8;
        }

        .status-filter-dropdown {
            position: relative;
        }

        .status-filter-dropdown summary {
            list-style: none;
        }

        .status-filter-dropdown summary::-webkit-details-marker {
            display: none;
        }

        .status-filter-toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border: 1px solid rgba(67, 89, 113, .16);
            border-radius: 12px;
            background: #f9fafc;
            color: #566274;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .status-filter-summary {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .status-filter-caret {
            flex-shrink: 0;
            transition: transform .18s ease;
        }

        .status-filter-dropdown[open] .status-filter-caret {
            transform: rotate(180deg);
        }

        .status-filter-panel {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 20;
            border: 1px solid rgba(67, 89, 113, .16);
            border-radius: 14px;
            padding: 12px;
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .12);
        }

        .status-filter-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            max-height: 150px;
            overflow: auto;
        }

        .status-filter-option input {
            display: none;
        }

        .status-filter-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(67, 89, 113, .18);
            background: #fff;
            color: #566274;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: .18s ease;
        }

        .status-filter-option input:checked + .status-filter-chip {
            background: #e8f2ff;
            border-color: #3b82f6;
            color: #1d4ed8;
            box-shadow: 0 6px 18px rgba(59, 130, 246, .12);
        }

        .status-filter-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
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
                    @php($periodOptions = collect($users)->pluck('nama_periode')->filter()->unique()->sort()->values())
                    @php($statusOptions = collect($users)->pluck('nama_jurusan')->filter()->unique()->sort()->values())

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Filter Periode</label>
                            <select id="period_filter" class="form-select">
                                <option value="">-- Semua Periode --</option>
                                @foreach($periodOptions as $periodOption)
                                    <option value="{{ $periodOption }}">{{ $periodOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Filter Status Kepegawaian</label>
                            <details class="status-filter-dropdown">
                                <summary class="status-filter-toggle">
                                    <span class="status-filter-summary" id="status_filter_summary">Semua status kepegawaian</span>
                                    <span class="status-filter-caret">▾</span>
                                </summary>
                                <div class="status-filter-panel">
                                    <div class="status-filter-grid" id="status_filter">
                                        @foreach($statusOptions as $statusOption)
                                            <label class="status-filter-option">
                                                <input type="checkbox" value="{{ $statusOption }}">
                                                <span class="status-filter-chip">{{ $statusOption }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="status-filter-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="select_all_status">Pilih Semua</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="reset_status_filter">Reset</button>
                                    </div>
                                </div>
                            </details>
                            <small class="text-muted">Bisa pilih lebih dari satu status.</small>
                        </div>
                    </div>

                    <form action="{{ route('sk-templates.show', $template) }}" method="GET" id="single_generate_form">
                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select id="user_id" name="user_id" class="form-select">
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        data-periode="{{ $user->nama_periode ?? '' }}"
                                        data-status="{{ $user->nama_jurusan ?? '' }}"
                                        {{ (string) optional($selectedUser)->id === (string) $user->id ? 'selected' : '' }}
                                    >
                                        {{ $user->nama_lengkap }} - {{ $user->nama_kelas ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Text Setelah Nomor SK</label>
                                <input type="text" id="nomor_text" name="nomor_text" class="form-control" value="{{ $nomorText }}" placeholder="Contoh: SK.01/LPM.GK">
                                <small class="text-muted">Dipakai untuk placeholder <code>&#123;&#123;teks_nomor_sk&#125;&#125;</code> saat generate.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun Generate SK</label>
                                <input type="number" id="tahun_sk" name="tahun_sk" class="form-control" min="2000" max="2100" value="{{ $selectedYear }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor SK Mulai Dari</label>
                                <input type="number" id="nomor_mulai" name="nomor_mulai" class="form-control" min="1" value="{{ $startNumber }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100 mb-3">Terapkan ke Preview</button>
                    </form>

                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary disabled" id="previewLink" target="_blank">Preview Halaman</a>
                        <a href="#" class="btn btn-success disabled" id="pdfLink" target="_blank">Generate PDF</a>
                        <form action="{{ route('sk-templates.batch-pdf', $template) }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="nomor_text" id="batch_nomor_text" value="{{ $nomorText }}">
                            <input type="hidden" name="tahun_sk" id="batch_tahun_sk" value="{{ $selectedYear }}">
                            <input type="hidden" name="nomor_mulai" id="batch_nomor_mulai" value="{{ $startNumber }}">
                            <div class="mb-2">
                                <label class="form-label">Generate Banyak User</label>
                                <select name="user_ids[]" id="batch_user_ids" class="form-select" multiple size="10" required>
                                    @foreach($users as $user)
                                        <option
                                            value="{{ $user->id }}"
                                            data-periode="{{ $user->nama_periode ?? '' }}"
                                            data-status="{{ $user->nama_jurusan ?? '' }}"
                                            {{ in_array((int) $user->id, $selectedUserIds ?? [], true) ? 'selected' : '' }}
                                        >
                                            {{ $user->nama_lengkap }} - {{ $user->nama_kelas ?? '-' }} - {{ $user->nama_periode ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Nomor SK akan mengikuti urutan daftar user yang dipilih pada kotak ini, berdasarkan pengaturan periode. Filter periode dan status di atas juga berlaku di sini.</small>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Generate PDF Banyak User</button>
                        </form>
                        <a href="{{ route('sk-templates.settings') }}" class="btn btn-outline-dark">Pengaturan SK Yayasan</a>
                        <a href="{{ route('sk-templates.edit', $template) }}" class="btn btn-outline-primary">Edit Template</a>
                        <a href="{{ route('sk-templates.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>

                    <hr>

                    {{-- <div>
                        <small class="text-muted d-block mb-2">Placeholder tersedia</small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($placeholders as $placeholder)
                                <span class="badge bg-label-primary">&#123;&#123;{{ $placeholder['key'] }}&#125;&#125;</span>
                            @endforeach
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Preview Template</h5>
                </div>
                <div class="card-body">
                    @if($selectedUser)
                        <div class="mb-3 text-muted">
                            Preview untuk user: <strong>{{ $selectedUser->nama_lengkap }}</strong>
                            @if($previewSkNumber)
                                <div>Text setelah nomor SK: <strong>{{ $nomorText }}</strong></div>
                                <div>Tahun generate: <strong>{{ $selectedYear }}</strong></div>
                                <div>Nomor mulai: <strong>{{ $startNumber }}</strong></div>
                                <div>Nomor SK hasil preview: <strong>{{ $previewSkNumber }}</strong></div>
                            @endif
                        </div>
                        <div class="template-preview-box">
                            <iframe
                                id="template_preview_frame"
                                class="template-preview-frame"
                                src="{{ route('sk-templates.preview', [$template, $selectedUser->id]) }}?tahun_sk={{ $selectedYear }}&nomor_mulai={{ $startNumber }}"
                            ></iframe>
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
    const periodFilter = document.getElementById('period_filter');
    const statusFilter = document.getElementById('status_filter');
    const statusFilterSummary = document.getElementById('status_filter_summary');
    const statusFilterInputs = Array.from(statusFilter.querySelectorAll('input[type="checkbox"]'));
    const selectAllStatusButton = document.getElementById('select_all_status');
    const resetStatusFilterButton = document.getElementById('reset_status_filter');
    const userSelect = document.getElementById('user_id');
    const batchUserSelect = document.getElementById('batch_user_ids');
    const nomorTextInput = document.getElementById('nomor_text');
    const yearInput = document.getElementById('tahun_sk');
    const startNumberInput = document.getElementById('nomor_mulai');
    const previewLink = document.getElementById('previewLink');
    const pdfLink = document.getElementById('pdfLink');
    const previewFrame = document.getElementById('template_preview_frame');
    const templateId = @json($template->id);
    const batchNomorTextInput = document.getElementById('batch_nomor_text');
    const batchYearInput = document.getElementById('batch_tahun_sk');
    const batchStartNumberInput = document.getElementById('batch_nomor_mulai');

    function matchesFilters(option) {
        if (!option.value) {
            return true;
        }

        const optionPeriod = option.dataset.periode || '';
        const optionStatus = option.dataset.status || '';
        const selectedPeriod = periodFilter.value || '';
        const selectedStatuses = statusFilterInputs.filter((input) => input.checked).map((input) => input.value).filter(Boolean);

        return (!selectedPeriod || optionPeriod === selectedPeriod)
            && (!selectedStatuses.length || selectedStatuses.includes(optionStatus));
    }

    function updateStatusSummary() {
        const selectedStatuses = statusFilterInputs.filter((input) => input.checked).map((input) => input.value);

        if (!selectedStatuses.length) {
            statusFilterSummary.textContent = 'Semua status kepegawaian';
            return;
        }

        if (selectedStatuses.length === 1) {
            statusFilterSummary.textContent = selectedStatuses[0];
            return;
        }

        statusFilterSummary.textContent = `${selectedStatuses.length} status dipilih`;
    }

    function applyUserFilters() {
        updateStatusSummary();

        Array.from(userSelect.options).forEach((option) => {
            const visible = matchesFilters(option);
            option.hidden = !visible;

            if (!visible && option.selected) {
                option.selected = false;
            }
        });

        Array.from(batchUserSelect.options).forEach((option) => {
            const visible = matchesFilters(option);
            option.hidden = !visible;

            if (!visible && option.selected) {
                option.selected = false;
            }
        });

        updateLinks();
    }

    function updateLinks() {
        const userId = userSelect.value;
        const nomorText = nomorTextInput.value || 'SK.01/LPM.GK';
        const year = yearInput.value || '{{ $selectedYear }}';
        const startNumber = startNumberInput.value || '1';
        batchNomorTextInput.value = nomorText;
        batchYearInput.value = year;
        batchStartNumberInput.value = startNumber;

        if (!userId) {
            previewLink.href = '#';
            pdfLink.href = '#';
            previewLink.classList.add('disabled');
            pdfLink.classList.add('disabled');
            if (previewFrame) {
                previewFrame.removeAttribute('src');
            }
            return;
        }

        const query = `?nomor_text=${encodeURIComponent(nomorText)}&tahun_sk=${encodeURIComponent(year)}&nomor_mulai=${encodeURIComponent(startNumber)}`;
        previewLink.href = `/sk-templates/${templateId}/preview/${userId}${query}`;
        pdfLink.href = `/sk-templates/${templateId}/pdf/${userId}${query}`;
        previewLink.classList.remove('disabled');
        pdfLink.classList.remove('disabled');
        if (previewFrame) {
            previewFrame.src = previewLink.href;
        }
    }

    [userSelect, nomorTextInput, yearInput, startNumberInput].forEach((element) => {
        element.addEventListener('input', updateLinks);
        element.addEventListener('change', updateLinks);
    });

    periodFilter.addEventListener('change', applyUserFilters);
    statusFilterInputs.forEach((input) => {
        input.addEventListener('change', applyUserFilters);
    });

    selectAllStatusButton.addEventListener('click', function () {
        statusFilterInputs.forEach((input) => {
            input.checked = true;
        });
        applyUserFilters();
    });

    resetStatusFilterButton.addEventListener('click', function () {
        statusFilterInputs.forEach((input) => {
            input.checked = false;
        });
        applyUserFilters();
    });

    applyUserFilters();
</script>
@endsection
