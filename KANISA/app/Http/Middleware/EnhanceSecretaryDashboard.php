<?php

namespace App\Http\Middleware;

use App\Models\ChurchAnnouncement;
use App\Models\Letter;
use App\Models\ServiceAttendance;
use App\Models\ServiceVisitor;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnhanceSecretaryDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        if (! $user?->isSecretary() || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();

        // The office template is shared legacy markup. Remove its obsolete
        // offerings navigation for the secretary without affecting other roles.
        $html = preg_replace('/<button class="nav" onclick="show\(\'offerings\',this\)">.*?Sadaka<\/button>/', '', $html);
        $html = str_replace(
            "function show(id,b){document.querySelectorAll('.panel').forEach(x=>x.classList.remove('active'));document.querySelectorAll('.nav').forEach(x=>x.classList.remove('active'));document.getElementById(id).classList.add('active');b.classList.add('active')}",
            "function show(id,b){const panel=document.getElementById(id);if(!panel)return;document.querySelectorAll('.panel').forEach(x=>x.classList.remove('active'));document.querySelectorAll('.nav').forEach(x=>x.classList.remove('active'));panel.classList.add('active');if(b)b.classList.add('active')}",
            $html
        );

        // Keep the period controls in the shared legacy template without
        // duplicating its large markup. Values are handled by the controller.
        $years = json_encode(array_reverse(collect($this->recordYears())->values()->all()));
        $periodFilter = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded',()=>{const panel=document.getElementById('attendance');if(!panel||panel.querySelector('.attendance-period-filter'))return;const q=new URLSearchParams(location.search),years=__RECORD_YEARS__,months={1:'Januari',2:'Februari',3:'Machi',4:'Aprili',5:'Mei',6:'Juni',7:'Julai',8:'Agosti',9:'Septemba',10:'Oktoba',11:'Novemba',12:'Desemba'},options=(values,label,selected)=>`<option value="">${label}</option>`+Object.entries(values).map(([value,text])=>`<option value="${value}" ${String(selected)===String(value)?'selected':''}>${text}</option>`).join(''),form=document.createElement('form'),box=document.createElement('div');form.method='get';form.className='member-filters toolbar attendance-period-filter';form.innerHTML=`<select name="attendance_year">${options(Object.fromEntries(years.map(year=>[year,year])),'Miaka yote',q.get('attendance_year'))}</select><select name="attendance_month">${options(months,'Miezi yote',q.get('attendance_month'))}</select><select name="attendance_week">${options({1:'Wiki ya 1',2:'Wiki ya 2',3:'Wiki ya 3',4:'Wiki ya 4',5:'Wiki ya 5'},'Wiki zote',q.get('attendance_week'))}</select><button class="button">Chuja mahudhurio</button>`;box.className='card';box.style.marginBottom='18px';box.append(form);panel.querySelector('.columns')?.before(box)});
</script>
HTML;
        $periodFilter = str_replace('__RECORD_YEARS__', $years, $periodFilter);
        $html = str_replace('</body>', $periodFilter.'</body>', $html);

        $profiles = User::with('departments')->whereNotIn('role', ['admin', 'mchungaji'])->orderBy('name')->get()->mapWithKeys(function (User $member) {
            return [$member->name => [
                'Jina kamili' => $member->name,
                'Barua pepe' => $member->email,
                'Simu' => $member->phone ?: 'Haijawekwa',
                'Namba ya mshirika' => $member->member_id ?: 'Haijawekwa',
                'Jinsia' => $member->gender ?: 'Haijawekwa',
                'Hali ya ndoa' => $member->marital_status ?: 'Haijawekwa',
                'Anwani' => $member->address ?: 'Haijawekwa',
                'Mtaa' => $member->street_name ?: 'Haijawekwa',
                'Kata' => $member->ward ?: 'Haijawekwa',
                'Tarehe ya kuzaliwa' => optional($member->dob)->format('d/m/Y') ?: 'Haijawekwa',
                'Tarehe ya wokovu' => optional($member->saved_date)->format('d/m/Y') ?: 'Haijawekwa',
                'Tarehe ya ubatizo' => optional($member->baptism_date)->format('d/m/Y') ?: 'Haijawekwa',
                'Elimu' => $member->education ?: 'Haijawekwa',
                'Huduma' => $member->service ?: 'Haijawekwa',
                'Jina la mwenzi' => $member->spouse_names ?: 'Haijawekwa',
                'Idadi ya watoto' => $member->children_count ?? 'Haijawekwa',
                'Alipotoka' => $member->origin_location ?: 'Haijawekwa',
                'Alipofikia' => $member->current_location ?: 'Haijawekwa',
                'Kanisa alilotoka' => $member->previous_church_name ?: 'Haijawekwa',
                'Mahali pa kanisa alilotoka' => $member->previous_church_location ?: 'Haijawekwa',
                'Mchungaji alikotoka' => $member->previous_pastor_name ?: 'Haijawekwa',
                'Simu ya mchungaji' => $member->previous_pastor_phone ?: 'Haijawekwa',
                'Barua ya utambulisho' => $member->introduction_letter_path ? asset('storage/'.$member->introduction_letter_path) : null,
                'Idara' => $member->departments->pluck('name_sw')->join(', ') ?: 'Haijawekwa',
            ]];
        });
        $profilesJson = json_encode($profiles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        $announcements = ChurchAnnouncement::with('user')->latest()->get();
        $items = $announcements->map(function (ChurchAnnouncement $announcement): string {
            $title = e($announcement->title);
            $content = e($announcement->content);
            $date = e($announcement->created_at->format('d/m/Y H:i'));
            $author = e($announcement->user?->name ?? 'Ofisi ya Kanisa');

            return "<article class=\"secretary-notification-item\"><strong>{$title}</strong><p>{$content}</p><small>{$date} · {$author}</small></article>";
        })->implode('') ?: '<p class="secretary-notification-empty">Hakuna tangazo lililopo kwa sasa.</p>';

        $count = $announcements->count();
        $widget = <<<HTML
<style>
#secretary-notification-trigger{position:fixed;top:18px;left:265px;z-index:30;border:1px solid #b9e9d1;border-radius:22px;background:#fff;color:#13794d;padding:9px 14px;font:700 13px system-ui;cursor:pointer;box-shadow:0 5px 16px #1d2a4714}#secretary-notification-panel{display:none;position:fixed;top:62px;left:265px;z-index:31;width:min(430px,calc(100vw - 32px));max-height:calc(100vh - 85px);overflow:auto;background:#fff;border:1px solid #e3e9f2;border-radius:12px;box-shadow:0 16px 36px #17213a2b;padding:16px}#secretary-notification-panel.open{display:block}.secretary-notification-head{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e3e9f2;padding-bottom:10px}.secretary-notification-head h3{margin:0;color:#18213d}.secretary-notification-close{border:0;background:transparent;font-size:22px;cursor:pointer;color:#75829a}.secretary-notification-item{padding:13px 0;border-bottom:1px solid #edf0f5}.secretary-notification-item:last-child{border-bottom:0}.secretary-notification-item strong{color:#18213d}.secretary-notification-item p{margin:5px 0;color:#44516b;line-height:1.45}.secretary-notification-item small,.secretary-notification-empty{color:#75829a}@media(max-width:700px){#secretary-notification-trigger{left:14px;top:70px}#secretary-notification-panel{left:16px;top:112px}}
</style>
<button id="secretary-notification-trigger" type="button">🔔 Taarifa ({$count})</button>
<aside id="secretary-notification-panel" aria-label="Matangazo ya kanisa"><div class="secretary-notification-head"><h3>Matangazo ya Kanisa</h3><button type="button" class="secretary-notification-close" aria-label="Funga">×</button></div>{$items}</aside>
<script>const notificationTrigger=document.getElementById('secretary-notification-trigger'),notificationPanel=document.getElementById('secretary-notification-panel');notificationTrigger.addEventListener('click',()=>notificationPanel.classList.toggle('open'));notificationPanel.querySelector('.secretary-notification-close').addEventListener('click',()=>notificationPanel.classList.remove('open'));document.addEventListener('click',e=>{if(!notificationPanel.contains(e.target)&&e.target!==notificationTrigger)notificationPanel.classList.remove('open')});</script>
HTML;

        $memberWidget = <<<HTML
<style>
.schedule-person{display:grid;grid-template-columns:1fr 180px auto;gap:8px;margin-bottom:12px}.schedule-group>.button{margin:0 0 8px}.member-card.member-row{cursor:pointer}.member-card.member-row:hover{border-color:#2bb673}.member-card.member-row[data-moved-in="yes"] .member-details strong{color:#159b62;text-decoration:underline;text-underline-offset:3px}.member-profile-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.member-profile-list div{background:#f7fafc;border:1px solid #e3e9f2;border-radius:8px;padding:10px}.member-profile-list small{display:block;color:#75829a;margin-bottom:3px}@media(max-width:700px){.schedule-person,.member-profile-list{grid-template-columns:1fr}}
</style>
<div class="modal" id="member-profile"><div class="box"><div class="modal-head"><h3 id="member-profile-title">Taarifa za mshirika</h3><button class="close" type="button" onclick="closeModal('member-profile')">&times;</button></div><div id="member-profile-body" class="member-profile-list"></div></div></div>
<script>
const secretaryProfiles={$profilesJson};
document.querySelectorAll('.schedule-person select').forEach(select=>{const phone=select.parentElement.querySelector('.schedule-phone');const fill=()=>{if(phone)phone.value=select.options[select.selectedIndex]?.dataset.phone||'Namba haijawekwa'};select.addEventListener('change',fill);fill()});
function showSecretaryProfile(card){const name=card.querySelector('.member-details strong')?.textContent.trim(),profile=secretaryProfiles[name];if(!profile)return;document.getElementById('member-profile-title').textContent='Taarifa za '+name;const body=document.getElementById('member-profile-body');body.replaceChildren(...Object.entries(profile).map(([label,value])=>{const item=document.createElement('div'),small=document.createElement('small'),strong=document.createElement('strong');small.textContent=label;if(label==='Barua ya utambulisho'&&value){const link=document.createElement('a');link.href=value;link.target='_blank';link.rel='noopener';link.textContent='Tazama barua';strong.replaceWith(link);item.append(small,link)}else{strong.textContent=value||(label==='Barua ya utambulisho'?'Haijaambatanishwa':'Haijawekwa');item.append(small,strong)}return item}));openModal('member-profile')}
document.querySelectorAll('.member-card.member-row').forEach(card=>{card.addEventListener('click',event=>{if(event.target.closest('button,select,form'))return;showSecretaryProfile(card)});if(card.dataset.movedIn==='yes'){const actions=card.querySelector('.member-actions .actions');if(actions){const button=document.createElement('button');button.type='button';button.className='button secondary';button.textContent='Tazama taarifa';button.addEventListener('click',event=>{event.stopPropagation();showSecretaryProfile(card)});actions.prepend(button)}}});
const transferEditFields=[['origin_location','editOriginLocation','Alipotoka (eneo/mji)'],['current_location','editCurrentLocation','Alipofikia (eneo/mtaa)'],['previous_church_name','editPreviousChurchName','Jina la kanisa alilotoka'],['previous_church_location','editPreviousChurchLocation','Mahali pa kanisa alilotoka'],['previous_pastor_name','editPreviousPastorName','Jina la mchungaji alikotoka'],['previous_pastor_phone','editPreviousPastorPhone','Simu ya mchungaji']];
function prepareTransferEditFields(){const form=document.getElementById('editMemberForm'),grid=form?.querySelector('.form-grid');if(!grid||document.getElementById('editOriginLocation'))return;const heading=document.createElement('p');heading.className='full';heading.textContent='Taarifa za uhamisho';heading.style.cssText='font-weight:800;color:#13794d;margin:10px 0 0';grid.append(heading);transferEditFields.forEach(([,id,placeholder])=>{const input=document.createElement('input');input.id=id;input.name=id.replace('edit','').replace(/([A-Z])/g,'_$1').toLowerCase().replace(/^_/,'');input.placeholder=placeholder;grid.append(input)});const letter=document.createElement('div');letter.className='full';letter.innerHTML='<label style="display:block;font-weight:700;margin:8px 0 5px">Barua ya utambulisho</label><input id="editIntroductionLetter" type="file" name="introduction_letter" accept=".pdf,.jpg,.jpeg,.png"><small id="editLetterNote" style="color:#75829a">Pakia faili jipya kubadilisha barua iliyopo (PDF/JPG/PNG, hadi MB 5).</small>';grid.append(letter)}
const nativeEditMember=window.editMember;window.editMember=function(member){prepareTransferEditFields();nativeEditMember(member);transferEditFields.forEach(([key,id])=>{document.getElementById(id).value=member[key]||''});const note=document.getElementById('editLetterNote');if(note)note.textContent=member.introduction_letter_path?'Barua ipo tayari. Pakia faili jipya kubadilisha barua iliyopo.':'Pakia barua ya utambulisho (PDF/JPG/PNG, hadi MB 5).'};
</script>
HTML;

        $response->setContent(str_replace('</body>', $widget.$memberWidget.'</body>', $html));

        return $response;
    }

    private function recordYears(): array
    {
        return collect([now()->year])
            ->merge(Letter::query()->pluck('created_at')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->merge(ServiceAttendance::query()->pluck('service_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->merge(ServiceVisitor::query()->pluck('visit_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->filter()
            ->unique()
            ->sort()
            ->all();
    }
}
