<?php

namespace App\Http\Controllers;

use App\Models\ChurchAnnouncement;
use App\Models\DutySchedule;
use App\Models\MeetingMinute;
use App\Models\Offering;
use App\Models\Expense;
use App\Models\Letter;
use App\Models\Report;
use App\Models\ServiceAttendance;
use App\Models\ServiceVisitor;
use App\Models\User;
use App\Models\Department;
use App\Models\ContributionCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ChurchOfficeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isSecretary() || $user->isDeacon() || $user->isAccountant(), 403);
        $month = now()->startOfMonth();

        if ($user->isAccountant()) {
            $selectedMonth = request('month');
            $month = $selectedMonth && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)
                ? \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()
                : now()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            $monthOfferings = Offering::whereBetween('offering_date', [$month, $monthEnd]);
            $monthExpenses = Expense::whereBetween('expense_date', [$month, $monthEnd]);
            $weeklyFinance = $this->weeklyFinance($month);

            return view('church-office.accountant', [
                'user' => $user,
                'offerings' => Offering::latest('offering_date')->take(50)->get(),
                'expenses' => Expense::latest('expense_date')->take(50)->get(),
                'monthOfferingTotal' => (clone $monthOfferings)->where('fund_scope', 'internal')->sum('amount'),
                'monthExternalOfferingTotal' => (clone $monthOfferings)->where('fund_scope', 'external')->sum('amount'),
                'monthExpenseTotal' => $monthExpenses->sum('amount'),
                'financeMonth' => $month,
                'weeklyFinance' => $weeklyFinance,
            ]);
        }

        if ($user->isDeacon()) {
            return view('church-office.deacon', [
                'user' => $user,
                'attendances' => $this->filterByMonthWeek(ServiceAttendance::query()->latest('service_date'), $request, 'attendance', 'service_date')->take(50)->get(),
                'visitors' => $this->filterByMonthWeek(ServiceVisitor::query()->latest('visit_date'), $request, 'attendance', 'visit_date')->take(50)->get(),
                'announcements' => ChurchAnnouncement::with('user')
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
                    ->latest()->take(10)->get(),
                'recordYears' => $this->recordYears(),
            ]);
        }

        $kuski = Department::whereRaw('LOWER(name_sw) like ?', ['%kuski%'])->first();
        $praiseLeaders = $kuski
            ? $kuski->users()->where('users.status', 'active')->orderBy('users.name')->get()
            : collect();
        $deacons = User::where('status', 'active')->where(function ($query) {
            $query->whereHas('roles', fn ($roles) => $roles->where('name', 'deacon'))
                ->orWhereRaw("LOWER(COALESCE(cheo, '')) like ?", ['%shemasi%']);
        })->orderBy('name')->get();
        $elders = User::where('status', 'active')->where(function ($query) {
            $query->whereHas('roles', fn ($roles) => $roles->where('name', 'mzee'))
                ->orWhereRaw("LOWER(COALESCE(cheo, '')) like ?", ['%mzee%']);
        })->orderBy('name')->get();
        $cleaningDepartments = Department::where(function ($query) {
            $query->whereRaw('LOWER(name_sw) like ?', ['%vijana%'])
                ->orWhereRaw('LOWER(name_sw) like ?', ['%wamama%']);
        })->orderBy('name_sw')->get();

        $letterQuery = Letter::with(['user', 'department'])->latest();
        $attendanceQuery = ServiceAttendance::with('user')->whereHas('user.roles', fn ($query) => $query->where('name', 'deacon'))->latest('service_date');
        $visitorQuery = ServiceVisitor::with(['user', 'attendance'])->latest('visit_date');

        return view('church-office.index', [
            'user' => $user,
            'members' => User::with('departments')
                ->whereNotIn('role', ['admin', 'mchungaji'])
                ->orderBy('name')->get(),
            'membersByDepartment' => \App\Models\Department::with([
                'users' => function ($query) {
                    $query->whereNotIn('users.role', ['admin', 'mchungaji'])->orderBy('name');
                },
            ])->withCount(['users as members_count' => function ($query) {
                $query->whereNotIn('users.role', ['admin', 'mchungaji']);
            }])->orderBy('name_sw')->get(),
            'departments' => Department::orderBy('name_sw')->get(),
            'memberStatusCounts' => User::whereNotIn('role', ['admin', 'mchungaji'])
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'minutes' => MeetingMinute::latest('meeting_date')->take(8)->get(),
            'schedules' => DutySchedule::whereDate('week_start', '>=', now()->startOfWeek())->orderBy('week_start')->get(),
            'attendances' => $this->filterByMonthWeek($attendanceQuery, $request, 'attendance', 'service_date')->take(20)->get(),
            'visitors' => $this->filterByMonthWeek($visitorQuery, $request, 'attendance', 'visit_date')->take(30)->get(),
            'offerings' => Offering::whereBetween('offering_date', [$month, now()->endOfMonth()])->latest('offering_date')->get(),
            'monthOfferingTotal' => Offering::whereBetween('offering_date', [$month, now()->endOfMonth()])->sum('amount'),
            'announcements' => ChurchAnnouncement::with('user')->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))->latest()->take(10)->get(),
            'letters' => $this->filterByMonthWeek($letterQuery, $request, 'letter', 'created_at')->take(30)->get(),
            'recordYears' => $this->recordYears(),
            'reports' => Report::with(['user', 'department'])->latest('report_date')->take(10)->get(),
            'praiseLeaders' => $praiseLeaders,
            'deacons' => $deacons,
            'elders' => $elders,
            'cleaningDepartments' => $cleaningDepartments,
        ]);
    }

    public function showDepartment(Department $department)
    {
        $this->secretary();

        return view('church-office.department', [
            'user' => Auth::user(),
            'department' => $department,
            'members' => $department->users()
                ->whereNotIn('users.role', ['admin', 'mchungaji'])
                ->orderBy('users.name')
                ->get(),
            'minutes' => MeetingMinute::where('department_id', $department->id)->latest('meeting_date')->get(),
            'schedules' => DutySchedule::where('department_id', $department->id)->orderByDesc('week_start')->get(),
            'reports' => Report::with('user')->where('department_id', $department->id)->latest('report_date')->get(),
            'campaigns' => ContributionCampaign::where('department_id', $department->id)->latest()->get(),
        ]);
    }

    public function storeMinute(Request $request) { $this->secretary(); $data = $request->validate(['department_id'=>'nullable|exists:departments,id','meeting_type'=>'required|string|max:80','meeting_date'=>'required|date','title'=>'required|string|max:255','minutes'=>'required|string']); $data['user_id']=Auth::id(); MeetingMinute::create($data); return back()->with('success','Kumbukumbu za kikao zimehifadhiwa.'); }
    public function storeSchedule(Request $request)
    {
        $this->secretary();

        $data = $request->validate([
            'week_start' => 'required|date',
            'praise_leader_ids' => 'required|array|min:1',
            'praise_leader_ids.*' => 'required|distinct|exists:users,id',
            'deacon_ids' => 'required|array|min:1',
            'deacon_ids.*' => 'required|distinct|exists:users,id',
            'elder_names' => 'required|array|min:1',
            'elder_names.*' => 'required|string|max:255',
            'elder_phones' => 'nullable|array',
            'elder_phones.*' => 'nullable|string|max:30',
            'cleaning_department_ids' => 'required|array|min:1',
            'cleaning_department_ids.*' => 'required|distinct|exists:departments,id',
        ]);

        $weekStart = \Carbon\Carbon::parse($data['week_start'])->startOfWeek(\Carbon\Carbon::SUNDAY);
        $kuski = Department::whereRaw('LOWER(name_sw) like ?', ['%kuski%'])->first();
        $people = [];
        foreach ($data['praise_leader_ids'] as $id) {
            $person = User::findOrFail($id);
            abort_unless($kuski && $person->departments()->whereKey($kuski->id)->exists(), 422, 'Viongozi wa Praise lazima wawe wanachama wa KUSKI.');
            $people[] = ['user' => $person, 'duty' => 'VIONGOZI WA PRAISE'];
        }
        foreach ($data['deacon_ids'] as $id) {
            $person = User::findOrFail($id);
            abort_unless($this->isDeacon($person), 422, 'Chagua mshirika mwenye cheo cha Shemasi.');
            $people[] = ['user' => $person, 'duty' => 'MASHEMASI'];
        }
        foreach ($data['elder_names'] as $index => $name) {
            $people[] = [
                'name' => $name,
                'phone' => $data['elder_phones'][$index] ?? null,
                'duty' => 'MZEE WA ZAMU',
            ];
        }
        foreach ($data['cleaning_department_ids'] as $id) {
            $department = Department::findOrFail($id);
            abort_unless(
                str_contains(mb_strtolower($department->name_sw), 'vijana') || str_contains(mb_strtolower($department->name_sw), 'wamama'),
                422,
                'Zamu ya usafi inaruhusu Idara ya Vijana au Idara ya Wamama pekee.'
            );
            $people[] = ['department' => $department, 'duty' => 'ZAMU YA USAFI'];
        }

        // One published duty list applies to the entire church for that Sunday-to-Sunday week.
        DutySchedule::whereDate('week_start', $weekStart->toDateString())->delete();

        foreach ($people as $person) {
            DutySchedule::create([
                'user_id' => Auth::id(),
                'week_start' => $weekStart,
                'service_name' => 'Zamu za wiki',
                'person_name' => $person['user']->name ?? $person['department']->name_sw ?? $person['name'],
                'duty' => $person['duty'],
                'notes' => $person['user']->phone ?? $person['phone'] ?? null,
            ]);
        }

        return back()->with('success', 'Zamu za wiki zimechapishwa kwa washirika wote.');
    }

    private function isDeacon(User $user): bool
    {
        return $user->hasRole('deacon') || str_contains(mb_strtolower((string) $user->cheo), 'shemasi');
    }

    private function isElder(User $user): bool
    {
        return $user->hasRole('mzee') || str_contains(mb_strtolower((string) $user->cheo), 'mzee');
    }
    public function storeAnnouncement(Request $request)
    {
        $this->secretary();
        $data = $request->validate([
            'audience' => 'required|in:all,leaders,members',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'expires_at' => 'nullable|date',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm,mov|max:51200',
        ]);
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $data['media_path'] = $file->store('announcement-media', 'public');
            $data['media_type'] = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';
            $data['media_original_name'] = $file->getClientOriginalName();
        }
        unset($data['media']);
        $data['user_id'] = Auth::id();
        ChurchAnnouncement::create($data);
        return back()->with('success', 'Tangazo limechapishwa kwa walengwa waliochaguliwa.');
    }

    public function downloadAnnouncementMedia(ChurchAnnouncement $announcement)
    {
        abort_unless(Auth::check() && $announcement->media_path, 404);
        return Storage::disk('public')->download($announcement->media_path, $announcement->media_original_name ?: basename($announcement->media_path));
    }
    public function storeAttendance(Request $request)
    {
        $this->attendanceOfficer();
        $data = $request->validate([
            'service_date' => 'required|date', 'service_name' => 'required|string|max:120',
            'male_count' => 'required|integer|min:0', 'female_count' => 'required|integer|min:0',
            'children_count' => 'nullable|integer|min:0', 'notes' => 'nullable|string',
            'visitors' => 'nullable|array', 'visitors.*.name' => 'nullable|string|max:255',
            'visitors.*.phone' => 'nullable|string|max:30', 'visitors.*.has_moved' => 'nullable|boolean',
            'visitors.*.origin_location' => 'nullable|string|max:255',
            'visitors.*.current_location' => 'nullable|string|max:255',
            'visitors.*.previous_church_name' => 'nullable|string|max:255',
            'visitors.*.previous_church_location' => 'nullable|string|max:255',
            'visitors.*.previous_pastor_name' => 'nullable|string|max:255',
            'visitors.*.previous_pastor_phone' => 'nullable|string|max:30',
            'visitors.*.service' => 'nullable|string|max:255',
            'visitors.*.marital_status' => 'nullable|in:single,married,widowed',
            'visitors.*.introduction_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        $attendance = ServiceAttendance::create([
            'user_id' => Auth::id(), 'service_date' => $data['service_date'], 'service_name' => $data['service_name'],
            'male_count' => $data['male_count'], 'female_count' => $data['female_count'],
            'children_count' => $data['children_count'] ?? 0, 'notes' => $data['notes'] ?? null,
        ]);

        $movedVisitors = collect($data['visitors'] ?? [])->filter(fn ($visitor) => !empty($visitor['name']) && ($visitor['has_moved'] ?? false));
        foreach ($movedVisitors as $index => $visitor) {
            $phone = trim((string) ($visitor['phone'] ?? ''));
            $member = $phone !== '' ? User::where('phone', $phone)->first() : null;
            $transferDetails = [
                'origin_location' => $visitor['origin_location'] ?? null,
                'current_location' => $visitor['current_location'] ?? null,
                'previous_church_name' => $visitor['previous_church_name'] ?? null,
                'previous_church_location' => $visitor['previous_church_location'] ?? null,
                'previous_pastor_name' => $visitor['previous_pastor_name'] ?? null,
                'previous_pastor_phone' => $visitor['previous_pastor_phone'] ?? null,
                'service' => $visitor['service'] ?? null,
                'marital_status' => $visitor['marital_status'] ?? null,
            ];
            $letter = $request->file("visitors.{$index}.introduction_letter");
            if ($letter) {
                $transferDetails['introduction_letter_path'] = $letter->store('introduction-letters', 'public');
            }

            if (! $member) {
                $member = User::create([
                    'name' => trim($visitor['name']),
                    'phone' => $phone ?: null,
                    'email' => 'mhamia'.now()->format('YmdHis').random_int(1000, 9999).'@kanisa.local',
                    'member_id' => 'MHA-'.now()->format('ymdHis').random_int(10, 99),
                    'role' => 'mshirika',
                    'status' => 'active',
                    'joined_as_visitor' => true,
                    'joined_church_at' => $data['service_date'],
                    'password' => Hash::make('password'),
                ] + $transferDetails);
            } else {
                // Do not replace existing information with blank inputs, but save any
                // transfer details supplied during this visit.
                $member->update(array_filter($transferDetails + [
                    'joined_as_visitor' => true,
                    'joined_church_at' => $member->joined_church_at ?: $data['service_date'],
                ], fn ($value) => $value !== null && $value !== ''));
            }

            ServiceVisitor::create([
                'user_id' => Auth::id(), 'member_id' => $member->id, 'service_attendance_id' => $attendance->id,
                'visit_date' => $data['service_date'], 'name' => $visitor['name'],
                'phone' => $visitor['phone'] ?? null, 'has_moved' => true,
            ]);
        }

        return back()->with('success', 'Mahudhurio yamehifadhiwa'.($movedVisitors->isNotEmpty() ? ' pamoja na wageni '.$movedVisitors->count().' waliohamia.' : '.'));
    }

    public function downloadVisitorReport(Request $request)
    {
        $this->attendanceOfficer();
        $data = $request->validate(['period' => 'required|in:week,month', 'date' => 'nullable|date']);
        $date = isset($data['date']) ? \Carbon\Carbon::parse($data['date']) : now();
        [$start, $end, $title] = $data['period'] === 'week'
            ? [$date->copy()->startOfWeek(), $date->copy()->endOfWeek(), 'Ripoti ya Wageni Waliohamia ya Wiki']
            : [$date->copy()->startOfMonth(), $date->copy()->endOfMonth(), 'Ripoti ya Wageni Waliohamia ya Mwezi'];
        $visitors = ServiceVisitor::whereBetween('visit_date', [$start->toDateString(), $end->toDateString()])->orderBy('visit_date')->get();
        $pdf = Pdf::loadView('church-office.visitors-report-pdf', compact('title', 'start', 'end', 'visitors'))->setPaper('a4');
        $filename = 'wageni-waliohamia-'.$data['period'].'-'.$start->format('Y-m-d').'.pdf';
        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }
    public function storeOffering(Request $request)
    {
        $this->offeringOfficer();

        // Keep legacy single-entry submissions compatible with the multi-row form.
        if (! $request->has('items') && $request->filled('offering_type')) {
            $request->merge(['items' => [[
                'offering_type' => $request->input('offering_type'),
                'fund_scope' => 'internal',
                'amount' => $request->input('amount'),
            ]]]);
        }

        $data = $request->validate([
            'offering_date' => 'required|date',
            'payment_method' => 'required|string|max:80',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.offering_type' => 'required|string|max:120',
            'items.*.fund_scope' => 'required|in:internal,external',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        foreach ($data['items'] as $item) {
            Offering::create([
                'user_id' => Auth::id(),
                'offering_date' => $data['offering_date'],
                'offering_type' => $item['offering_type'],
                'fund_scope' => $item['fund_scope'],
                'amount' => $item['amount'],
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
            ]);
        }

        return back()->with('success', count($data['items']).' aina za sadaka zimehifadhiwa na jumla imesasishwa.');
    }

    public function downloadOfferingReport(Request $request)
    {
        $this->offeringOfficer();
        $data = $request->validate([
            'period' => 'required|in:week,month',
            'date' => 'nullable|date',
        ]);

        $date = isset($data['date']) ? \Carbon\Carbon::parse($data['date']) : now();
        [$start, $end, $title] = $data['period'] === 'week'
            ? [$date->copy()->startOfWeek(), $date->copy()->endOfWeek(), 'Ripoti ya Sadaka ya Wiki']
            : [$date->copy()->startOfMonth(), $date->copy()->endOfMonth(), 'Ripoti ya Sadaka ya Mwezi'];

        $offerings = Offering::whereBetween('offering_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('offering_date')->orderBy('offering_type')->get();
        $expenses = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('expense_date')->orderBy('expense_type')->get();
        $internalOfferingTotal = $offerings->where('fund_scope', 'internal')->sum('amount');
        $externalOfferingTotal = $offerings->where('fund_scope', 'external')->sum('amount');
        $expenseTotal = $expenses->sum('amount');

        $pdf = Pdf::loadView('church-office.offerings-report-pdf', [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'offerings' => $offerings,
            'expenses' => $expenses,
            'internalOfferingTotal' => $internalOfferingTotal,
            'externalOfferingTotal' => $externalOfferingTotal,
            'expenseTotal' => $expenseTotal,
            'balance' => $internalOfferingTotal - $expenseTotal,
        ])->setPaper('a4');

        $filename = 'taarifa-ya-mfuko-'.$data['period'].'-'.$start->format('Y-m-d').'.pdf';
        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }

    public function storeExpense(Request $request)
    {
        $this->offeringOfficer();

        // Supports the former single-entry form as well as the new multi-row form.
        if (! $request->has('expense_items') && $request->filled('expense_type')) {
            $request->merge(['expense_items' => [[
                'expense_type' => $request->input('expense_type'),
                'amount' => $request->input('amount'),
            ]]]);
        }

        $data = $request->validate([
            'expense_date' => 'required|date',
            'expense_items' => 'required|array|min:1',
            'expense_items.*.expense_type' => 'required|string|max:120',
            'expense_items.*.amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        foreach ($data['expense_items'] as $item) {
            Expense::create([
                'user_id' => Auth::id(),
                'expense_date' => $data['expense_date'],
                'expense_type' => $item['expense_type'],
                'amount' => $item['amount'],
                'notes' => $data['notes'] ?? null,
            ]);
        }

        return back()->with('success', count($data['expense_items']).' matumizi yamehifadhiwa na salio la mfuko limesasishwa.');
    }

    private function weeklyFinance(\Carbon\Carbon $month): array
    {
        $weeks = [];
        for ($index = 0; $index < 5; $index++) {
            $start = $month->copy()->addDays($index * 7);
            if ($start->month !== $month->month) {
                break;
            }
            $end = $start->copy()->addDays(6)->min($month->copy()->endOfMonth());
            $offerings = Offering::whereBetween('offering_date', [$start->toDateString(), $end->toDateString()])
                ->where('fund_scope', 'internal')->sum('amount');
            $expenses = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])->sum('amount');
            $weeks[] = [
                'label' => 'Wiki ya '.($index + 1),
                'range' => $start->format('d/m').' - '.$end->format('d/m/Y'),
                'offerings' => $offerings,
                'expenses' => $expenses,
                'balance' => $offerings - $expenses,
            ];
        }
        return $weeks;
    }

    private function filterByMonthWeek($query, Request $request, string $prefix, string $dateColumn)
    {
        $year = $request->integer($prefix.'_year');
        $month = $request->integer($prefix.'_month');
        $week = $request->integer($prefix.'_week');

        if ($year >= 2020 && $year <= now()->year) {
            $query->whereYear($dateColumn, $year);
        }
        if ($month >= 1 && $month <= 12) {
            $query->whereMonth($dateColumn, $month);
        }
        if ($week >= 1 && $week <= 5 && $year && $month) {
            $start = \Carbon\Carbon::create($year, $month, 1)->addDays(($week - 1) * 7)->startOfDay();
            $end = $start->copy()->addDays(6)->endOfDay()->min(\Carbon\Carbon::create($year, $month, 1)->endOfMonth());
            $query->whereBetween($dateColumn, [$start, $end]);
        }

        return $query;
    }

    /** Includes every saved year and automatically adds the new calendar year. */
    private function recordYears(): array
    {
        return collect([now()->year])
            ->merge(Letter::query()->pluck('created_at')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->merge(ServiceAttendance::query()->pluck('service_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->merge(ServiceVisitor::query()->pluck('visit_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
    public function updateMemberStatus(Request $request, User $member) { $this->secretary(); abort_if($member->isAdmin() || $member->isMchungaji(), 403); $data = $request->validate(['status' => 'required|in:active,inactive,moved,deceased']); $member->update($data); return back()->with('success', "Taarifa ya {$member->name} imesasishwa."); }
    public function storeMember(Request $request) { $this->secretary(); $data = $request->validate(['name'=>'required|string|max:255','email'=>'nullable|email|unique:users,email','phone'=>'nullable|string|max:30','member_id'=>'nullable|string|max:100|unique:users,member_id','gender'=>'nullable|in:male,female','status'=>'required|in:active,inactive,moved,deceased','department_id'=>'nullable|exists:departments,id','avatar'=>'nullable|image|max:2048']); $data['email'] = $data['email'] ?? ('mshirika'.now()->format('YmdHis').rand(10,99).'@kanisa.local'); $departmentId = $data['department_id'] ?? null; unset($data['department_id']); if ($request->hasFile('avatar')) $data['avatar']=$request->file('avatar')->store('avatars','public'); $data['role']='mshirika'; $data['password']=Hash::make('password'); $member=User::create($data); if ($departmentId) $member->departments()->attach($departmentId, ['role'=>'mshirika']); return back()->with('success', "Mshirika {$member->name} amesajiliwa."); }
    public function updateMember(Request $request, User $member) { $this->secretary(); abort_if($member->isAdmin() || $member->isMchungaji(), 403); $data=$request->validate(['name'=>'required|string|max:255','email'=>'nullable|email|max:255|unique:users,email,'.$member->id,'phone'=>'nullable|string|max:30','member_id'=>'nullable|string|max:100|unique:users,member_id,'.$member->id,'gender'=>'nullable|in:male,female','dob'=>'nullable|date','address'=>'nullable|string|max:500','street_name'=>'nullable|string|max:255','ward'=>'nullable|string|max:255','marital_status'=>'nullable|in:single,married,widowed','spouse_names'=>'nullable|string|max:255','children_count'=>'nullable|integer|min:0|max:50','saved_date'=>'nullable|date','baptism_date'=>'nullable|date','education'=>'nullable|string|max:255','service'=>'nullable|string|max:255','origin_location'=>'nullable|string|max:255','current_location'=>'nullable|string|max:255','previous_church_name'=>'nullable|string|max:255','previous_church_location'=>'nullable|string|max:255','previous_pastor_name'=>'nullable|string|max:255','previous_pastor_phone'=>'nullable|string|max:30','introduction_letter'=>'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120','avatar'=>'nullable|image|max:2048']); if (($data['marital_status'] ?? null) !== 'married') $data['spouse_names'] = null; unset($data['introduction_letter']); if ($request->hasFile('avatar')) $data['avatar']=$request->file('avatar')->store('avatars','public'); if ($request->hasFile('introduction_letter')) $data['introduction_letter_path']=$request->file('introduction_letter')->store('introduction-letters','public'); $member->update($data); return back()->with('success', "Taarifa za {$member->name} zimehaririwa."); }
    public function assignDepartment(Request $request, User $member) { $this->secretary(); $data=$request->validate(['department_id'=>'required|exists:departments,id']); $member->departments()->syncWithoutDetaching([$data['department_id']=>['role'=>'mshirika']]); return back()->with('success', 'Idara imeunganishwa na mshirika.'); }
    public function removeDepartment(User $member, Department $department) { $this->secretary(); $member->departments()->detach($department->id); return back()->with('success', 'Mshirika ameondolewa kwenye idara hiyo.'); }
    private function secretary(): void { abort_unless(Auth::user()->isSecretary(), 403); }
    private function attendanceOfficer(): void { abort_unless(Auth::user()->isSecretary() || Auth::user()->isDeacon(), 403); }
    private function offeringOfficer(): void { abort_unless(Auth::user()->isAccountant() || Auth::user()->isMchungaji(), 403); }
}
