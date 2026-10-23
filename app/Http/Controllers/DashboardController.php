<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ContributionCampaign;
use App\Models\Department;
use App\Models\HelpMessage;
use App\Models\Letter;
use App\Models\Report;
use App\Models\SmsLog;
use App\Models\User;
use App\Models\ChurchSetting;
use App\Models\ChurchAnnouncement;
use App\Models\Offering;
use App\Models\DutySchedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function markNotificationsRead(Request $request)
    {
        $request->session()->put('notifications_read_at_'.Auth::id(), now()->toIso8601String());

        return response()->noContent();
    }

    /** Change only the dashboard view; the user's account, roles and records remain unchanged. */
    public function setDashboardViewMode(Request $request)
    {
        $data = $request->validate(['mode' => 'required|in:leader,member']);
        /** @var User $user */
        $user = Auth::user();
        $canSwitch = ! $user->isAdmin() && ! $user->isMchungaji()
            && $user->departments()->wherePivot('role', 'kiongozi')->exists();

        abort_unless($canSwitch, 403);
        $request->session()->put('dashboard_view_mode', $data['mode']);

        return redirect()->route('dashboard');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isSecretary() || $user->isDeacon() || $user->isAccountant()) {
            return redirect()->route('church-office.index');
        }
        $user->load('departments');

        $canSwitchDashboardMode = ! $user->isAdmin() && ! $user->isMchungaji()
            && $user->departments->contains(fn (Department $department) => $department->pivot?->role === 'kiongozi');
        $dashboardViewMode = $canSwitchDashboardMode && $request->session()->get('dashboard_view_mode') === 'member'
            ? 'member'
            : 'leader';
        if (! $canSwitchDashboardMode) {
            $request->session()->forget('dashboard_view_mode');
        }
        $user->setAttribute('dashboard_view_mode', $dashboardViewMode === 'member' ? 'member' : null);

        $allDepartmentIds = Department::pluck('id');
        $requestedDepartmentId = $request->integer('department_id');
        $leadershipDepartments = $user->departments->filter(fn (Department $department) => $department->pivot?->role === 'kiongozi')->values();
        $leaderDepartmentIds = ($user->isAdmin() || $user->isMchungaji())
            ? $allDepartmentIds
            : ($user->isKiongozi() ? $leadershipDepartments->pluck('id') : $user->departments->pluck('id'));

        if ($requestedDepartmentId && $leaderDepartmentIds->contains($requestedDepartmentId)) {
            session(['active_department_id' => $requestedDepartmentId]);
        }

        $activeDepartmentId = session('active_department_id');
        if (!$activeDepartmentId || !$leaderDepartmentIds->contains((int) $activeDepartmentId)) {
            $activeDepartmentId = ($user->isAdmin() || $user->isMchungaji())
                ? $allDepartmentIds->first()
                : $user->departments->first()?->id;
            if ($activeDepartmentId) {
                session(['active_department_id' => $activeDepartmentId]);
            }
        }

        $mainDepartment = Department::find($activeDepartmentId) ?? $user->departments->first();
        $allDepartments = Department::with(['users' => function ($query) {
            $query->wherePivot('role', 'kiongozi')
                ->whereNotIn('users.role', ['admin', 'mchungaji'])
                ->whereDoesntHave('roles', function ($roleQuery) {
                    $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
                });
        }])->get();
        $allPayments    = \App\Models\Payment::with(['user', 'contributionCampaign.department'])
            ->whereNotNull('contribution_campaign_id')
            ->latest()
            ->get();
        $visibleDepartmentUsers = function ($query) {
            $query->whereNotIn('users.role', ['admin', 'mchungaji'])
                ->whereDoesntHave('roles', function ($roleQuery) {
                    $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
                });
        };

        $allContributionCampaigns = ContributionCampaign::with([
                'department.users' => $visibleDepartmentUsers,
                'payments.user',
            ])
            ->latest()
            ->get();
        $department = $mainDepartment;
        $leaderPositionCount = ($user->isAdmin() || $user->isMchungaji()) ? $allDepartments->count() : $leadershipDepartments->count();
        $showPositionCounter = !$user->isAdmin() && !$user->isMchungaji() && $leaderPositionCount > 1;
        $activeAnnouncementsQuery = function ($query) {
            $query->where(function ($dateQuery) {
                $dateQuery->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', now()->toDateString());
            });
        };
        // Keep church-wide notices separate so they are always visible on Overview.
        $churchAnnouncements = $this->churchAnnouncementsFor($user);

        if ($user->isAdmin()) {
            // SUPER ADMIN DASHBOARD
            $totalMembers     = User::count();
            $activeMembers    = User::where('status', 'active')->count();
            $deptLeadersCount = User::where('role', 'kiongozi')->orWhereHas('roles', function($q){ $q->where('name', 'kiongozi'); })->count();
            $totalPaymentsSum = \App\Models\Payment::where('status', 'approved')->sum('amount');
            $pendingPayments  = \App\Models\Payment::where('status', 'pending')->count();
            $approvedLetters  = Letter::where('status', 'approved')->orWhere('status', 'generated')->count();
            $pendingLetters   = Letter::where('status', 'pending')->orWhere('status', 'under_review')->count();

            $activeDeptId     = $department?->id;
            $departmentMembers = $department
                ? $department->users()
                    ->with('departments')
                    ->whereNotIn('users.role', ['admin', 'mchungaji'])
                    ->whereDoesntHave('roles', function ($roleQuery) {
                        $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
                    })
                    ->get()
                : collect();
            $adminMembers = User::with('departments')
                ->whereNotIn('role', ['admin', 'mchungaji'])
                ->whereDoesntHave('roles', function ($roleQuery) {
                    $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
                })
                ->latest()
                ->get();
            $members          = $adminMembers;
            $allMembers       = $adminMembers;
            $allLetters       = Letter::with(['user', 'department'])
                ->when(!$user->isMchungaji() && $activeDeptId, fn($query) => $query->where('department_id', $activeDeptId))
                ->latest()
                ->get();
            $allHelpMessages  = HelpMessage::with('user')->latest()->get();
            $auditLogs        = \App\Models\AuditLog::with('user')->latest()->take(15)->get();

            $membersByDept = Department::withCount(['users' => $visibleDepartmentUsers])->get();
            $paymentsByType = \App\Models\Payment::selectRaw('type, sum(amount) as total')
                ->whereHas('contributionCampaign', function ($query) use ($activeDeptId) {
                    if ($activeDeptId) {
                        $query->where('department_id', $activeDeptId);
                    }
                })
                ->groupBy('type')
                ->get();

            $announcements = Announcement::with('department')
                ->when($activeDeptId, fn($query) => $query->where('department_id', $activeDeptId))
                ->where($activeAnnouncementsQuery)
                ->latest()
                ->get();
            $announcements = $announcements->merge($churchAnnouncements)->sortByDesc('created_at')->values();
            $reports = Report::with('department')
                ->when($activeDeptId, fn($query) => $query->where('department_id', $activeDeptId))
                ->latest()
                ->get();
            $letters = $allLetters;
            $smsLogs = SmsLog::with('department')
                ->when($activeDeptId, fn($query) => $query->where('department_id', $activeDeptId))
                ->latest()
                ->get();
            $allPayments = \App\Models\Payment::with(['user', 'contributionCampaign.department'])
                ->whereNotNull('contribution_campaign_id')
                ->whereHas('contributionCampaign', function ($query) use ($activeDeptId) {
                    if ($activeDeptId) {
                        $query->where('department_id', $activeDeptId);
                    }
                })
                ->latest()
                ->get();
            $contributionCampaigns = ContributionCampaign::with([
                    'department.users' => $visibleDepartmentUsers,
                    'payments.user',
                ])
                ->when($activeDeptId, fn($query) => $query->where('department_id', $activeDeptId))
                ->latest()
                ->get();
            $adminDepartmentPaymentGroups = Department::with([
                    'contributionCampaigns' => function ($query) use ($visibleDepartmentUsers) {
                        $query->with([
                            'payments.user',
                            'department.users' => $visibleDepartmentUsers,
                        ])->latest();
                    },
                ])
                ->withCount(['users as members_count' => $visibleDepartmentUsers])
                ->get();
            $adminDepartmentReportGroups = Department::with([
                    'reports' => fn($query) => $query->latest(),
                ])
                ->withCount('reports')
                ->orderByDesc('reports_count')
                ->get();
            $memberSearchData = $adminMembers->map(function (User $member) {
                $approvedPayments = \App\Models\Payment::with('contributionCampaign.department')
                    ->where('user_id', $member->id)
                    ->where('status', 'approved')
                    ->latest()
                    ->get();
                $letters = Letter::with('department')
                    ->where('user_id', $member->id)
                    ->latest()
                    ->get();

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'member_id' => $member->member_id,
                    'role' => ucfirst($member->role ?? 'mshirika'),
                    'cheo' => $member->cheo,
                    'status' => ucfirst($member->status ?? 'active'),
                    'gender' => $member->gender,
                    'dob' => $member->dob ? \Illuminate\Support\Carbon::parse($member->dob)->format('d/m/Y') : null,
                    'address' => $member->address,
                    'avatar' => $member->avatar ? asset('storage/' . $member->avatar) : null,
                    'departments' => $member->departments->pluck('name_sw')->values(),
                    'department_ids' => $member->departments->pluck('id')->values(),
                    'total_paid' => (float) $approvedPayments->sum('amount'),
                    'payments_count' => $approvedPayments->count(),
                    'latest_payment' => optional($approvedPayments->first())->created_at?->format('d/m/Y'),
                    'letters_count' => $letters->count(),
                    'latest_letter' => optional($letters->first())->subject,
                ];
            })->values();
            $myPayments = \App\Models\Payment::with('contributionCampaign.department')->where('user_id', $user->id)->latest()->get();
            $myHelpMessages = HelpMessage::where('user_id', $user->id)->latest()->get();
            $pendingDeptLetters = $letters->whereIn('status', ['pending', 'under_review']);
            $myLetters = Letter::where('user_id', $user->id)->latest()->get();
            $leaderDepartmentsForSwitch = $allDepartments;
            $activeDepartmentCheo = 'System Admin';
            $deptPerformance = Department::withCount([
                    'users as members_count' => $visibleDepartmentUsers,
                    'reports',
                    'contributionCampaigns',
                ])
                ->get()
                ->map(function (Department $dept) use ($visibleDepartmentUsers) {
                    $deptCampaigns = ContributionCampaign::with('payments')
                        ->where('department_id', $dept->id)
                        ->get();
                    $departmentMembers = $dept->users()
                        ->where($visibleDepartmentUsers)
                        ->count();
                    $target = $deptCampaigns->sum(fn($campaign) => (float) $campaign->amount_per_member * max(1, $departmentMembers));
                    $paid = $deptCampaigns->sum(fn($campaign) => $campaign->payments->where('status', 'approved')->sum('amount'));
                    $paymentPercent = $target > 0 ? min(100, round(($paid / $target) * 100)) : 0;
                    $activityScore = min(40, ($dept->reports_count * 10) + ($dept->contribution_campaigns_count * 10));
                    $memberScore = min(20, $dept->members_count * 4);
                    $dept->performance_percent = min(100, $paymentPercent + $activityScore + $memberScore);
                    $dept->paid_amount = $paid;
                    $dept->target_amount = $target;

                    return $dept;
                })
                ->sortByDesc('performance_percent')
                ->values();
            $adminDepartmentFullView = Department::with([
                    'users' => $visibleDepartmentUsers,
                    'reports' => fn($query) => $query->latest()->take(6),
                    'contributionCampaigns' => function ($query) {
                        $query->with(['payments' => fn($paymentQuery) => $paymentQuery->where('status', 'approved')->latest()])
                            ->latest()
                            ->take(6);
                    },
                ])
                ->withCount([
                    'users as members_count' => $visibleDepartmentUsers,
                    'reports',
                    'contributionCampaigns',
                ])
                ->get();

            $isMwenyekiti = true;

            return view('dashboard', compact(
                'user', 'totalMembers', 'activeMembers', 'deptLeadersCount',
                'totalPaymentsSum', 'pendingPayments', 'approvedLetters', 'pendingLetters',
                'allMembers', 'allLetters', 'allPayments', 'allHelpMessages', 'allDepartments',
                'auditLogs', 'membersByDept', 'paymentsByType', 'department', 'members',
                'announcements', 'reports', 'letters', 'smsLogs', 'myPayments',
                'myHelpMessages', 'pendingDeptLetters', 'myLetters', 'allContributionCampaigns',
                'leaderPositionCount', 'showPositionCounter', 'contributionCampaigns',
                'leaderDepartmentsForSwitch', 'activeDepartmentCheo', 'deptPerformance',
                'adminDepartmentPaymentGroups', 'adminDepartmentReportGroups', 'memberSearchData',
                'adminMembers', 'departmentMembers', 'adminDepartmentFullView', 'churchAnnouncements',
                'canSwitchDashboardMode', 'dashboardViewMode', 'isMwenyekiti'
            ));

        } elseif ($user->isKiongozi()) {
            // KIONGOZI DASHBOARD
            $deptIds       = ($user->isAdmin() || $user->isMchungaji()) ? $allDepartmentIds : $leadershipDepartments->pluck('id');
            $activeDeptId  = $department?->id;
            $members       = $user->isMchungaji()
                ? User::with('departments')
                    ->whereNotIn('role', ['admin', 'mchungaji'])
                    ->orderBy('name')
                    ->get()
                : ($department
                ? $department->users()
                    ->with('departments')
                    ->whereNotIn('users.role', ['admin', 'mchungaji'])
                    ->whereDoesntHave('roles', function ($roleQuery) {
                        $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
                    })
                    ->get()
                : collect());
            $announcements = $activeDeptId
                ? Announcement::where('department_id', $activeDeptId)
                    ->where($activeAnnouncementsQuery)
                    ->with('department')
                    ->latest()
                    ->get()
                : collect();
            $announcements = $announcements->merge($churchAnnouncements)->sortByDesc('created_at')->values();
            $reports       = $department ? Report::where('department_id', $department->id)->latest()->get() : collect();
            $letters       = $department ? Letter::where('department_id', $department->id)->latest()->get() : collect();
            $allLetters    = $user->isMchungaji() ? Letter::with(['user', 'department'])->latest()->get() : collect();
            $smsLogs       = $department ? SmsLog::where('department_id', $department->id)->latest()->get() : collect();
            $myPayments    = \App\Models\Payment::with('contributionCampaign.department')->where('user_id', $user->id)->latest()->get();
            $allPayments   = \App\Models\Payment::with(['user', 'contributionCampaign.department'])
                ->when(!$user->isMchungaji(), function ($query) use ($deptIds, $activeDeptId) {
                    $query->whereHas('contributionCampaign', function ($campaignQuery) use ($deptIds) {
                        $campaignQuery->whereIn('department_id', $deptIds);
                    })->when($activeDeptId, function ($paymentQuery) use ($activeDeptId) {
                        $paymentQuery->whereHas('contributionCampaign', function ($campaignQuery) use ($activeDeptId) {
                            $campaignQuery->where('department_id', $activeDeptId);
                        });
                    });
                })
                ->latest()
                ->get();
            $contributionCampaigns = ContributionCampaign::with([
                    'department.users' => $visibleDepartmentUsers,
                    'payments.user',
                ])
                ->when($activeDeptId, fn($query) => $query->where('department_id', $activeDeptId))
                ->latest()
                ->get();

            $myHelpMessages  = HelpMessage::where('user_id', $user->id)->latest()->get();
            $pendingDeptLetters = $letters->whereIn('status', ['pending', 'under_review']);
            $activeDepartmentCheo = $user->isMchungaji()
                ? 'Mchungaji'
                : ($department?->pivot?->cheo ?? $user->cheo ?? 'Kiongozi');
            $leaderDepartmentsForSwitch = ($user->isMchungaji() || $user->isAdmin()) ? $allDepartments : $leadershipDepartments;
            $allOfferings = $user->isMchungaji() ? Offering::latest('offering_date')->get() : collect();

            $isMwenyekiti = $user->isMwenyekiti($department);

            return view('dashboard', compact(
                'user', 'department', 'members', 'announcements', 'reports',
                'letters', 'smsLogs', 'allDepartments', 'allPayments', 'myPayments',
                'leaderPositionCount', 'showPositionCounter', 'myHelpMessages', 'pendingDeptLetters',
                'allLetters', 'contributionCampaigns', 'allContributionCampaigns', 'activeDepartmentCheo',
                'leaderDepartmentsForSwitch', 'churchAnnouncements', 'allOfferings',
                'canSwitchDashboardMode', 'dashboardViewMode', 'isMwenyekiti'
            ));
        } else {
            // MSHIRIKI DASHBOARD
            $departments    = Department::with([
                    'users' => function ($query) {
                        $query->wherePivot('role', 'kiongozi')
                            ->whereNotIn('users.role', ['admin', 'mchungaji'])
                            ->whereDoesntHave('roles', function ($roleQuery) {
                                $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
                            });
                    },
                    'reports' => function ($query) {
                        $query->latest('report_date')->take(5);
                    },
                    'contributionCampaigns.payments' => function ($query) {
                        $query->where('status', 'approved');
                    },
                ])
                ->whereIn('id', $user->departments->pluck('id'))
                ->get();
            $deptIds        = $departments->pluck('id');
            $announcements  = Announcement::whereIn('department_id', $deptIds)
                ->where($activeAnnouncementsQuery)
                ->with('department')
                ->latest()
                ->get();
            $announcements = $announcements->merge($churchAnnouncements)->sortByDesc('created_at')->values();
            $myLetters      = Letter::where('user_id', $user->id)->latest()->get();
            $myPayments     = \App\Models\Payment::with('contributionCampaign.department')->where('user_id', $user->id)->latest()->get();
            $standalonePayments = $myPayments->whereNull('contribution_campaign_id')->values();
            $myHelpMessages = HelpMessage::where('user_id', $user->id)->latest()->get();
            $myContributionCampaigns = ContributionCampaign::with(['department', 'payments' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('status', 'approved');
                }])
                ->whereIn('department_id', $deptIds)
                ->latest()
                ->get()
                ->map(function (ContributionCampaign $campaign) {
                    $paid = $campaign->payments->sum('amount');
                    $campaign->paid_amount = $paid;
                    $campaign->balance_amount = max(0, (float)$campaign->amount_per_member - (float)$paid);

                    return $campaign;
                });
            $pendingContributionReminders = $myContributionCampaigns
                ->filter(function (ContributionCampaign $campaign) {
                    return $campaign->status === 'active'
                        && $campaign->due_date
                        && $campaign->due_date->toDateString() >= now()->toDateString()
                        && (float) ($campaign->balance_amount ?? 0) > 0;
                })
                ->values();

            $totalPaidAmount = $myPayments->where('status', 'approved')->sum('amount');
            $pendingLettersCount  = $myLetters->whereIn('status', ['pending', 'under_review'])->count();
            $approvedLettersCount = $myLetters->whereIn('status', ['approved', 'generated', 'downloaded'])->count();
            $profileIncomplete = !$user->phone || !$user->gender || !$user->dob || !$user->avatar;
            $weeklyDutySchedules = DutySchedule::whereDate(
                'week_start',
                now()->startOfWeek(\Carbon\Carbon::SUNDAY)->toDateString()
            )->orderByRaw("CASE duty WHEN 'VIONGOZI WA PRAISE' THEN 1 WHEN 'MASHEMASI' THEN 2 WHEN 'MZEE WA ZAMU' THEN 3 ELSE 4 END")->get();

            return view('dashboard', compact(
                'user', 'departments', 'announcements', 'myLetters', 'myPayments', 'allPayments',
                'allDepartments', 'myHelpMessages', 'totalPaidAmount',
                'pendingLettersCount', 'approvedLettersCount', 'myContributionCampaigns',
                'pendingContributionReminders', 'churchAnnouncements', 'standalonePayments', 'profileIncomplete', 'weeklyDutySchedules',
                'canSwitchDashboardMode', 'dashboardViewMode'
            ));
        }
    }

    /** Return church notices with their media information intact for the dashboard. */
    private function churchAnnouncementsFor(User $user)
    {
        // "all" is delivered to everyone. The other two audiences are mutually
        // exclusive, so a church notice never leaks to the wrong account type.
        $audience = ($user->isAdmin() || $user->isMchungaji() || $user->isKiongozi())
            ? 'leaders'
            : 'members';

        return ChurchAnnouncement::whereIn('audience', ['all', $audience])
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
            ->latest()
            ->get();
    }

    // Direct Avatar Update from Dashboard
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $avatarPath]);

        return redirect()->back()->with('success', 'Picha yako ya profilu imesasishwa kikamilifu!');
    }

    // Onboarding step 2: Complete department & role selection
    public function completeOnboarding(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Handle avatar upload if provided during onboarding
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'nullable|image|max:2048',
            ]);
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        $role = $request->input('role', 'mshirika');

        $canSelfAssignLeader = $user->isAdmin() || $user->isMchungaji() || $user->role === 'kiongozi' || $user->hasRole('kiongozi');
        if ($role === 'kiongozi' && !$canSelfAssignLeader) {
            return redirect()->back()
                ->with('error', 'Nafasi za uongozi wa idara husajiliwa na kupewa password na Kiongozi Mwenyekiti pekee. Tafadhali chagua kujiunga kama mshirika.');
        }

        if ($role === 'mshirika') {
            $request->validate([
                'role'             => 'required|in:kiongozi,mshirika',
                'department_ids'   => 'required|array|min:1',
                'department_ids.*' => 'exists:departments,id',
            ]);

            $currentCount = $user->departments()->count();
            $newIds       = $request->input('department_ids', []);
            $maxAllowed   = 5;

            if ($currentCount + count($newIds) > $maxAllowed) {
                return redirect()->back()
                    ->with('error', "Huwezi kujiunga na zaidi ya idara {$maxAllowed}. Unabaki na nafasi " . ($maxAllowed - $currentCount) . ".");
            }

            $user->update(['role' => 'mshirika', 'cheo' => null]);

            $syncData = [];
            foreach ($newIds as $deptId) {
                $syncData[(int)$deptId] = ['role' => 'mshirika', 'cheo' => null];
            }
            $user->departments()->syncWithoutDetaching($syncData);

        } else {
            $request->validate([
                'role'          => 'required|in:kiongozi,mshirika',
                'department_id' => 'required|exists:departments,id',
                'cheo'          => 'required|string|max:255',
            ]);

            $currentCount = $user->departments()->count();
            $maxAllowed   = 4;

            if ($currentCount >= $maxAllowed) {
                return redirect()->back()
                    ->with('error', "Huwezi kushikilia zaidi ya nafasi {$maxAllowed} za uongozi.");
            }

            if ($user->departments()->where('department_id', $request->department_id)->exists()) {
                return redirect()->back()
                    ->with('error', 'Tayari una nafasi ya uongozi katika idara hii.');
            }

            $cheoTaken = \Illuminate\Support\Facades\DB::table('department_user')
                ->where('department_id', $request->department_id)
                ->where('role', 'kiongozi')
                ->where('cheo', $request->cheo)
                ->exists();

            if ($cheoTaken) {
                return redirect()->back()
                    ->with('error', "Cheo cha {$request->cheo} tayari kimeshachukuliwa kwenye idara hii.");
            }

            $user->update(['role' => 'kiongozi', 'cheo' => $request->cheo]);

            $user->departments()->syncWithoutDetaching([
                (int)$request->department_id => [
                    'role' => 'kiongozi',
                    'cheo' => $request->cheo,
                ]
            ]);

            session(['active_department_id' => (int) $request->department_id]);
        }

        $roleName = $role === 'kiongozi' ? 'kiongozi' : 'mshiriki';
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
        $user->syncRoles([$roleName]);
        $user->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Hongera! Mipangilio yako ya Idara imekamilika kikamilifu.');
    }

    // Kiongozi: Add a member to his department
    public function addMember(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'email'         => 'required|email|exists:users,email',
        ]);

        /** @var User $leader */
        $leader = Auth::user();

        // Verify leader/admin/mchungaji belongs to or manages this department
        if (!$leader->isAdmin() && !$leader->isMchungaji() && !$leader->departments()->where('department_id', $request->department_id)->exists()) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kusimamia idara hii.');
        }

        $member = User::where('email', $request->email)->first();

        if ($member->isAdmin() || $member->isMchungaji()) {
            return redirect()->back()->with('error', "{$member->name} ana access ya idara zote, hahitaji kuongezwa kama mwanachama wa idara.");
        }

        // Check member dept limit
        $memberDeptCount = $member->departments()->count();
        if ($memberDeptCount >= 5) {
            return redirect()->back()->with('error', "{$member->name} tayari amefika kikomo cha idara 5.");
        }

        if ($member->departments()->where('department_id', $request->department_id)->exists()) {
            return redirect()->back()->with('error', "{$member->name} tayari yuko katika idara hii.");
        }

        $member->departments()->syncWithoutDetaching([
            (int)$request->department_id => ['role' => $member->role ?? 'mshirika', 'cheo' => null]
        ]);

        \App\Models\AuditLog::create([
            'user_id' => $leader->id,
            'action' => 'MEMBER_ADDED',
            'description' => "{$member->name} ameongezwa kwenye idara.",
            'ip_address' => $request->ip(),
            'document_id' => (string) $request->department_id,
        ]);

        return redirect()->back()->with('success', "{$member->name} ameongezwa kwenye idara kikamilifu!");
    }

    // Kiongozi: Remove a member from his department
    public function removeMember(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id'       => 'required|exists:users,id',
        ]);

        /** @var User $leader */
        $leader = Auth::user();

        // Cannot remove yourself
        if ((int)$request->user_id === $leader->id) {
            return redirect()->back()->with('error', 'Huwezi kujiondoa mwenyewe.');
        }

        // Verify leader/admin/mchungaji belongs to or manages this department
        if (!$leader->isAdmin() && !$leader->isMchungaji() && !$leader->departments()->where('department_id', $request->department_id)->exists()) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kusimamia idara hii.');
        }

        $member = User::findOrFail($request->user_id);
        $member->departments()->detach($request->department_id);

        return redirect()->back()->with('success', "{$member->name} ameondolewa kutoka idara.");
    }

    // Kiongozi Mwenyekiti: Register a new leader for the department and set their password
    public function storeDepartmentLeader(Request $request)
    {
        $request->validate([
            'department_id'         => 'required|exists:departments,id',
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'phone'                 => 'required|string|max:30',
            'gender'                => 'nullable|in:male,female',
            'cheo'                  => 'required|string|max:100',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $department = Department::findOrFail($request->department_id);

        // Verify currentUser is Mwenyekiti of this department OR Mchungaji OR Admin
        if (!$currentUser->isAdmin() && !$currentUser->isMchungaji() && !$currentUser->isMwenyekiti($department)) {
            return redirect()->back()->with('error', 'Ni Kiongozi Mwenyekiti pekee mwenye mamlaka ya kusajili viongozi wa idara hii.');
        }

        $email = mb_strtolower(trim($request->email));
        $normalizedCheo = trim($request->cheo);

        // Check if another leader in this department already holds this cheo (unless it's Mjumbe wa Kamati)
        if (!str_contains(mb_strtolower($normalizedCheo), 'mjumbe')) {
            $existingLeaderWithCheo = $department->users()
                ->wherePivot('role', 'kiongozi')
                ->whereRaw('LOWER(department_user.cheo) = ?', [mb_strtolower($normalizedCheo)])
                ->first();

            if ($existingLeaderWithCheo && $existingLeaderWithCheo->email !== $email) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Cheo cha '{$normalizedCheo}' tayari kinashikiliwa na {$existingLeaderWithCheo->name} katika idara hii.");
            }
        }

        $targetUser = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($targetUser) {
            // Already in system - promote or assign leadership in this department
            if ($department->users()->where('users.id', $targetUser->id)->wherePivot('role', 'kiongozi')->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "{$targetUser->name} tayari ni kiongozi mwenye nafasi katika idara hii.");
            }

            $targetUser->update([
                'name'                    => trim($request->name),
                'password'                => Hash::make($request->password),
                'role'                    => 'kiongozi',
                'cheo'                    => $targetUser->cheo ?: $normalizedCheo,
                'phone'                   => $request->phone ?: $targetUser->phone,
                'gender'                  => $request->gender ?: $targetUser->gender,
                'status'                  => 'active',
                'onboarding_completed_at' => now(),
            ]);
        } else {
            // Create brand new leader
            $latestId = User::max('id') + 1;
            $memberId = 'EAGT-KBP-' . str_pad((string) $latestId, 4, '0', STR_PAD_LEFT);

            $targetUser = User::create([
                'name'                    => trim($request->name),
                'email'                   => $email,
                'phone'                   => trim($request->phone),
                'gender'                  => $request->gender,
                'password'                => Hash::make($request->password),
                'role'                    => 'kiongozi',
                'cheo'                    => $normalizedCheo,
                'member_id'               => $memberId,
                'status'                  => 'active',
                'onboarding_completed_at' => now(),
            ]);
        }

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'kiongozi']);
        $targetUser->assignRole('kiongozi');

        $targetUser->departments()->syncWithoutDetaching([
            $department->id => [
                'role' => 'kiongozi',
                'cheo' => $normalizedCheo,
            ]
        ]);

        \App\Models\AuditLog::create([
            'user_id'     => $currentUser->id,
            'action'      => 'LEADER_REGISTERED',
            'description' => "Mwenyekiti {$currentUser->name} amemsajili {$targetUser->name} kama {$normalizedCheo} wa {$department->name_sw}.",
            'ip_address'  => $request->ip(),
            'document_id' => (string) $department->id,
        ]);

        return redirect()->back()
            ->with('success', "Kiongozi {$targetUser->name} amesajiliwa kikamilifu!")
            ->with('registered_leader_credentials', [
                'name'       => $targetUser->name,
                'email'      => $targetUser->email,
                'phone'      => $targetUser->phone,
                'password'   => $request->password,
                'cheo'       => $normalizedCheo,
                'department' => $department->name_sw,
            ]);
    }

    // Kiongozi Mwenyekiti: Change/Set password for a department leader
    public function updateLeaderPassword(Request $request, User $user)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'password'      => 'required|string|min:6|confirmed',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $department = Department::findOrFail($request->department_id);

        if (!$currentUser->isAdmin() && !$currentUser->isMchungaji() && !$currentUser->isMwenyekiti($department)) {
            return redirect()->back()->with('error', 'Ni Kiongozi Mwenyekiti pekee mwenye mamlaka ya kubadilisha nenosiri la viongozi wa idara hii.');
        }

        $isLeaderInDept = $department->users()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'kiongozi')
            ->exists();

        if (!$isLeaderInDept && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Mtumiaji huyu si kiongozi katika idara hii.');
        }

        if (($user->isAdmin() || $user->isMchungaji()) && !$currentUser->isAdmin()) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kubadilisha nenosiri la msimamizi mkuu.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $pivotCheo = $department->users()->where('users.id', $user->id)->first()?->pivot?->cheo ?? $user->cheo ?? 'Kiongozi';

        \App\Models\AuditLog::create([
            'user_id'     => $currentUser->id,
            'action'      => 'LEADER_PASSWORD_UPDATED',
            'description' => "Mwenyekiti {$currentUser->name} amebadili nenosiri la kiongozi {$user->name} ({$pivotCheo}) katika {$department->name_sw}.",
            'ip_address'  => $request->ip(),
            'document_id' => (string) $department->id,
        ]);

        return redirect()->back()
            ->with('success', "Nenosiri la kiongozi {$user->name} limebadilishwa kikamilifu!")
            ->with('updated_leader_credentials', [
                'name'       => $user->name,
                'email'      => $user->email,
                'password'   => $request->password,
                'cheo'       => $pivotCheo,
                'department' => $department->name_sw,
            ]);
    }

    // Kiongozi Mwenyekiti: Remove a leader from department leadership
    public function removeDepartmentLeader(Request $request, User $user)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $department = Department::findOrFail($request->department_id);

        if (!$currentUser->isAdmin() && !$currentUser->isMchungaji() && !$currentUser->isMwenyekiti($department)) {
            return redirect()->back()->with('error', 'Huna mamlaka ya kuondoa kiongozi katika idara hii.');
        }

        if ($user->id === $currentUser->id) {
            return redirect()->back()->with('error', 'Huwezi kujiondoa mwenyewe kwenye uongozi.');
        }

        // Detach from department
        $department->users()->detach($user->id);

        // If user has no more leadership positions, demote role to mshirika
        $hasOtherLeadership = $user->departments()->wherePivot('role', 'kiongozi')->exists();
        if (!$hasOtherLeadership && $user->role === 'kiongozi' && !$user->isAdmin() && !$user->isMchungaji()) {
            $user->update(['role' => 'mshirika', 'cheo' => null]);
            $user->syncRoles(['mshiriki']);
        }

        \App\Models\AuditLog::create([
            'user_id'     => $currentUser->id,
            'action'      => 'LEADER_REMOVED',
            'description' => "Kiongozi {$user->name} ameondolewa kwenye uongozi wa {$department->name_sw}.",
            'ip_address'  => $request->ip(),
            'document_id' => (string) $department->id,
        ]);

        return redirect()->back()->with('success', "Kiongozi {$user->name} ameondolewa kwenye uongozi wa idara.");
    }

    // Help message to admin
    public function sendHelp(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        HelpMessage::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        return redirect()->back()->with('success', 'Ujumbe wako wa msaada umetumwa kwa Admin kikamilifu!');
    }

    // Leader actions: Store Announcement
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'expires_at'    => 'required|date|after_or_equal:today',
        ]);

        $announcement = Announcement::create([
            'department_id' => $request->department_id,
            'user_id'       => Auth::id(),
            'title'         => $request->title,
            'content'       => $request->content,
            'expires_at'    => $request->expires_at,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'ANNOUNCEMENT_CREATED',
            'description' => "Tangazo '{$announcement->title}' limechapishwa.",
            'ip_address' => $request->ip(),
            'document_id' => (string) $announcement->id,
        ]);

        return redirect()->back()->with('success', 'Taarifa imechapishwa kikamilifu!');
    }

    // Leader actions: Store Report
    public function storeReport(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title'         => 'required|string|max:255',
            'type'          => 'required|string',
            'report_date'   => 'required|date',
            'content'       => 'required|string',
        ]);

        $report = Report::create([
            'department_id' => $request->department_id,
            'user_id'       => Auth::id(),
            'title'         => $request->title,
            'type'          => $request->type,
            'report_date'   => $request->report_date,
            'content'       => $request->content,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'REPORT_CREATED',
            'description' => "Ripoti '{$report->title}' imehifadhiwa.",
            'ip_address' => $request->ip(),
            'document_id' => (string) $report->id,
        ]);

        return redirect()->route('reports.show', $report->id)->with('success', 'Ripoti imehifadhiwa kikamilifu!');
    }

    public function showReport($id)
    {
        $report = Report::with(['department', 'user'])->findOrFail($id);
        $this->authorizeReportAccess($report);

        return view('reports.show', $this->reportViewData($report));
    }

    public function downloadReport($id)
    {
        $report = Report::with(['department', 'user'])->findOrFail($id);
        $this->authorizeReportAccess($report);

        $pdf = Pdf::loadView('pdfs.report', $this->reportViewData($report))->setPaper('a4', 'portrait');
        $filename = 'EAGT_KBP_RIPOTI_' . str_pad((string) $report->id, 4, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    private function authorizeReportAccess(Report $report): void
    {
        /** @var User $user */
        $user = Auth::user();

        $belongsToDepartment = $user->departments()
            ->where('departments.id', $report->department_id)
            ->exists();

        if ($report->user_id !== $user->id && !$belongsToDepartment && !$user->isMchungaji() && !$user->isAdmin()) {
            abort(403, 'Hauruhusiwi kuiona ripoti hii.');
        }
    }

    private function reportViewData(Report $report): array
    {
        return [
            'report' => $report,
            'churchName' => ChurchSetting::where('key', 'church_name')->value('value') ?? 'E.A.G.T KIBURUGWA PHILADELPHIA CHURCH',
            'address' => ChurchSetting::where('key', 'address')->value('value') ?? 'P.O. BOX 3236, DAR ES SALAAM',
            'logoPath' => ($logoPath = ChurchSetting::where('key', 'logo_path')->value('value')) ? public_path('storage/' . $logoPath) : null,
            'logoUrl' => $logoPath ? asset('storage/' . $logoPath) : null,
            'typeLabels' => [
                'weekly' => 'Ripoti ya Wiki',
                'monthly' => 'Ripoti ya Mwezi',
                'special' => 'Ripoti Maalumu',
            ],
        ];
    }

    // Leader actions: Store Letter
    public function storeLetter(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'subject'       => 'required|string|max:255',
            'recipient_type'=> 'required|string',
            'body'          => 'required|string',
        ]);

        Letter::create([
            'department_id'  => $request->department_id,
            'user_id'        => Auth::id(),
            'subject'        => $request->subject,
            'recipient_type' => $request->recipient_type,
            'body'           => $request->body,
        ]);

        return redirect()->back()->with('success', 'Barua imetengenezwa kikamilifu!');
    }

    // Leader actions: Send SMS
    public function sendSms(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'message'       => 'required|string|max:160',
        ]);

        $department     = Department::findOrFail($request->department_id);
        $recipientsCount = $department->users()
            ->whereNotIn('users.role', ['admin', 'mchungaji'])
            ->whereDoesntHave('roles', function ($roleQuery) {
                $roleQuery->whereIn('roles.name', ['admin', 'mchungaji']);
            })
            ->count();

        $smsLog = SmsLog::create([
            'department_id'   => $department->id,
            'user_id'         => Auth::id(),
            'message'         => $request->message,
            'recipients_count'=> $recipientsCount,
            'status'          => 'sent',
        ]);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SMS_SENT',
            'description' => "SMS imetumwa kwa wanachama {$recipientsCount} wa {$department->name_sw}.",
            'ip_address' => $request->ip(),
            'document_id' => (string) $smsLog->id,
        ]);

        return redirect()->back()->with('success', "SMS imetumwa kwa wanachama {$recipientsCount} wa {$department->name_sw}!");
    }
}
