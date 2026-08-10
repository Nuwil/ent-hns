@php
    $hasInfo = $patient->allergies || $patient->medical_history || $patient->vaccine_history;
@endphp

@if(!$hasInfo)
    <div class="text-muted text-center py-3" style="font-size:13px;">
        <i class="bi bi-clipboard2 me-2"></i>No critical information available.
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="ci-field">
            <div class="ci-field-label ci-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Allergies</div>
            @if($patient->allergies)
                <div class="ci-field-val text-danger fw-semibold">{{ $patient->allergies }}</div>
            @else
                <div class="ci-field-val text-muted small">No information available</div>
            @endif
        </div>

        <div class="ci-field">
            <div class="ci-field-label ci-blue"><i class="bi bi-clipboard2-pulse me-1"></i>Medical History</div>
            @if($patient->medical_history)
                <div class="ci-field-val" style="white-space:pre-line;line-height:1.6;font-size:13px;">
                    {{ $patient->medical_history }}</div>
            @else
                <div class="ci-field-val text-muted small">No information available</div>
            @endif
        </div>

        <div class="ci-field">
            <div class="ci-field-label ci-green"><i class="bi bi-shield-plus me-1"></i>Vaccine History</div>
            @if($patient->vaccine_history)
                <div class="ci-field-val" style="white-space:pre-line;line-height:1.6;font-size:13px;">
                    {{ $patient->vaccine_history }}</div>
            @else
                <div class="ci-field-val text-muted small">No information available</div>
            @endif
        </div>
    </div>
@endif