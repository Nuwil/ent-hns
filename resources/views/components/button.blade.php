{{-- Button Component --}}
@php
    $variant = $variant ?? 'primary';
    $size = $size ?? null;
    $block = $block ?? false;
@endphp
<button 
    class="btn btn-{{ $variant }} {{ $size ? 'btn-' . $size : '' }} {{ $block ? 'btn-block' : '' }}"
    {{ $attributes->merge(['type' => 'button']) }}
>
    {{ $slot }}
</button>
