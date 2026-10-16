<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report->title }} - Ripoti</title>
    <style>
        :root {
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --amber: #f59e0b;
            --paper: #ffffff;
            --bg: #f3f4f6;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.65;
        }
        .toolbar {
            max-width: 920px;
            margin: 24px auto 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 0 16px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--text);
            background: #fff;
            font-weight: 700;
            font-size: 14px;
        }
        .btn-primary {
            background: var(--amber);
            color: #fff;
            border-color: var(--amber);
        }
        .paper {
            max-width: 920px;
            margin: 0 auto 32px;
            background: var(--paper);
            min-height: 1120px;
            padding: 48px 56px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .10);
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double var(--text);
            padding-bottom: 16px;
            margin-bottom: 28px;
        }
        .logo {
            max-height: 72px;
            margin-bottom: 8px;
        }
        .church-name {
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .church-address {
            color: var(--muted);
            font-size: 14px;
        }
        .report-title {
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 26px 0 20px;
            font-size: 22px;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .meta td {
            border: 1px solid var(--border);
            padding: 10px 12px;
            vertical-align: top;
        }
        .label {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .content {
            white-space: pre-line;
            text-align: justify;
            font-size: 16px;
            margin-top: 16px;
            min-height: 420px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            margin-top: 64px;
        }
        .line {
            border-top: 1px solid var(--text);
            padding-top: 8px;
            font-weight: 700;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .paper { box-shadow: none; border-radius: 0; margin: 0; max-width: none; }
        }
        @media (max-width: 720px) {
            .toolbar { flex-direction: column; }
            .paper { padding: 28px 20px; border-radius: 0; }
            .signatures { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a class="btn" href="{{ route('dashboard') }}">Rudi Dashboard</a>
        <a class="btn btn-primary" href="{{ route('reports.download', $report->id) }}">Pakua PDF</a>
    </div>

    <main class="paper">
        <header class="header">
            @if($logoUrl)
                <img class="logo" src="{{ $logoUrl }}" alt="Logo">
            @endif
            <div class="church-name">{{ $churchName }}</div>
            <div class="church-address">{{ $address }}</div>
        </header>

        <h1 class="report-title">{{ $report->title }}</h1>

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

        <section>
            <span class="label">Maelezo ya Ripoti</span>
            <div class="content">{{ $report->content }}</div>
        </section>

        <div class="signatures">
            <div>
                <div class="line">Aliyeandaa</div>
                <div>{{ $report->user->name ?? '' }}</div>
            </div>
            <div>
                <div class="line">Mchungaji / Kiongozi</div>
                <div>Tarehe: __________________</div>
            </div>
        </div>
    </main>
</body>
</html>
