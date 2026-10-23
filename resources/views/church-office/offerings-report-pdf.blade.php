<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px; } body { font-family: DejaVu Sans, sans-serif; color: #17213d; font-size: 11px; }
        h1 { margin: 0 0 6px; font-size: 20px; } h2 { font-size: 14px; margin: 24px 0 8px; } .period { color: #5e6d87; margin: 0 0 16px; }
        .summary { width: 100%; margin: 0 0 18px; border-collapse: separate; border-spacing: 7px; } .summary td { background: #f3f6fa; padding: 10px; width: 25%; }
        .summary .label { color: #5e6d87; display: block; margin-bottom: 4px; } .summary .balance { background: #e9f8ef; color: #12633d; }
        table { border-collapse: collapse; width: 100%; } th, td { border-bottom: 1px solid #dfe5ef; padding: 8px 6px; text-align: left; }
        th { background: #f3f6fa; color: #53627d; font-size: 10px; } .amount { text-align: right; font-weight: bold; } .empty { color: #71809a; text-align: center; padding: 16px; }
        .footer { margin-top: 24px; color: #71809a; font-size: 9px; }
    </style>
</head>
<body>
    <h1>{{ $title }} — Mfuko wa Kanisa</h1>
    <p class="period">Kuanzia {{ $start->format('d/m/Y') }} hadi {{ $end->format('d/m/Y') }}</p>
    <table class="summary"><tr><td><span class="label">Sadaka za ndani</span><strong>TZS {{ number_format($internalOfferingTotal, 2) }}</strong></td><td><span class="label">Sadaka za nje (si salio)</span><strong>TZS {{ number_format($externalOfferingTotal, 2) }}</strong></td><td><span class="label">Jumla ya matumizi</span><strong>TZS {{ number_format($expenseTotal, 2) }}</strong></td><td class="balance"><span class="label">Salio la mfuko wa kanisa</span><strong>TZS {{ number_format($balance, 2) }}</strong></td></tr></table>
    <h2>Sadaka zilizorekodiwa</h2>
    <table><thead><tr><th>Tarehe</th><th>Aina ya sadaka</th><th>Eneo la sadaka</th><th>Njia ya malipo</th><th class="amount">Kiasi (TZS)</th></tr></thead><tbody>@forelse($offerings as $offering)<tr><td>{{ $offering->offering_date->format('d/m/Y') }}</td><td>{{ $offering->offering_type }}</td><td>{{ $offering->fund_scope === 'external' ? 'Nje ya kanisa' : 'Ndani ya kanisa' }}</td><td>{{ $offering->payment_method }}</td><td class="amount">{{ number_format($offering->amount, 2) }}</td></tr>@empty<tr><td colspan="5" class="empty">Hakuna sadaka iliyorekodiwa kwenye kipindi hiki.</td></tr>@endforelse</tbody></table>
    <h2>Matumizi ya kanisa</h2>
    <table><thead><tr><th>Tarehe</th><th>Aina ya matumizi</th><th>Maelezo</th><th class="amount">Kiasi (TZS)</th></tr></thead><tbody>@forelse($expenses as $expense)<tr><td>{{ $expense->expense_date->format('d/m/Y') }}</td><td>{{ $expense->expense_type }}</td><td>{{ $expense->notes ?: '-' }}</td><td class="amount">{{ number_format($expense->amount, 2) }}</td></tr>@empty<tr><td colspan="4" class="empty">Hakuna matumizi yaliyorekodiwa kwenye kipindi hiki.</td></tr>@endforelse</tbody></table>
    <p class="footer">Ripoti hii inaonesha sadaka, matumizi, na salio la mfuko kwa kipindi kilichochaguliwa.</p>
</body>
</html>
