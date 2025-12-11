@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-sm">
            {{-- HEADER --}}
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $title }}</h4>
                        <small class="text-white-50">Kelas: {{ $kelas->nama_kelas ?? 'N/A' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-success fs-6">
                            Total Tagihan: Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="card-body bg-light">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h5 class="card-title text-primary">{{ $totalStudents }}</h5>
                                <p class="card-text small">Total Siswa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-file-invoice fa-2x text-info mb-2"></i>
                                <h5 class="card-title text-info">{{ $totalInvoices }}</h5>
                                <p class="card-text small">Total Invoice</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title text-success">{{ $paidInvoices }}</h5>
                                <p class="card-text small">Lunas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h5 class="card-title text-warning">{{ $unpaidInvoices }}</h5>
                                <p class="card-text small">Belum Lunas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STUDENTS TABLE --}}
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">
                    <i class="fas fa-list me-2"></i>Daftar Siswa & Tagihan
                </h5>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-hover table-sm">
                        <thead class="table-primary">
                            <tr class="text-center">
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th width="130">Kelas</th>
                                <th width="130">Jurusan</th>
                                <th width="130">No Invoice</th>
                                <th width="100">Status</th>
                                <th width="140">Total Tagihan</th>
                                <th width="240" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($students as $student)
                                @php
                                    $studentInvoice = $invoices->where('user_id', $student->id)->first();
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold text-secondary">{{ $no++ }}</td>
                                    <td class="fw-semibold text-dark">{{ $student->nama_lengkap }}</td>
                                    <td class="text-muted">{{ $student->nama_kelas ?? '-' }}</td>
                                    <td class="text-muted">{{ $student->nama_jurusan ?? '-' }}</td>
                                    <td class="text-muted small">{{ $studentInvoice->invoice_number ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($studentInvoice)
                                            @if($studentInvoice->status == 'paid')
                                                <span class="badge bg-success">Lunas</span>
                                            @elseif($studentInvoice->status == 'sent')
                                                <span class="badge bg-info">Dikirim</span>
                                            @else
                                                <span class="badge bg-secondary">Draft</span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        @if($studentInvoice)
                                            Rp {{ number_format($studentInvoice->total_amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($studentInvoice)
                                                <a href="{{ route('invoice.add', $student->id) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Lihat Detail Invoice">
                                                    <i class="fas fa-file-alt me-1"></i>Detail
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-success" 
                                                        onclick="printInvoice({{ $student->id }})"
                                                        title="Cetak Invoice">
                                                    <i class="fas fa-print me-1"></i>Cetak
                                                </button>
                                                <a href="{{ route('invoice.edit', $studentInvoice->id) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit Invoice">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            @else
                                                <a href="{{ route('invoice.add', $student->id) }}" 
                                                   class="btn btn-primary" 
                                                   title="Buat Invoice Baru">
                                                    <i class="fas fa-plus me-1"></i>Buat
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <th colspan="6" class="text-end">Total Tagihan Kelas:</th>
                                <th class="text-end text-success fs-6">
                                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="card-footer bg-light border-top">
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ count($students) }} siswa dari kelas <strong>{{ $kelas->nama_kelas ?? 'N/A' }}</strong>
                    </small>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoice') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportClassBilling()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printInvoice(studentId) {
    const printWindow = window.open('{{ route("invoice.add", "") }}/' + studentId, '_blank');
    if (printWindow) {
        printWindow.focus();
    }
}

function exportClassBilling() {
    // Implement export functionality
    alert('Fitur export akan segera hadir!');
}
</script>
@endsection
