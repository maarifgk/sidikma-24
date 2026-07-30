@extends('backend.layout.base')

@section('content')
<div class="col-md-12 col-lg-4 mb-4">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-8">
                        <div class="card-body">
                            <h6 class="card-title mb-1 text-nowrap">
                            </h6>
                            <small class="d-block mb-3 text-nowrap">Total Pembayaran</small>
                            <h5 class="card-title text-primary mb-1">Rp. </h5>
                            {{-- <small class="d-block mb-4 pb-1 text-muted">78% of target</small> --}}
                            <a href="/profile" class="btn btn-sm btn-primary">View profile</a>
                        </div>
                    </div>
                    <div class="col-4 pt-3 ps-0">
                        @if (request()->user()->image != null)
                            <img src="{{ asset('') }}storage/images/users/{{ request()->user()->image }}" width="120"
                                height="100" style="margin-bottom: 30%;" class="rounded-start" alt="">
                        @else
                            <img src="{{ asset('') }}storage/images/users/users.png" width="120"
                                height="100" style="margin-bottom: 30%;" class="rounded-start" alt="">
                        @endif
                    </div>
                </div>
            </div>
        </div>
@endsection
