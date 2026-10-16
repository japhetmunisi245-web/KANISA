<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>{{ $report->title }} - Ripoti</title>
    <style>
        @page { size: A4; margin: 18mm 18mm 20mm 18mm; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 11pt;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .logo {
            max-height: 64px;
            margin-bottom: 6px;
        }
        .church-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .church-address {
            color: #64748b;
            font-size: 9.5pt;
        }
        .report-title {
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
            font-size: 14pt;
            margin: 20px 0;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .meta td {
            width: 50%;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: top;
        }
        .label {
            display: block;
            color: #64748b;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .content-title {
            color: #64748b;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .content {
            text-align: justify;
            min-height: 360px;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .signatures td {
            width: 50%;
            padding-right: 35px;
            vertical-align: top;
        }
        .line {
            border-top: 1px solid #0f172a;
            padding-top: 7px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 7px;
            color: #64748b;
            font-size: 8pt;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists($logoPath ?? ''))
            <img class="logo" src="{{ $logoPath }}" alt="Logo">
        @endif
        <div class="church-name">{{ $churchName }}</div>
        <div class="church-address">{{ $address }}</div>
    </div>

    <div class="report-title">{{ $report->title }}</div>

    <table class="meta">
        <tr>
            <td>
                <span class="label">Idara</span>
                {{ $report->department->name_sw ?? $report->department->name ?? 'Haijatajwa' }}
            </td>
            <td>
                <span class="label">Aina ya Ripoti</span>
                {{ $typeLabels[$report->type] ?? ucfirst($report->type) }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Imeandaliwa Na</span>
                {{ $report->user->name ?? 'Haijatajwa' }}
            </td>
            <td>
                <span class="label">Tarehe ya Ripoti</span>
                {{ $report->report_date?->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <div class="content-title">Maelezo ya Ripoti</div>
    <div class="content">
        {!! nl2br(e($report->content)) !!}
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="line">Aliyeandaa</div>
                <div>{{ $report->user->name ?? '' }}</div>
            </td>
            <td>
                <div class="line">Mchungaji / Kiongozi</div>
                <div>Tarehe: __________________</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Ripoti hii imetolewa kupitia mfumo wa {{ $churchName }}.
    </div>
</body>
</html>
