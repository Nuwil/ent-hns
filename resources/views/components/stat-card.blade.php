{{-- Stat Card Component --}}
@php
    $description = $description ?? null;
    $color = $color ?? 'var(--color-primary)';
@endphp
<div class="stat-card">
    <h4>{{ $title ?? 'Stat' }}</h4>
    <p class="stat-number" style="color: {{ $color }};">{{ $value ?? 0 }}</p>
    @if($description)
        <p>{{ $description }}</p>
    @endif
</div>
