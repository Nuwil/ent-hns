<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription — {{ $patient->full_name }}</title>
    <style>
        /* ── A5 Page Setup ─────────────────────────────────────── */
        @page {
            size: A5 portrait;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            background: white;
            color: #000;
            font-size: 11pt;
        }

        /* ── Header ────────────────────────────────────────────── */
        .rx-header {
            text-align: center;
            padding: 12mm 10mm 6mm;
            border-bottom: 2px solid #000;
        }
        .rx-doctor-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .rx-specialty {
            font-size: 9.5pt;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .rx-clinic-name {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 6px;
        }
        .rx-clinic-address {
            font-size: 9pt;
            margin-top: 2px;
            line-height: 1.4;
        }
        .rx-clinic-phone {
            font-size: 9pt;
            margin-top: 2px;
        }
        .rx-affiliations {
            font-size: 8.5pt;
            margin-top: 5px;
            font-weight: bold;
            line-height: 1.4;
        }
        .rx-affiliations span { font-weight: normal; font-style: italic; }

        /* ── Patient Info ───────────────────────────────────────── */
        .rx-patient-info {
            padding: 5mm 10mm 3mm;
            border-bottom: 1px solid #ccc;
        }
        .rx-patient-row {
            display: flex;
            gap: 10mm;
            margin-bottom: 3mm;
            font-size: 10pt;
        }
        .rx-patient-field {
            display: flex;
            align-items: flex-end;
            gap: 2mm;
            flex: 1;
        }
        .rx-patient-label { font-size: 9.5pt; white-space: nowrap; }
        .rx-patient-value {
            flex: 1;
            border-bottom: 1px solid #000;
            min-width: 20mm;
            padding-bottom: 1px;
            font-size: 10pt;
        }
        .rx-patient-value.short { max-width: 18mm; }

        /* ── Rx Body ────────────────────────────────────────────── */
        .rx-body {
            padding: 4mm 10mm;
            min-height: 80mm;
        }
        .rx-symbol {
            font-family: 'Times New Roman', serif;
            font-size: 32pt;
            font-style: italic;
            font-weight: bold;
            margin-bottom: 3mm;
            line-height: 1;
        }

        /* ── Prescriptions List ─────────────────────────────────── */
        .rx-item {
            margin-bottom: 5mm;
            padding-left: 5mm;
        }
        .rx-item-number {
            font-size: 9pt;
            color: #555;
            margin-bottom: 1mm;
        }
        .rx-drug-name {
            font-size: 11pt;
            font-weight: bold;
        }
        .rx-dosage {
            font-size: 10pt;
            padding-left: 5mm;
            margin-top: 1mm;
            font-style: italic;
        }
        .rx-qty {
            font-size: 9.5pt;
            padding-left: 5mm;
            color: #333;
        }
        .rx-divider {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 2mm 0 4mm;
        }

        /* ── Instructions ───────────────────────────────────────── */
        .rx-instructions {
            padding: 3mm 10mm 3mm;
            border-top: 1px dashed #ccc;
        }
        .rx-instructions-label {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 2mm;
        }
        .rx-instructions-text {
            font-size: 10pt;
            line-height: 1.5;
        }

        /* ── Bible Quote ────────────────────────────────────────── */
        .rx-quote {
            text-align: center;
            padding: 4mm 12mm;
            color: #1a56a0;
            font-style: italic;
            font-size: 9.5pt;
            line-height: 1.6;
        }

        /* ── Footer ─────────────────────────────────────────────── */
        .rx-footer {
            padding: 3mm 10mm 8mm;
            border-top: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .rx-followup { font-size: 9.5pt; }
        .rx-followup-label {
            font-weight: bold;
            margin-bottom: 4mm;
        }
        .rx-followup-date {
            border-bottom: 1px solid #000;
            min-width: 30mm;
            display: inline-block;
            padding-bottom: 1px;
        }
        .rx-license {
            font-size: 9pt;
            text-align: right;
            line-height: 1.8;
        }
        .rx-license-row {
            display: flex;
            gap: 2mm;
            align-items: flex-end;
        }
        .rx-license-label { white-space: nowrap; }
        .rx-license-value {
            border-bottom: 1px solid #000;
            min-width: 25mm;
            text-align: left;
            padding-bottom: 1px;
        }
        .rx-md-suffix {
            font-size: 11pt;
            font-weight: bold;
            text-align: right;
            margin-bottom: 2mm;
        }

        /* ── Print Button (hidden on print) ─────────────────────── */
        .print-btn-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .print-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37,99,235,0.4);
        }
        .print-btn:hover { background: #1d4ed8; }
        .close-btn {
            background: #64748b;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {
            .print-btn-bar { display: none !important; }
            body { width: 148mm; }
        }
    </style>
</head>
<body>

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="rx-header">
        <div class="rx-doctor-name">{{ $doctor->full_name }}, M.D.</div>
        <div class="rx-specialty">Ear, Nose, Throat, Head and Neck Surgery</div>
        <div class="rx-clinic-name">New Session Diagnostic Center</div>
        <div class="rx-clinic-address">
            Lower Session Road, Baguio City<br>
            (in between Pelizloy Centrum &amp; Pines Arcade)
        </div>
        <div class="rx-clinic-phone">246-9905</div>
        <div class="rx-affiliations">
            Hospital Affiliations:<br>
            <span>
                *Baguio General Hospital &amp; Medical Center
                &nbsp;*Baguio Medical Center<br>
                *Notre Dame de Chartres Hospital
                &nbsp;*Parkway Medical Center
                &nbsp;*Pines City Doctor's Hospital
            </span>
        </div>
    </div>

    {{-- ── PATIENT INFO ─────────────────────────────────────────── --}}
    <div class="rx-patient-info">
        <div class="rx-patient-row">
            <div class="rx-patient-field" style="flex:2">
                <span class="rx-patient-label">Patient:</span>
                <span class="rx-patient-value">{{ $patient->full_name }}</span>
            </div>
            <div class="rx-patient-field">
                <span class="rx-patient-label">Age:</span>
                <span class="rx-patient-value short">{{ $patient->age }}</span>
            </div>
            <div class="rx-patient-field">
                <span class="rx-patient-label">Sex:</span>
                <span class="rx-patient-value short">{{ ucfirst($patient->gender) }}</span>
            </div>
        </div>
        <div class="rx-patient-row">
            <div class="rx-patient-field" style="flex:2">
                <span class="rx-patient-label">Address:</span>
                <span class="rx-patient-value">
                    {{ collect([$patient->city, $patient->province])->filter()->implode(', ') ?: '—' }}
                </span>
            </div>
            <div class="rx-patient-field">
                <span class="rx-patient-label">Date:</span>
                <span class="rx-patient-value">{{ now()->setTimezone('Asia/Manila')->format('m/d/Y') }}</span>
            </div>
        </div>
    </div>

    {{-- ── Rx BODY ──────────────────────────────────────────────── --}}
    <div class="rx-body">
        <div class="rx-symbol">&#x211E;</div>

        @forelse($prescriptions as $index => $rx)
            <div class="rx-item">
                <div class="rx-drug-name">{{ $rx['drug'] ?? '' }}</div>
                @if(!empty($rx['dosage']))
                    <div class="rx-dosage">Sig: {{ $rx['dosage'] }}</div>
                @endif
                @if(!empty($rx['quantity']))
                    <div class="rx-qty">Qty: #{{ $rx['quantity'] }}</div>
                @endif
            </div>
            @if(!$loop->last)
                <hr class="rx-divider">
            @endif
        @empty
            <div style="color:#999;font-style:italic;font-size:10pt;padding-left:5mm">
                No prescriptions recorded.
            </div>
        @endforelse
    </div>

    {{-- ── INSTRUCTIONS ─────────────────────────────────────────── --}}
    @if(!empty($instructions))
        <div class="rx-instructions">
            <div class="rx-instructions-label">Instructions:</div>
            <div class="rx-instructions-text">{{ $instructions }}</div>
        </div>
    @endif

    {{-- ── BIBLE QUOTE ──────────────────────────────────────────── --}}
    <div class="rx-quote">
        "The grass withers, the flower fades, but the<br>
        words of our God stands forever"<br>
        <em>Isa. 40:8</em>
    </div>

    {{-- ── FOOTER ──────────────────────────────────────────────── --}}
    <div class="rx-footer">
        <div class="rx-followup">
            <div class="rx-followup-label">Date of follow up:</div>
            <span class="rx-followup-date">
                {{ $followUpDate ?? '' }}
            </span>
        </div>
        <div>
            <div class="rx-md-suffix">, M.D.</div>
            <div class="rx-license">
                <div class="rx-license-row">
                    <span class="rx-license-label">Lic. No.</span>
                    <span class="rx-license-value">80812</span>
                </div>
                <div class="rx-license-row">
                    <span class="rx-license-label">PTR No.</span>
                    <span class="rx-license-value"></span>
                </div>
                <div class="rx-license-row">
                    <span class="rx-license-label">S2 No.</span>
                    <span class="rx-license-value"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PRINT BUTTON ─────────────────────────────────────────── --}}
    <div class="print-btn-bar">
        <button class="close-btn" onclick="window.close()">✕ Close</button>
        <button class="print-btn" onclick="window.print()">🖨 Print Prescription</button>
    </div>

</body>
</html>