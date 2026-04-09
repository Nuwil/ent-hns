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
            height: 210mm;
            margin: 0;
            padding: 0;
            background: white;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page {
            width: 148mm;
            height: 210mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10mm;
        }

        .rx-container {
            width: 100%;
            max-width: 120mm;
        }

        /* ── Prescriptions Grid Table ─────────────────────────────────── */
        .rx-prescription-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0 4mm;
            width: 100%;
        }

        .rx-item {
            padding: 2mm;
            border-bottom: 1px solid #ccc;
            border-radius: 2px;
            min-width: 0;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .rx-item-number {
            font-size: 9pt;
            color: #555;
            margin-bottom: 2mm;
            font-weight: bold;
        }

        .rx-drug-name {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.2;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            margin-bottom: 2mm;
        }

        .rx-dosage {
            font-size: 9pt;
            margin-top: 1mm;
            font-style: italic;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .rx-qty {
            font-size: 9pt;
            color: #333;
            margin-top: 1mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .rx-empty {
            color: #999;
            font-style: italic;
            font-size: 11pt;
            text-align: center;
            padding: 20mm;
            grid-column: 1 / -1;
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
            html, body {
                width: 148mm;
                height: 210mm;
                margin: 0;
                padding: 0;
            }
            .print-btn-bar {
                display: none !important;
            }
            body {
                display: flex;
            }
        }
    </style>
</head>
<body>
    @php
        $rxCount = count($prescriptions ?? []);
        $globalIndex = 1;
    @endphp

    <div class="page">
        <div class="rx-container">
            <div class="rx-prescription-grid">
                @forelse($prescriptions as $index => $rx)
                    <div class="rx-item">
                        <div class="rx-item-number">{{ $globalIndex++ }}.</div>
                        <div class="rx-drug-name">{{ $rx['drug'] ?? '' }}</div>
                        @if(!empty($rx['dosage']))
                            <div class="rx-dosage">{{ $rx['dosage'] }}</div>
                        @endif
                        @if(!empty($rx['quantity']))
                            <div class="rx-qty">Qty: {{ $rx['quantity'] }}</div>
                        @endif
                    </div>
                @empty
                    <div class="rx-empty">
                        No prescriptions recorded.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── PRINT BUTTON ─────────────────────────────────────────── --}}
    <div class="print-btn-bar">
        <button class="close-btn" onclick="window.close()">✕ Close</button>
        <button class="print-btn" onclick="window.print()">🖨 Print</button>
    </div>

</body>
</html>