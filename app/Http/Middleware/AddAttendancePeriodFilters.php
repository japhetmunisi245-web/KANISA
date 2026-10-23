<?php

namespace App\Http\Middleware;

use App\Models\Letter;
use App\Models\ServiceAttendance;
use App\Models\ServiceVisitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddAttendancePeriodFilters
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        if (! $user?->isDeacon() || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $years = json_encode($this->recordYears());
        $script = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded',()=>{const panel=document.getElementById('attendance');if(!panel||panel.querySelector('.attendance-period-filter'))return;const q=new URLSearchParams(location.search),years=__RECORD_YEARS__,months={1:'Januari',2:'Februari',3:'Machi',4:'Aprili',5:'Mei',6:'Juni',7:'Julai',8:'Agosti',9:'Septemba',10:'Oktoba',11:'Novemba',12:'Desemba'},options=(values,label,selected)=>`<option value="">${label}</option>`+Object.entries(values).map(([value,text])=>`<option value="${value}" ${String(selected)===String(value)?'selected':''}>${text}</option>`).join(''),form=document.createElement('form'),box=document.createElement('div');form.method='get';form.className='attendance-period-filter';form.style.cssText='display:flex;gap:8px;flex-wrap:wrap;align-items:center';form.innerHTML=`<strong style="width:100%;color:#18213d">Chuja mahudhurio ya ibada</strong><select name="attendance_year" style="width:auto;min-width:140px">${options(Object.fromEntries(years.map(year=>[year,year])),'Miaka yote',q.get('attendance_year'))}</select><select name="attendance_month" style="width:auto;min-width:150px">${options(months,'Miezi yote',q.get('attendance_month'))}</select><select name="attendance_week" style="width:auto;min-width:130px">${options({1:'Wiki ya 1',2:'Wiki ya 2',3:'Wiki ya 3',4:'Wiki ya 4',5:'Wiki ya 5'},'Wiki zote',q.get('attendance_week'))}</select><button class="button">Chuja</button>`;box.className='card';box.style.marginBottom='18px';box.append(form);panel.querySelector('.columns')?.before(box)});
</script>
HTML;

        $script .= <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded',()=>{const q=new URLSearchParams(location.search),panel=document.getElementById('attendance'),form=panel?.querySelector('.attendance-period-filter');if(form&&!form.querySelector('[name="panel"]')){const input=document.createElement('input');input.type='hidden';input.name='panel';input.value='attendance';form.append(input)}if(q.get('panel')==='attendance'){const nav=[...document.querySelectorAll('.nav')].find(item=>item.getAttribute('onclick')?.includes("'attendance'"));if(panel&&typeof show==='function')show('attendance',nav)}});
</script>
HTML;

        $script = str_replace('__RECORD_YEARS__', $years, $script);
        $response->setContent(str_replace('</body>', $script.'</body>', $response->getContent()));

        return $response;
    }

    private function recordYears(): array
    {
        return collect([now()->year])
            ->merge(Letter::query()->pluck('created_at')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->merge(ServiceAttendance::query()->pluck('service_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->merge(ServiceVisitor::query()->pluck('visit_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->year))
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
    }
}
