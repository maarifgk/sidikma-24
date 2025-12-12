@props([
    'title' => '',
    'value' => '',
    'icon' => 'fa-circle'
])

<div class="d-flex align-items-center p-2 border rounded shadow-sm bg-white" style="min-height: 80px;">
    <div class="avatar">
        <div class="avatar-initial rounded-circle shadow"
             style="background-color: #0a48b3; color: white; width: 40px; height: 40px;
             display:flex; align-items:center; justify-content:center;">
            <i class="fa-solid {{ $icon }}"></i>
        </div>
    </div>
    <div class="ms-3">
        <div class="small text-muted">{{ $title }}</div>
        <h6 class="mb-0">{{ $value }}</h6>
    </div>
</div>
