@props(['title' => '', 'value' => '', 'icon' => 'fa-circle'])

<div class="col-xl-3 col-md-6">
    <div class="card shadow-sm h-100 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-uppercase text-muted">{{ $title }}</small>
                <h4 class="fw-bold mb-0">{{ $value }}</h4>
            </div>
            <i class="fa-solid {{ $icon }} fa-2x text-secondary"></i>
        </div>
    </div>
</div>
