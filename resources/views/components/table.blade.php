{{-- Table Component --}}
@php
    $striped = $striped ?? false;
    $size = $size ?? null;
@endphp
<table class="table {{ $striped ? 'table-striped' : '' }} {{ $size ? 'table-' . $size : '' }}">
    @if($headers)
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
    @endif
    
    <tbody>
        {{ $slot }}
    </tbody>
</table>
