{{-- Alert Component --}}
@php
    $type = $type ?? 'info';
@endphp
<div class="alert alert-{{ $type }}">
    {{ $slot }}
</div>
