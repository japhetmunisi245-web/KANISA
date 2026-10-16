<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ofisi ya Kanisa</title>
    <style>
        body{margin:0;background:#f5f7fb;font:14px system-ui;color:#182033}.wrap{max-width:1200px;margin:auto;padding:28px 18px}header{background:#1b1f3b;color:#fff;padding:24px;border-radius:14px;display:flex;justify-content:space-between;align-items:center}.role{color:#a5b4fc;text-transform:uppercase;font-size:12px;letter-spacing:1px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:18px;margin-top:20px}.card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 8px #00000012}.wide{grid-column:1/-1}h2{font-size:17px;margin:0 0 14px;color:#252d55}h3{font-size:15px;margin:0 0 8px}input,select,textarea{width:100%;box-sizing:border-box;margin:5px 0 10px;padding:9px;border:1px solid #d9deea;border-radius:7px;font:inherit}textarea{min-height:90px}button{background:#4f46e5;color:#fff;border:0;border-radius:7px;padding:10px 13px;font-weight:600;cursor:pointer}.success{background:#dcfce7;color:#166534;padding:10px;border-radius:8px;margin-top:15px}.list{padding:0;margin:0;list-style:none}.list li{border-bottom:1px solid #edf0f5;padding:9px 0}.muted{color:#667085;font-size:12px}.stat{font-size:26px;font-weight:750;color:#15803d}.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px}.stat-card{background:#f8faff;border:1px solid #e5eaf5;border-radius:9px;padding:14px}.stat-card strong{display:block;font-size:23px;color:#252d55}.dept-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px}.dept-item{border:1px solid #edf0f5;padding:10px;border-radius:8px}.amount-preview{background:#eff6ff;color:#1d4ed8;border-radius:8px;padding:10px;margin:0 0 10px;font-weight:600}a{color:#fff;text-decoration:none}@media(max-width:600px){.wrap{padding:18px 12px}}
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <div><div class="role">{{ $user->cheo ?? 'Church Office' }}</div><h1 style="margin:4px 0">Karibu, {{ $user->name }}</h1></div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout').submit()">Toka</a>
        <form id="logout" action="{{ route('logout') }}" method="post">@csrf</form>
    </header>

    @if(session('success'))<div class="success">{{ session('success') }}</div>@endif

    <div class="grid">
        @if($user->isSecretary())
            <section class="card wide">
                <h2>Muhtasari wa washirika</h2>
                <div class="stats">
                    <div class="stat-card"><span class="muted">Washirika wote</span><strong>{{ $members->count() }}</strong></div>
                    <div class="stat-card"><span class="muted">Wanaofanya kazi</span><strong>{{ $memberStatusCounts['active'] ?? 0 }}</strong></div>
                    <div class="stat-card"><span class="muted">Waliohama</span><strong>{{ $memberStatusCounts['moved'] ?? 0 }}</strong></div>
                    <div class="stat-card"><span class="muted">Waliofariki</span><strong>{{ $memberStatusCounts['deceased'] ?? 0 }}</strong></div>
                    <div class="stat-card"><span class="muted">Wasio hai</span><strong>{{ $memberStatusCounts['inactive'] ?? 0 }}</strong></div>
                </div>
                <h3 style="margin-top:18px">Washirika kwa idara</h3>
                <div class="dept-list">
                    @forelse($membersByDepartment as $department)<div class="dept-item"><strong>{{ $department->name_sw }}</strong><br><span class="muted">Washirika {{ $department->members_count }}</span></div>@empty
                        <span class="muted">Hakuna idara iliyosajiliwa.</span>
                    @endforelse
                </div>
                <p class="muted" style="margin:14px 0 0">Hali za mshirika zinaonekana kwenye dashibodi: active, inactive, moved (amehama), na deceased (amefariki).</p>
            </section>
            <section class="card wide"><h2>Taarifa za washirika</h2><ul class="list">@forelse($members as $member)<li style="display:flex;gap:12px;justify-content:space-between;align-items:center;flex-wrap:wrap"><span><strong>{{ $member->name }}</strong><br><span class="muted">{{ $member->member_id ?? $member->email }} · {{ $member->departments->pluck('name_sw')->join(', ') ?: 'Hajawekwa idara' }}</span></span><form method="post" action="{{ route('church-office.members.status.update', $member) }}" style="display:flex;gap:7px;align-items:center">@csrf @method('PATCH')<select name="status" style="margin:0;width:auto"><option value="active" @selected($member->status === 'active')>Yupo kanisani</option><option value="inactive" @selected($member->status === 'inactive')>Hajathibitishwa</option><option value="moved" @selected($member->status === 'moved')>Amehama</option><option value="deceased" @selected($member->status === 'deceased')>Amefariki</option></select><button>Hifadhi</button></form></li>@empty<li class="muted">Hakuna mshirika bado.</li>@endforelse</ul></section>

            <section class="card"><h2>Andika kumbukumbu za kikao</h2><form method="post" action="{{ route('church-office.minutes.store') }}">@csrf<input name="meeting_type" placeholder="Aina ya kikao (Wazee / Mkutano Mkuu)" required><input type="date" name="meeting_date" required><input name="title" placeholder="Kichwa cha kumbukumbu" required><textarea name="minutes" placeholder="Maamuzi na kumbukumbu za kikao" required></textarea><button>Hifadhi kumbukumbu</button></form></section>
            <section class="card"><h2>Panga zamu ya wiki</h2><form method="post" action="{{ route('church-office.schedules.store') }}">@csrf<input type="date" name="week_start" required><input name="service_name" placeholder="Ibada / shughuli" required><input name="person_name" placeholder="Jina la mhudumu" required><input name="duty" placeholder="Wajibu" required><textarea name="notes" placeholder="Maelezo (si lazima)"></textarea><button>Hifadhi ratiba</button></form></section>
            <section class="card wide"><h2>Chapisha tangazo la kanisa</h2><form method="post" action="{{ route('church-office.announcements.store') }}">@csrf<select name="audience"><option value="all">Wote: viongozi na washirika</option><option value="leaders">Viongozi pekee</option><option value="members">Washirika wote</option></select><input name="title" placeholder="Kichwa cha tangazo" required><textarea name="content" placeholder="Ujumbe wa tangazo" required></textarea><label class="muted">Mwisho wa kuonekana (si lazima)</label><input type="date" name="expires_at"><button>Chapisha tangazo</button></form><p class="muted">Ukichagua “Washirika wote”, tangazo litaonekana kwa kila mshirika aliye ndani ya mfumo.</p></section>

            <section class="card"><h2>Kumbukumbu za karibuni</h2><ul class="list">@forelse($minutes as $item)<li><strong>{{ $item->title }}</strong><br><span class="muted">{{ $item->meeting_type }} · {{ $item->meeting_date->format('d/m/Y') }}</span></li>@empty<li class="muted">Hakuna kumbukumbu bado.</li>@endforelse</ul></section>
            <section class="card"><h2>Ratiba zijazo</h2><ul class="list">@forelse($schedules as $item)<li><strong>{{ $item->person_name }}</strong> — {{ $item->duty }}<br><span class="muted">{{ $item->service_name }} · wiki ya {{ $item->week_start->format('d/m/Y') }}</span></li>@empty<li class="muted">Hakuna ratiba bado.</li>@endforelse</ul></section>
            <section class="card wide"><h2>Kumbukumbu za mfumo: barua na ripoti</h2><div class="grid" style="margin-top:0"><div><h3>Barua zilizotumwa</h3><ul class="list">@forelse($letters as $letter)<li><strong>{{ $letter->subject }}</strong><br><span class="muted">{{ $letter->user->name ?? 'Mfumo' }} · {{ $letter->department->name_sw ?? 'Kanisa' }} · {{ $letter->created_at->format('d/m/Y') }}</span></li>@empty<li class="muted">Hakuna barua bado.</li>@endforelse</ul></div><div><h3>Ripoti za kanisa na idara</h3><ul class="list">@forelse($reports as $report)<li><strong>{{ $report->title }}</strong><br><span class="muted">{{ $report->type }} · {{ $report->user->name ?? 'Mfumo' }} · {{ $report->report_date->format('d/m/Y') }}</span></li>@empty<li class="muted">Hakuna ripoti bado.</li>@endforelse</ul></div></div></section>
        @endif

        @if($user->isDeacon())
            <section class="card"><h2>Rekodi mahudhurio ya ibada</h2><form method="post" action="{{ route('church-office.attendance.store') }}">@csrf<input type="date" name="service_date" required><input name="service_name" placeholder="Jina la ibada" required><input type="number" min="0" name="male_count" placeholder="Wanaume" required><input type="number" min="0" name="female_count" placeholder="Wanawake" required><input type="number" min="0" name="children_count" placeholder="Watoto (si lazima)"><textarea name="notes" placeholder="Maelezo"></textarea><button>Hifadhi mahudhurio</button></form></section>
            <section class="card"><h2>Mahudhurio ya karibuni</h2><ul class="list">@forelse($attendances as $item)<li><strong>{{ $item->service_name }}</strong> — {{ $item->service_date->format('d/m/Y') }}<br><span class="muted">Wanaume {{ $item->male_count }} · Wanawake {{ $item->female_count }} · Watoto {{ $item->children_count }}</span></li>@empty<li class="muted">Hakuna rekodi bado.</li>@endforelse</ul></section>
        @endif

        @if($user->isAccountant())
            <section class="card"><h2>Rekodi sadaka</h2><form method="post" action="{{ route('church-office.offerings.store') }}">@csrf<input type="date" name="offering_date" required><label class="muted" for="offering_type">Ania ya sadaka</label><input id="offering_type" name="offering_type" placeholder="Mfano: Sadaka ya jumapili, zaka, ujenzi" required><label class="muted" for="offering_amount">Kiasi cha sadaka (TZS)</label><input id="offering_amount" type="number" step="0.01" min="0" name="amount" placeholder="0.00" required><div class="amount-preview">Jumla mpya ya mwezi: TZS <span id="running-total">{{ number_format($monthOfferingTotal, 2) }}</span></div><select name="payment_method"><option>Cash</option><option>M-Pesa</option><option>Benki</option><option>Airtel Money</option></select><textarea name="notes" placeholder="Maelezo"></textarea><button>Hifadhi sadaka</button></form></section>
            <section class="card"><h2>Ripoti ya mwezi huu</h2><div class="stat">TZS {{ number_format($monthOfferingTotal, 2) }}</div><p class="muted">Jumla husasishwa kiotomatiki kila sadaka inapohifadhiwa.</p><ul class="list">@forelse($offerings as $item)<li><strong>{{ $item->offering_type }}</strong> — TZS {{ number_format($item->amount,2) }}<br><span class="muted">{{ $item->offering_date->format('d/m/Y') }} · {{ $item->payment_method }}</span></li>@empty<li class="muted">Hakuna sadaka mwezi huu.</li>@endforelse</ul></section>
        @endif

        <section class="card wide"><h2>Matangazo ya kanisa</h2><ul class="list">@forelse($announcements as $item)<li><strong>{{ $item->title }}</strong><br>{{ $item->content }}<br><span class="muted">Walengwa: {{ $item->audience }} · Imetumwa na {{ $item->user->name ?? 'Mfumo' }} · {{ $item->created_at->format('d/m/Y') }}</span></li>@empty<li class="muted">Hakuna matangazo bado.</li>@endforelse</ul></section>
    </div>
</div>
@if($user->isAccountant())
<script>
    const offeringAmount = document.getElementById('offering_amount');
    const runningTotal = document.getElementById('running-total');
    const monthTotal = {{ (float) $monthOfferingTotal }};
    offeringAmount?.addEventListener('input', () => {
        const amount = Number.parseFloat(offeringAmount.value) || 0;
        runningTotal.textContent = (monthTotal + amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    });
</script>
@endif
</body>
</html>
