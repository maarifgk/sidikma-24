@extends('backend.layout.base')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0" style="font-size: 32px;"><b>{{ $title }}</b></h5>
                <small class="text-muted">Atur nomor SK otomatis per periode agar generate batch mengikuti urutan yang benar.</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sk-templates.index') }}" class="btn btn-outline-secondary">Kembali ke Template</a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-primary">
                Placeholder format nomor yang bisa dipakai:
                @foreach($patternHelp as $token => $label)
                    <div><code>{{ $token }}</code> - {{ $label }}</div>
                @endforeach
            </div>

            <form action="{{ route('sk-templates.settings.update') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Format Nomor SK</th>
                                <th width="120">Digit</th>
                                <th width="120">Nomor Awal</th>
                                <th width="150">Nomor Berikutnya</th>
                                <th width="100">Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods as $period)
                                @php($row = $settingsData[(int) $period->id] ?? [])
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $period->nama_periode }}</div>
                                    </td>
                                    <td>
                                        <input type="text" name="settings[{{ $period->id }}][nomor_pattern]" class="form-control" value="{{ $row['nomor_pattern'] ?? '' }}" required>
                                        <small class="text-muted">Contoh: <code>&#123;&#123;nomor_urut&#125;&#125;/&#123;&#123;teks_nomor_sk&#125;&#125;/&#123;&#123;periode&#125;&#125;/&#123;&#123;tahun&#125;&#125;</code></small>
                                    </td>
                                    <td>
                                        <input type="number" name="settings[{{ $period->id }}][digit_nomor]" class="form-control" min="1" max="10" value="{{ $row['digit_nomor'] ?? 4 }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="settings[{{ $period->id }}][nomor_awal]" class="form-control" min="1" value="{{ $row['nomor_awal'] ?? 1 }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="settings[{{ $period->id }}][nomor_berikutnya]" class="form-control" min="1" value="{{ $row['nomor_berikutnya'] ?? 1 }}" required>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="hidden" name="settings[{{ $period->id }}][is_active]" value="0">
                                            <input type="checkbox" class="form-check-input" name="settings[{{ $period->id }}][is_active]" value="1" {{ !empty($row['is_active']) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    <a href="{{ route('sk-templates.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
