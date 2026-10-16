<?php

namespace App\Http\Controllers;

use App\Models\ChurchAnnouncement;
use App\Models\DutySchedule;
use App\Models\MeetingMinute;
use App\Models\Offering;
use App\Models\Letter;
use App\Models\Report;
use App\Models\ServiceAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChurchOfficeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->isSecretary() || $user->isDeacon() || $user->isAccountant(), 403);
        $month = now()->startOfMonth();
        return view('church-office.index', [
            'user' => $user,
            'members' => User::with('departments')
                ->whereNotIn('role', ['admin', 'mchungaji'])
                ->orderBy('name')->get(),
            'membersByDepartment' => \App\Models\Department::withCount(['users as members_count' => function ($query) {
                $query->whereNotIn('users.role', ['admin', 'mchungaji']);
            }])->orderBy('name_sw')->get(),
            'memberStatusCounts' => User::whereNotIn('role', ['admin', 'mchungaji'])
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'minutes' => MeetingMinute::latest('meeting_date')->take(8)->get(),
            'schedules' => DutySchedule::whereDate('week_start', '>=', now()->startOfWeek())->orderBy('week_start')->get(),
            'attendances' => ServiceAttendance::latest('service_date')->take(10)->get(),
            'offerings' => Offering::whereBetween('offering_date', [$month, now()->endOfMonth()])->latest('offering_date')->get(),
            'monthOfferingTotal' => Offering::whereBetween('offering_date', [$month, now()->endOfMonth()])->sum('amount'),
            'announcements' => ChurchAnnouncement::with('user')->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))->latest()->take(10)->get(),
            'letters' => Letter::with(['user', 'department'])->latest()->take(10)->get(),
            'reports' => Report::with(['user', 'department'])->latest('report_date')->take(10)->get(),
        ]);
    }

    public function storeMinute(Request $request) { $this->secretary(); $data = $request->validate(['meeting_type'=>'required|string|max:80','meeting_date'=>'required|date','title'=>'required|string|max:255','minutes'=>'required|string']); $data['user_id']=Auth::id(); MeetingMinute::create($data); return back()->with('success','Kumbukumbu za kikao zimehifadhiwa.'); }
    public function storeSchedule(Request $request) { $this->secretary(); $data = $request->validate(['week_start'=>'required|date','service_name'=>'required|string|max:120','person_name'=>'required|string|max:120','duty'=>'required|string|max:120','notes'=>'nullable|string']); $data['user_id']=Auth::id(); DutySchedule::create($data); return back()->with('success','Ratiba ya zamu imehifadhiwa.'); }
    public function storeAnnouncement(Request $request) { $this->secretary(); $data = $request->validate(['audience'=>'required|in:all,leaders,members','title'=>'required|string|max:255','content'=>'required|string','expires_at'=>'nullable|date']); $data['user_id']=Auth::id(); ChurchAnnouncement::create($data); return back()->with('success','Tangazo limechapishwa kwa walengwa waliochaguliwa.'); }
    public function storeAttendance(Request $request) { $this->deacon(); $data = $request->validate(['service_date'=>'required|date','service_name'=>'required|string|max:120','male_count'=>'required|integer|min:0','female_count'=>'required|integer|min:0','children_count'=>'nullable|integer|min:0','notes'=>'nullable|string']); $data['user_id']=Auth::id(); ServiceAttendance::create($data); return back()->with('success','Mahudhurio yamehifadhiwa.'); }
    public function storeOffering(Request $request) { $this->accountant(); $data = $request->validate(['offering_date'=>'required|date','offering_type'=>'required|string|max:120','amount'=>'required|numeric|min:0','payment_method'=>'required|string|max:80','notes'=>'nullable|string']); $data['user_id']=Auth::id(); Offering::create($data); return back()->with('success','Sadaka imerekodiwa na jumla za mwezi zimesasishwa.'); }
    public function updateMemberStatus(Request $request, User $member) { $this->secretary(); abort_if($member->isAdmin() || $member->isMchungaji(), 403); $data = $request->validate(['status' => 'required|in:active,inactive,moved,deceased']); $member->update($data); return back()->with('success', "Taarifa ya {$member->name} imesasishwa."); }
    private function secretary(): void { abort_unless(Auth::user()->isSecretary(), 403); }
    private function deacon(): void { abort_unless(Auth::user()->isDeacon(), 403); }
    private function accountant(): void { abort_unless(Auth::user()->isAccountant(), 403); }
}
