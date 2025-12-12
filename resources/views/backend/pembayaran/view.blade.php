@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-mb">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>
                <div class="card-body">
                    @if (request()->user()->role == 1)
                        <form action="/pembayaran/search" method="GET" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label" for="kelas_id">Asal Madrasah</label>
                                        <select class="form-select" name="kelas_id" id="kelas_id" required>
                                            <option value="" selected>-- Pilih --</option>
                                            @foreach ($kelas as $s)
                                                <option value="{{ $s->id }}">{{ $s->nama_kelas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    {{-- <div id="open" class="position-absolute top-0 start-50 translate-middle"
                                        style="margin-top: 5%">
                                        <div id="loading-image" class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div> --}}
                                    <div class="mb-3">
                                        <label class="form-label" for="">EWANUGK/NAMA</label>
                                        <select class="form-select" name="nis" id="nis"
                                            aria-label="Default select example"></select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                    <a href="/pembayaran" type="button" class="btn btn-danger">refresh</a>
                                </div>
                        </form>
                    @endif
                </div>

            </div>
        </div>
        <div class="col-xl">
            <div class="card mb-4">
                @if ($siswa != null)
                    <div class="card-body">
                        <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                        @if (request()->user()->role != 3)
                            <!-- Card Header - Accordion -->
                            <a href="#informasisiswa" class="d-block card-header py-3"
                            style="background-color: #009991;" data-toggle="collapse" role="button"
                            aria-expanded="true" aria-controls="collapseCardExample">
                                <h6 class="m-0 font-weight-bold text-white">Informasi Guru/Pegawai</h6>
                            </a>
                            <!-- Card Content - Collapse -->
                            <div class="collapse show" id="informasisiswa">
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tbody>

                                            <tr>
                                                <td>EWANUGK</td>
                                                <td>: <?php echo $siswa->nis; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Nama Lengkap</td>
                                                <td>: <span id="nm-siswa"><?php echo $siswa->nama_lengkap; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Asal Madrasah</td>
                                                <td>: <?php echo $siswa->nama_kelas; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>: <?php echo $siswa->email; ?></td>
                                            </tr>
                                            <tr>
                                                <td>TMT</td>
                                                <td>: <?php echo $siswa->tmt; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Nomor Telephone</td>
                                                <td>: <?php echo $siswa->no_tlp; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Lahir</td>
                                                <td>: <?php echo $siswa->tgl_lahir; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td>: <?php echo $siswa->alamat; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="card-body">
                    <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                        <!-- Card Header - Accordion -->

                        <a href="#informasisiswa" class="d-block card-header py-3"
                        data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #009991;"
                        aria-controls="collapseCardExample">
                        <h6 class="m-0 font-weight-bold text-white">Informasi Pembayaran Iuran LP. Ma'arif NU PCNU Gunungkidul</h6>
                        </a>

                        <!-- Card Content - Collapse -->
                        <div class="collapse show" id="informasisiswa">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <tbody>

                                        <tr>
                                            <td class="m-0 font-weight-bold text-dark">IURAN</td>
                                            <td class="m-0 font-weight-bold text-dark">NOMINAL</td>
                                        </tr>
                                        <tr>
                                            <td>a. Iuran Siswa Jenjang Madrasah Ibtidaiyah</td>
                                            <td width="800px">: Rp.1000</td>
                                        </tr>
                                        <tr>
                                            <td>b. Iuran Siswa Jenjang Madrasah Tsanawiyah/SMP</td>
                                            <td width="800px">: Rp.1000</td>
                                        </tr>
                                        <tr>
                                            <td>c. Iuran Kepala/Guru ASN Madrasah Bersertifikasi</td>
                                            <td width="800px">: Rp.20.000</td>
                                        </tr>
                                        <tr>
                                            <td>d. Iuran Kepala/Guru ASN Madrasah Belum Sertifikasi</td>
                                            <td width="800px">: Rp.15.000</td>
                                        </tr>
                                        <tr>
                                            <td>e. Iuran Kepala/Guru Madrasah Yayasan Bersertifikasi/Inpassing</td>
                                            <td width="800px">: Rp.10.000</td>
                                        </tr>
                                        <tr>
                                            <td>f. Iuran Kepala/Guru Madrasah Yayasan Belum Bersertifikasi</td>
                                            <td width="800px">: Rp.2000</td>
                                        </tr>
                                        <tr>
                                            <td class="m-0 font-weight-bold text-dark">SK YAYASAN</td>
                                            <td class="m-0 font-weight-bold text-dark">NOMINAL</td>
                                        </tr>
                                        <tr>
                                            <td>a. Penerbitan SK GTY/GTT/PTY/PTT Baru</td>
                                            <td width="800px">: Rp.50.000</td>
                                        </tr>
                                        <tr>
                                            <td>b. Perpanjangan SK Yayasan GTY/GTT/PTY/PTT</td>
                                            <td width="800px">: Rp.25.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($pembayaran_bulanan != null)
                    <div class="card-body">
                        <div class="card shadow mb-4 border-bottom-warning" id="tagihanbulanan" value="0">
                            <!-- Card Header - Accordion -->
                            <a href="#tagihanbulan" class="d-block card-header py-3"
                                data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #009991;"
                                aria-controls="collapseCardExample">
                                <h6 class="m-0 font-weight-bold text-white">Iuran Bulanan</h6>
                            </a>
                            <!-- Card Content - Collapse -->

                            <div class="collapse show" id="tagihanbulan">

                                <div class="table-responsive">
                                    <div class="card-body">
                                        <table class="table table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>Tahun Anggaran</th>
                                                    <th>Asal Madrasah</th>
                                                    <th>Jenis Pembayaran</th>
                                                    <th>Dibayar</th>
                                                    <th class="text-center">Status Bayar</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                            $id = 1;

                            foreach ($pembayaran_bulanan as $u) {
                            ?>
                                                <tr>
                                                    <td><?php echo $id++; ?></td>
                                                    <td><?php echo $u->tahun; ?></td>
                                                    <td><?php echo $u->nama_kelas; ?></td>
                                                    <td><?php echo $u->pembayaran; ?></td>
                                                    <td>Rp. {{ number_format($u->total_bayar) }}</td>
                                                    <td class="text-center">
                                                        @if ($u->status_bayar == 'Belum Lunas')
                                                            <span class="badge bg-label-danger" >Belum
                                                                Lunas</span>
                                                        @else
                                                            <span class="badge bg-label-primary"
                                                                style="width: 57%;">Lunas</span>
                                                        @endif
                                                    </td>
                                                    <td hidden id="getidtagihan">{{ $u->id }}</td>

                                                    <td>
                                                        @if ($u->status_bayar == 'Belum Lunas')
                                                            <a href="/pembayaran/spp/{{ $u->id }}"
                                                                class="btn btn-primary">Bayar</a>
                                                                <a href="/bulananPdf/{{$u->id}}" target="_blank"
                                                                    class="btn btn-danger">PDF</a>
                                                        @else
                                                            <button onclick="printExcelById()"
                                                                class="btn btn-success">Excel</button>
                                                                <a href="/bulananPdf/{{$u->id}}" target="_blank"
                                                                    class="btn btn-danger">PDF</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (!empty($pembayaran_lainya))
                <div class="card-body">
                    <div class="card shadow mb-4 border-bottom-info" id="tagihanlainya" value="0">
                        <!-- Card Header - Accordion -->
                        <a href="#tagihanlainya" class="d-block card-header py-3"
                            data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #009991;"
                            aria-controls="collapseCardExample">
                            <h6 class="m-0 font-weight-bold text-white">Tagihan Lainnya</h6>
                        </a>
                        <!-- Card Content - Collapse -->
                        <div class="collapse show" id="tagihanlainya">
                            <div class="table-responsive">
                                <div class="card-body">
                                    <table class="table table-striped" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>Tahun Anggaran</th>
                                                <th>Asal Madrasah</th>
                                                <th>Jenis Pembayaran</th>
                                                <th>Dibayar</th>
                                                <th class="text-center">Status Bayar</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $id = 1; @endphp
                                            @foreach ($pembayaran_lainya as $u)
                                                <tr>
                                                    <td hidden id="getIdLainya">{{ $u->id }}</td>
                                                    <td>{{ $id++ }}</td>
                                                    <td>{{ $u->tahun }}</td>
                                                    <td>{{ $u->nama_kelas }}</td>
                                                    <td>{{ $u->pembayaran }}</td>
                                                    <td>
                                                        @if ($u->status_payment == null)
                                                            Rp. 0
                                                        @else
                                                            Rp. {{ number_format($u->nilai) }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($u->status_payment == null)
                                                            <span class="badge bg-label-danger">Belum Lunas</span>
                                                        @elseif ($u->status_payment == 'Pending')
                                                            <span class="badge bg-label-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-label-primary">Lunas</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($u->status_payment == 'Pending')
                                                            <a href="{{ $u->pdf_url }}" class="btn btn-success" target="_blank">Invoice</a>
                                                        @elseif ($u->status_payment == 'Lunas')
                                                            <a href="/lainyaPdf/{{ $u->id }}" class="btn btn-danger">PDF</a>

                                                            @if (request()->user()->sk01_2025)
                                                                <a href="{{ asset('storage/dokumen/sk01_2025/' . request()->user()->sk01_2025) }}" class="btn btn-success">Download SK</a>
                                                            @endif

                                                            @if (request()->user()->role == 1)
                                                                <a href="/pdfSkJuli2025/{{ $u->id }}" class="btn btn-danger">PDF</a>
                                                            @endif

                                                            @if (request()->user()->skbfr2025)
                                                                <a href="{{ asset('storage/dokumen/skbfr2025/' . request()->user()->skbfr2025) }}" class="btn btn-success" target="_blank">Download SK</a>
                                                            @endif
                                                        @else
                                                            <a href="/pembayaran/payment/{{ $u->id }}" class="btn btn-primary">Bayar</a>
                                                            <a href="/lainyaPdf/{{ $u->id }}" class="btn btn-danger">PDF</a>

                                                            @if (request()->user()->role == 1)
                                                                <a href="/pdfSkJuli2025/{{ $u->id }}" class="btn btn-danger">PDF</a>
                                                            @endif

                                                            @auth
                                                                @if (strtolower($u->pembayaran) == 'pembayaran iuran januari-juni 2025')
                                                                    <a href="/invoice/add/{{ auth()->id() }}" class="btn btn-success">Invoice</a>
                                                                @endif
                                                            @endauth
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // $("#loading-image").hide();
        $('#kelas_id').change(function() {
            var klsid = $(this).val();
            if (klsid) {
                $.ajax({
                    type: "GET",
                    url: "/siswaByKelas/" + klsid,
                    dataType: 'JSON',
                    beforeSend: function() {
                        $("#loading-image").show();

                    },
                        success: function(res) {
                            if (res) {
                                $("#nis").empty();
                                $("#nis").append('<option>---Pilih Siswa---</option>');
                                $.each(res, function(kode, value) {
                                    $("#nis").append('<option value="' + value.nis + '">' + value.nama_lengkap + '</option>');
                                });
                            } else {
                                $("#nis").empty();
                            }
                            $("#loading-image").hide();
                        }
                });
            } else {
                $("#nis").empty();
            }
        });

        function printExcelById() {
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: "{{ url('cetakExcelById') }}/",
                data: {
                    tagihan_id: $("#getidtagihan").text(),
                },

                success: function(response) {
                    // console.log(response.file);
                    window.open(response.file, '_blank');
                },
                error: function() {
                    alert("error");
                }
            });
            return false;
        }

        function printExcelByIdLainya() {
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: "{{ url('cetakExcelById') }}/",
                data: {
                    tagihan_id: $("#getIdLainya").text(),
                },

                success: function(response) {
                    // console.log(response.file);
                    window.open(response.file, '_blank');
                },
                error: function() {
                    alert("error");
                }
            });
            return false;
        }
    </script>
@endsection
