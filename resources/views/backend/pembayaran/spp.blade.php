@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-md-9">
            <div class="card table-responsive">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">
                        <b>{{ $title }}</b>
                    </h5>

                </div>
                <div class="container mt-4 ">
                    <table id="data" class="table table-striped ">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tahun</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Invoice</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($spp as $a)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td width="auto">{{ $a->nama_lengkap }}</td>
                                    <td width="20%">{{ $a->nama_bulan }} {{ $a->tahun }}</td>
                                    <td width="auto">Rp. {{ number_format($a->nilai) }}</td>
                                    <td width="auto">{{ $a->status }}</td>
                                    <td width="auto">
                                        @if ($a->status == 'Pending')
                                            <a href="{{ $a->pdf_url }}" class="btn btn-success"
                                                target="_blank">Invoice</a>
                                        @elseif ($a->status == 'Lunas')
                                            <a href="/bulananPdfById/{{$a->id}}" target="_blank"
                                                                    class="btn btn-danger">PDF</a>
                                        @endif
                                    </td>
                                    <td width="auto">{{ $a->created_at }}</td>
                                    <td>
                                        @if ($a->status == 'Pending')
                                            <button type="button" class="btn rounded-pill btn-icon btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#delete{{ $a->id }}">
                                                <span class="fa fa-trash"></span>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($a->status == 'Failed')
                                            <button type="button" class="btn rounded-pill btn-icon btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#delete{{ $a->id }}">
                                                <span class="fa fa-trash"></span>
                                            </button>
                                        @endif
                                    </td>
                                    <div class="modal fade" id="delete{{ $a->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="deletemodal" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addNewDonaturLabel">Hapus Pembayaran Failed
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Anda yakin ingin menghapus {{ $a->pembayaran }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <a href="{{ url('/pembayaran/deleteSpp', $a->id) }} "
                                                            class="btn btn-primary">Hapus</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pembayaran</h4>
                </div>
                <div class="card-body">
                    @php($defaultMetodePembayaran = request()->user()->role != 1 ? 'Online' : 'Manual')
                    <form id="payment-form" class="" method="post" action="/sppAddProses">
                        @csrf
                        <input type="text" name="tagihan_id" id="" value="{{ $tagihan_id }}" hidden>
                        <input type="text" name="getNilai" id="" value="{{ $getNilai }}" hidden>
                        <input type="text" name="kelas_id" id="" value="{{ $kelas_id }}" hidden>
                        <input type="text" name="user_id" id="" value="{{ $user_id }}" hidden>
                        {{-- midtrans --}}
                        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
                        <input type="hidden" name="result_type" id="result-type" value="">
                        <input type="hidden" name="result_data" id="result-data" value="">
                        <div class="row">


                            <div class="col-md-12">
                                <label>Bulan</label>
                                <select class="form-control selectpicker" title="Bulan" name="bulan[]" id="bulan"
                                    data-actions-box="true" data-virtual-scroll="false" data-live-search="true" multiple
                                    required>
                                    @foreach ($bulan as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_bulan }}</option>
                                    @endforeach
                                </select>
                                <br>
                            </div>
                            <div class="col-md-12">

                                <label>Jumlah Total</label>
                                <td colspan="4"><b><input class="form-control" type="text" name="nilai"
                                            id="total" readonly></b>
                                </td>

                                <br>
                            </div>
                            <div class="col-md-12">
                                <label>Metode Pembayaran</label>
                                <input type="hidden" id="metode_pembayaran" name="metode_pembayaran"
                                    value="{{ $defaultMetodePembayaran }}">
                                <input type="text" class="form-control" value="{{ $defaultMetodePembayaran }}"
                                    readonly>
                            </div>
                            <div class="col-md-12">
                                <div class="form-body">
                                    <br><br> &nbsp;<button type="submit" name="bayar" id="pay-button"
                                        class="btn btn-primary mb-2">BAYAR</button>
                                    <a class="btn btn-info mb-2"
                                        href="/pembayaran/search?&kelas_id={{ $kelas_id }}&nis={{ $nis }}">Kembali</a>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#bulan').change(function() {
                // console.log(nilai);
                var total = parseInt($('#bulan').val().length) * parseInt({{ $getNilai }});
                var reverse = total.toString().split('').reverse().join(''),
                    ribuan = reverse.match(/\d{1,3}/g);
                ribuan = ribuan.join('.').split('').reverse().join('');
                $("#total").val('Rp. ' + ribuan);
            })
        })
    
    </script>
    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="{{ Helper::apk()->clientKey }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        $('#pay-button').click(function(event) {
            if ($('#metode_pembayaran').val() == "Online") {
                event.preventDefault();
                $(this).attr("disabled", "disabled");
                var _total = $('#total').val();
                $.ajax({
                    method: "POST",
                    url: '/getToken',
                    cache: false,
                    data: {
                        _token: $('#_token').val(),
                        total: _total.replace("Rp.", '').replace(".", '').replace(".", '')

                    },
                    success: function(data) {
                        //location = data;
                        console.log('token = ' + data);

                        var resultType = document.getElementById('result-type');
                        var resultData = document.getElementById('result-data');

                        function changeResult(type, data) {
                            $("#result-type").val(type);
                            $("#result-data").val(JSON.stringify(data));
                            //resultType.innerHTML = type;
                            //resultData.innerHTML = JSON.stringify(data);
                        }
                        snap.pay(data, {

                            onSuccess: function(result) {
                                changeResult('success', result);
                                console.log(result.status_message);
                                console.log(result);
                                $("#payment-form").submit();
                            },
                            onPending: function(result) {
                                changeResult('pending', result);
                                console.log(result.status_message);
                                $("#payment-form").submit();
                            },
                            onError: function(result) {
                                changeResult('error', result);
                                console.log(result.status_message);
                                $("#payment-form").submit();
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
