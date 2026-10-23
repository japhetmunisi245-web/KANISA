<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Barua Rasmi - Philadelphia Church</title>
    <style>
        @page { size: A4; margin: 12mm 14mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #253044;
            font-size: 9.8pt;
            line-height: 1.38;
            background: #ffffff;
        }
        .page {
            position: relative;
            width: 100%;
            padding: 0;
            overflow: hidden;
            page-break-after: auto;
            page-break-inside: avoid;
        }
        .topbar {
            width: 100%;
            display: table;
            table-layout: fixed;
            margin-bottom: 8mm;
        }
        .brand-cell,
        .contact-cell {
            display: table-cell;
            vertical-align: top;
        }
        .brand-cell {
            width: 54%;
        }
        .contact-cell {
            width: 46%;
        }
        .brand-mark {
            display: table;
        }
        .brand-logo,
        .brand-text {
            display: table-cell;
            vertical-align: middle;
        }
        .brand-logo img {
            width: 16mm;
            height: 16mm;
            object-fit: contain;
        }
        .brand-fallback {
            width: 16mm;
            height: 16mm;
            border-radius: 8px;
            border: 2px solid #0ea5c6;
            text-align: center;
            color: #0f7fa6;
            font-weight: 900;
            font-size: 14pt;
            line-height: 16mm;
        }
        .brand-text {
            padding-left: 10px;
        }
        .brand-name {
            font-size: 12pt;
            font-weight: 800;
            color: #0f7fa6;
            text-transform: uppercase;
            letter-spacing: 0;
        }
        .brand-sub {
            font-size: 7.4pt;
            color: #334155;
            font-weight: 700;
            margin-top: 1px;
        }
        .contact-cell {
            text-align: right;
            font-size: 7.6pt;
            color: #526070;
            line-height: 1.45;
            padding-left: 8mm;
        }
        .contact-title {
            color: #0f2747;
            font-weight: 800;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .meta-line {
            width: 100%;
            display: table;
            margin-bottom: 3.8mm;
            font-size: 9pt;
        }
        .recipient-block,
        .date-block {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .date-block { text-align: right; }
        .label {
            color: #64748b;
            font-size: 7.4pt;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: .5px;
        }
        .value {
            color: #1e293b;
            font-weight: 700;
        }
        .ref {
            color: #0f7fa6;
            font-weight: 800;
        }
        .recipient-address {
            margin-bottom: 4mm;
            line-height: 1.42;
        }
        .recipient-address .value {
            display: block;
            margin-top: 1mm;
            white-space: pre-line;
        }
        .subject {
            margin: 4mm 0 3.5mm;
            font-size: 10.4pt;
            font-weight: 800;
            color: #0f2747;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .letter-body {
            text-align: justify;
            white-space: pre-line;
            min-height: 42mm;
            max-height: 66mm;
            overflow: hidden;
        }
        .closing {
            margin-top: 4mm;
        }
        .signature-area {
            width: 100%;
            display: table;
            margin-top: 2.5mm;
        }
        .signature-cell,
        .stamp-cell {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
        }
        .signature-img {
            max-width: 130px;
            max-height: 52px;
            display: block;
            margin-bottom: 2mm;
        }
        .signature-space {
            height: 9mm;
        }
        .signature-line {
            width: 56mm;
            border-bottom: 1.2px solid #263244;
            margin-bottom: 2mm;
        }
        .signer-name {
            font-weight: 800;
            color: #0f2747;
            text-transform: uppercase;
        }
        .signer-title,
        .signer-org {
            font-size: 8.5pt;
            color: #526070;
        }
        .signer-org {
            color: #0f7fa6;
            font-weight: 800;
        }
        .stamp-cell {
            text-align: right;
        }
        .stamp-img {
            width: 52mm;
            height: 52mm;
            display: inline-block;
            opacity: .95;
        }
    </style>
</head>
<body>
    @php
        $letterDate = $letter->approved_at ?: $letter->created_at;
        $displayDate = \Illuminate\Support\Carbon::parse($letterDate)->format('d F Y');
        $cleanAddress = $address ?? 'P.O. BOX 3236, DAR ES SALAAM';
        $stamp = $stampData ?? [];
    @endphp

    <div class="page">
        <div class="topbar">
            <div class="brand-cell">
                <div class="brand-mark">
                    <div class="brand-logo">
                        @if(!empty($logoBase64))
                            <img src="{{ $logoBase64 }}" alt="EAGT Logo">
                        @else
                            <div class="brand-fallback">E</div>
                        @endif
                    </div>
                    <div class="brand-text">
                        <div class="brand-name">Philadelphia Church</div>
                        <div class="brand-sub">E.A.G.T Tanzania</div>
                    </div>
                </div>
            </div>
            <div class="contact-cell">
                <div class="contact-title">{{ $churchName }}</div>
                <div>{{ $cleanAddress }}</div>
                <div>Dar es Salaam, Tanzania</div>
                <div>Kumb. {{ $letter->reference_number ?? 'EAGT/KBP/---' }}</div>
            </div>
        </div>

        <div class="meta-line" style="margin-bottom: 6mm;">
            <div class="recipient-block">
                <div class="label">Tarehe</div>
            </div>
            <div class="date-block">
                <div class="value">{{ $displayDate }}</div>
            </div>
        </div>

        <div class="meta-line" style="margin-bottom: 6mm;">
            <div class="recipient-block">
                <div class="label">Reference No.</div>
                <div class="ref">{{ $letter->reference_number ?? 'EAGT/KBP/---' }}</div>
            </div>
            <div class="date-block">
                <div class="label">Aina ya Barua</div>
                <div class="value">{{ $letter->letter_type ?? 'Barua Rasmi' }}</div>
            </div>
        </div>

        <div class="recipient-address">
            <div class="label">Anwani ya Mwandikiwa</div>
            <div class="value">{!! nl2br(e($letter->recipient ?: 'Haijawekwa')) !!}</div>
        </div>

        <div class="subject">Re: {{ $letter->subject }}</div>

        <div class="letter-body">{!! nl2br(e($letter->body)) !!}</div>

        <div class="closing">
            <div>Wako katika huduma ya Bwana,</div>
            <div class="signature-area">
                <div class="signature-cell">
                    @if(!empty($signatureBase64))
                        <img src="{{ $signatureBase64 }}" class="signature-img" alt="Saini">
                    @else
                        <div class="signature-space"></div>
                    @endif
                    <div class="signature-line"></div>
                    <div class="signer-name">{{ $pastorName }}</div>
                    <div class="signer-title">{{ $pastorTitle }}</div>
                    <div class="signer-org">Philadelphia Church (EAGT)</div>
                </div>
                <div class="stamp-cell">
                    <img src="{{ $stampSvgBase64 }}" class="stamp-img" alt="Muhuri">
                </div>
            </div>
        </div>

    </div>
</body>
</html>
