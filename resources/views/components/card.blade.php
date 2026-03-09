{{-- Card Component --}}
@php
    $header = $header ?? null;
    $footer = $footer ?? null;
    $class = $class ?? '';
@endphp
<div class="card {{ $class }}">
    @if($header)
        <div class="card-header">
            <h3>{{ $header }}</h3>
        </div>
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
