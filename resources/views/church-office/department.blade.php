<!doctype html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $department->name_sw }} | Ofisi ya Kanisa</title>
    <style>
        :root{--ink:#18213d;--green:#2bb673;--pale:#f5f7fb;--line:#e3e9f2;--muted:#75829a}*{box-sizing:border-box}body{margin:0;background:var(--pale);font:14px system-ui;color:#26314a}.page{max-width:1300px;margin:auto;padding:28px}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}.back{color:#13794d;font-weight:750;text-decoration:none}.eyebrow{font-size:11px;font-weight:800;letter-spacing:.1em;color:var(--green)}h1,h2,h3{color:var(--ink)}h1{margin:4px 0;font-size:28px}h2{font-size:18px;margin:0 0 12px}.muted{color:var(--muted)}.card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:20px;box-shadow:0 4px 16px #1d2a4708}.hero{display:grid;grid-template-columns:1.35fr .65fr;gap:18px;margin-bottom:18px}.summary{font-size:15px;line-height:1.7;margin:0}.metric{background:#e8fbf1;border-radius:10px;padding:17px;color:#13794d}.metric strong{font-size:29px;display:block;color:var(--ink)}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}.record{padding:13px 0;border-bottom:1px solid var(--line)}.record:last-child{border:0}.record small{color:var(--muted)}.tag{display:inline-block;background:#eef6ff;color:#315e97;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:700}.members{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.member{border:1px solid var(--line);border-radius:10px;padding:15px}.avatar{width:46px;height:46px;border-radius:50%;overflow:hidden;display:grid;place-items:center;background:#e7f8ef;color:#13794d;font-weight:800;float:left;margin:0 10px 8px 0}.avatar img{width:100%;height:100%;object-fit:cover}.member-head{min-height:53px}.member h3{margin:0 0 3px;font-size:15px}.member summary{cursor:pointer;clear:both;color:#13794d;font-weight:700;padding-top:10px}.details{margin-top:10px;border-top:1px solid var(--line);padding-top:9px;display:grid;gap:6px}.details span{color:var(--muted);font-size:12px}.details b{color:#26314a;font-size:13px}.empty{color:var(--muted);padding:8px 0}@media(max-width:800px){.hero,.grid{grid-template-columns:1fr}.members{grid-template-columns:1fr 1fr}.page{padding:18px}}@media(max-width:500px){.members{grid-template-columns:1fr}.top{align-items:flex-start;gap:14px;flex-direction:column}}
    </style>
</head>
<body>
<main class="page">
    <header class="top">
        <div><div class="eyebrow">OFISI YA KANISA · IDARA</div><h1>{{ $department->name_sw }}</h1></div>
        <a class="back" href="{{ route('church-office.index') }}">← Rudi kwenye idara</a>
    </header>

    <section class="hero">
        <article class="card"><h2>Documentary ya Idara</h2><p class="summary">{{ $department->description_sw ?: 'Maelezo ya idara hii bado hayajawekwa. Katibu anaweza kuyasasisha kwenye usimamizi wa idara.' }}</p></article>
        <aside class="metric"><span>WASHIRIKA WA IDARA</span><strong>{{ $members->count() }}</strong><span>Wanaonekana hapa ni wale waliosajiliwa kwenye {{ $department->name_sw }} pekee.</span></aside>
    </section>

    <section class="grid">
        <article class="card"><h2>Ratiba ya Idara</h2>@forelse($schedules as $schedule)<div class="record"><strong>{{ $schedule->service_name }}</strong><br><small>Wiki ya {{ $schedule->week_start->format('d/m/Y') }} · {{ $schedule->person_name }} — {{ $schedule->duty }}</small>@if($schedule->notes)<br><small>{{ $schedule->notes }}</small>@endif</div>@empty<p class="empty">Bado hakuna ratiba iliyowekwa kwa idara hii.</p>@endforelse</article>
        <article class="card"><h2>Kumbukumbu na Taarifa</h2>@forelse($minutes as $minute)<div class="record"><strong>{{ $minute->title }}</strong><br><small>{{ $minute->meeting_type }} · {{ $minute->meeting_date->format('d/m/Y') }}</small><br>{{ $minute->minutes }}</div>@empty<p class="empty">Hakuna kumbukumbu ya kikao cha idara hii.</p>@endforelse @forelse($reports as $report)<div class="record"><span class="tag">Ripoti {{ $report->type }}</span><br><strong>{{ $report->title }}</strong><br><small>{{ $report->report_date->format('d/m/Y') }} · {{ $report->user->name ?? 'Mfumo' }}</small><br>{{ $report->content }}</div>@empty @endforelse</article>
    </section>

    <section class="card" style="margin-bottom:18px"><h2>Mambo yanayohusu idara</h2>@forelse($campaigns as $campaign)<div class="record"><strong>{{ $campaign->title }}</strong> <span class="tag">{{ $campaign->status }}</span><br><small>Lengo la mshiriki: TZS {{ number_format($campaign->amount_per_member, 0) }} · Mwisho: {{ $campaign->due_date->format('d/m/Y') }}</small>@if($campaign->notes)<br>{{ $campaign->notes }}@endif</div>@empty<p class="empty">Hakuna mchango au shughuli nyingine ya idara iliyorekodiwa kwa sasa.</p>@endforelse</section>

    <section class="card"><h2>Washirika wa {{ $department->name_sw }}</h2><p class="muted">Bofya “Tazama taarifa” kwa maelezo ya mshirika husika.</p><div class="members">@forelse($members as $member)<article class="member"><div class="member-head"><div class="avatar">@if($member->avatar)<img src="{{ asset('storage/'.$member->avatar) }}" alt="{{ $member->name }}">@else{{ strtoupper(substr($member->name, 0, 1)) }}@endif</div><h3>{{ $member->name }}</h3><span class="muted">{{ $member->member_id ?: 'ID haijawekwa' }}</span></div><details><summary>Tazama taarifa</summary><div class="details"><span>Simu</span><b>{{ $member->phone ?: 'Haijawekwa' }}</b><span>Barua pepe</span><b>{{ $member->email ?: 'Haijawekwa' }}</b><span>Jinsia</span><b>{{ $member->gender === 'male' ? 'Mwanaume' : ($member->gender === 'female' ? 'Mwanamke' : 'Haijawekwa') }}</b><span>Hali ya ndoa</span><b>{{ $member->marital_status === 'married' ? 'Nimeoa / Nimeolewa' : ($member->marital_status === 'single' ? 'Sijaoa / Sijaolewa' : ($member->marital_status === 'widowed' ? 'Mjane' : 'Haijawekwa')) }}</b><span>Huduma</span><b>{{ $member->service ?: 'Haijawekwa' }}</b><span>Elimu</span><b>{{ $member->education ?: 'Haijawekwa' }}</b><span>Mtaa / Kata</span><b>{{ collect([$member->street_name, $member->ward])->filter()->join(', ') ?: 'Haijawekwa' }}</b></div></details></article>@empty<p class="empty">Hakuna mshirika aliyesajiliwa kwenye idara hii.</p>@endforelse</div></section>
</main>
</body>
</html>
