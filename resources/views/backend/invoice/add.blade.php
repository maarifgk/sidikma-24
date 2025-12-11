@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl-10 mx-auto">
        <div class="card shadow-sm">

            {{-- HEADER --}}
            <div class="card-body border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-0 fw-bold">INVOICE PEMBAYARAN</h3>
                        <small class="text-muted">
                            LP. Ma'arif NU PCNU Gunungkidul
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-primary fs-6">
                            Tahun Pelajaran 2024/2025
                        </span>
                    </div>
                </div>
            </div>

            {{-- INFO INVOICE --}}
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Informasi Madrasah</h5>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="140" class="fw-semibold">Nama Madrasah</td>
                                <td>:</td>
                                <td><strong>{{ $invoice->school_name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Alamat</td>
                                <td>:</td>
                                <td>{{ $invoice->school_address ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Informasi Invoice</h5>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="140" class="fw-semibold">No Invoice</td>
                                <td>:</td>
                                <td><span class="badge bg-secondary">{{ $invoice->invoice_number }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal</td>
                                <td>:</td>
                                <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TABEL RINCIAN --}}
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">Rincian Pembayaran</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-primary">
                            <tr class="text-center">
                                <th width="50">No</th>
                                <th>Uraian</th>
                                <th width="100">Satuan</th>
                                <th width="130">Nominal</th>
                                <th width="100">Qty</th>
                                <th width="150">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>Peserta Didik</td>
                                <td class="text-center">Orang</td>
                                <td class="text-end">Rp 1.000</td>
                                <td class="text-center">120</td>
                                <td class="text-end">Rp 720.000</td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>Guru ASN Sertifikasi</td>
                                <td class="text-center">Orang</td>
                                <td class="text-end">Rp 20.000</td>
                                <td class="text-center">5</td>
                                <td class="text-end">Rp 600.000</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end fw-bold">Total</th>
                                <th class="text-end text-success fs-5 fw-bold">
                                    Rp {{ $invoice->total_amount ? number_format($invoice->total_amount, 0, ',', '.') : '-' }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- CATATAN --}}
            <div class="card-body border-top bg-light">
                <h6 class="card-title text-primary mb-3">
                    <i class="fas fa-info-circle me-2"></i>Catatan Penting
                </h6>
                <ul class="mb-0 text-muted">
                    @if($invoice->notes)
                        @foreach(explode('.', $invoice->notes) as $note)
                            @if(trim($note))
                                <li>{{ trim($note) }}.</li>
                            @endif
                        @endforeach
                    @else
                        <li>Pembayaran iuran dilakukan per semester.</li>
                        <li>Invoice ini sah tanpa tanda tangan.</li>
                        <li>Pembayaran dapat dilakukan melalui transfer bank atau tunai.</li>
                    @endif
                </ul>
            </div>

            {{-- TANDA TANGAN --}}
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <p class="mb-5">
                            Gunungkidul, {{ now()->format('d M Y') }}
                        </p>
                        <p class="fw-bold mb-0">
                            Bendahara LP. Ma'arif NU
                        </p>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button class="btn btn-outline-primary me-2">
                            <i class="fas fa-eye me-2"></i>Preview
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-print me-2"></i>Cetak Invoice
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
