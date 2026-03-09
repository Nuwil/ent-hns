{{-- Badge Component --}}
@php
    $type = $type ?? 'primary';
@endphp
<span class="badge badge-{{ $type }}">
    {{ $slot }}
</span>
