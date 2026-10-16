<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ChurchSetting;
use App\Models\Department;
use App\Models\Letter;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LetterController extends Controller
{
    // Request a new official letter (Mshiriki or Kiongozi)
    public function store(Request $request)
    {
        $request->validate([
            'letter_type' => 'required|string',
            'recipient'   => 'required|string|max:255',
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!$user->isKiongozi() && !$user->isMchungaji() && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Hauruhusiwi kuandaa au kutengeneza barua rasmi. Ni Viongozi na Mchungaji pekee wanaoweza kuandaa barua.');
        }

        $letter = Letter::create([
            'user_id'            => $user->id,
            'department_id'      => $request->department_id,
            'letter_type'        => $request->letter_type,
            'subject'            => $request->subject,
            'recipient'          => $request->recipient,
            'body'               => $request->body,
            'status'             => 'pending',
            'verification_token' => Str::random(32),
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'LETTER_REQUESTED',
            'description' => "Ombi la barua ya {$letter->letter_type} limewasilishwa.",
            'ip_address'  => $request->ip(),
            'document_id' => (string)$letter->id,
        ]);

        return redirect()->back()->with('success', 'Ombi la barua limewasilishwa kikamilifu! Linasubiri mapitio na idhini.');
    }

    // Approve Letter (Mchungaji Only)
    public function approve(Request $request, $id)
    {
        /** @var User $mchungaji */
        $mchungaji = Auth::user();

        if (!$mchungaji->isMchungaji() && !$mchungaji->isAdmin()) {
            return redirect()->back()->with('error', 'Hauruhusiwi kuidhinisha barua. Ni kwa Mchungaji tu.');
        }

        $letter = Letter::findOrFail($id);

        // If already approved and has reference number, just re-generate PDF
        if ($letter->reference_number && in_array($letter->status, ['approved', 'generated', 'downloaded'])) {
            $this->generatePdf($letter);
            return redirect()->back()->with('success', "Barua KUMB: {$letter->reference_number} - PDF imesasishwa upya!");
        }

        // Generate a guaranteed-unique Reference Number
        $year = date('Y');
        $seq  = Letter::whereNotNull('reference_number')->count() + 1;

        do {
            $refNumber = sprintf("EAGT/KBP/%s/%03d", $year, $seq);
            $seq++;
        } while (Letter::where('reference_number', $refNumber)->exists());

        $letter->update([
            'status'           => 'approved',
            'reference_number' => $refNumber,
            'approved_by'      => $mchungaji->id,
            'approved_at'      => now(),
        ]);

        // Generate PDF
        $this->generatePdf($letter);

        AuditLog::create([
            'user_id'     => $mchungaji->id,
            'action'      => 'LETTER_APPROVED',
            'description' => "Mchungaji ameidhinisha barua KUMB: {$refNumber}",
            'ip_address'  => $request->ip(),
            'document_id' => (string)$letter->id,
        ]);

        return redirect()->back()->with('success', "Barua KUMB: {$refNumber} imeidhinishwa na PDF imetengenezwa!");
    }

    // Reject Letter (Mchungaji or Kiongozi)
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $letter = Letter::findOrFail($id);
        $letter->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'LETTER_REJECTED',
            'description' => "Barua imekataliwa: {$request->rejection_reason}",
            'ip_address'  => $request->ip(),
            'document_id' => (string)$letter->id,
        ]);

        return redirect()->back()->with('success', 'Barua imekataliwa na sababu imehifadhiwa.');
    }

    // Internal Helper: Generate A4 Official Church PDF
    public function generatePdf(Letter $letter)
    {
        $letter->load(['user', 'department']);

        // Ensure verification token exists
        if (!$letter->verification_token) {
            $letter->update(['verification_token' => \Illuminate\Support\Str::random(32)]);
            $letter->refresh();
        }

        // Church settings
        $churchName  = ChurchSetting::where('key', 'church_name')->value('value') ?? 'E.A.G.T KIBURUGWA PHILADELPHIA CHURCH';
        $address     = ChurchSetting::where('key', 'address')->value('value') ?? 'P.O. BOX 3236, DAR ES SALAAM';
        $pastorName  = ChurchSetting::where('key', 'authorized_name')->value('value') ?? 'REV. MAGDALENA MKINGA';
        $pastorTitle = ChurchSetting::where('key', 'authorized_title')->value('value') ?? 'MCHUNGAJI';

        $stampPath     = ChurchSetting::where('key', 'stamp_path')->value('value');
        $signaturePath = ChurchSetting::where('key', 'signature_path')->value('value');
        $logoPath      = ChurchSetting::where('key', 'logo_path')->value('value');

        $approvedDate = $letter->approved_at ?: now();
        $stampData    = $this->officialStampData($address, $approvedDate);
        $verifyUrl    = route('letters.verify', ['token' => $letter->verification_token]);

        // Use base64 data URIs so DomPDF embeds images without needing the GD extension
        $data = [
            'letter'          => $letter,
            'churchName'      => $churchName,
            'address'         => $address,
            'pastorName'      => $pastorName,
            'pastorTitle'     => $pastorTitle,
            'logoBase64'      => $this->imageToBase64($logoPath, 'images/eagt_logo.png'),
            'signatureBase64' => $this->imageToBase64($signaturePath),
            'stampData'       => $stampData,
            'stampSvgBase64'   => $this->officialStampSvg($stampData),
            'approvedDate'    => $approvedDate,
            'verifyUrl'       => $verifyUrl,
            'qrCodeBase64'     => $this->qrCodeBase64($verifyUrl),
        ];

        $pdf = Pdf::loadView('pdfs.official_letter', $data)->setPaper('a4', 'portrait');

        $filename = "letters/EAGT_KBP_LETTER_" . str_replace('/', '_', $letter->reference_number) . ".pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $letter->update([
            'pdf_path' => $filename,
            'status'   => 'generated',
        ]);
    }

    /**
     * Convert an image file to a base64 data URI string.
     * Works regardless of whether the GD extension is loaded in Apache.
     */
    private function imageToBase64(?string $storagePath, ?string $fallback = null): ?string
    {
        $candidates = [];

        if ($storagePath) {
            $clean = ltrim(str_replace('\\', '/', $storagePath), '/');
            $publicPath = preg_replace('#^(\.\./)+#', '', $clean);

            $candidates[] = public_path('storage/' . $clean);
            $candidates[] = storage_path('app/public/' . $clean);
            $candidates[] = public_path($publicPath);
        }

        if ($fallback) {
            $candidates[] = public_path($fallback);
        }

        foreach ($candidates as $path) {
            if ($path && file_exists($path) && is_readable($path)) {
                $mime = mime_content_type($path) ?: 'image/png';
                $encoded = base64_encode(file_get_contents($path));
                return "data:{$mime};base64,{$encoded}";
            }
        }

        return null;
    }

    private function resolvePublicAssetPath(?string $path, ?string $fallback = null): ?string
    {
        $candidates = [];

        if ($path) {
            $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
            $publicPath = preg_replace('#^(\.\./)+#', '', $cleanPath);

            $candidates[] = public_path('storage/' . $cleanPath);
            $candidates[] = public_path($publicPath);
        }

        if ($fallback) {
            $candidates[] = public_path($fallback);
        }

        foreach ($candidates as $candidate) {
            if ($candidate && file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function officialStampData(string $address, $approvedDate): array
    {
        $boxText = trim(preg_replace('/\s+/', ' ', $address)) ?: 'P.O. BOX 3236, DAR ES SALAAM';
        $boxLines = $this->stampAddressLines($boxText);

        return [
            'date' => \Illuminate\Support\Carbon::parse($approvedDate)->format('d M Y'),
            'box_line_one' => strtoupper($boxLines[0]),
            'box_line_two' => strtoupper($boxLines[1]),
            'outer_top' => 'EVANGELISM ASSEMBLY OF GOD',
            'outer_bottom' => 'TANZANIA',
            'inner_bottom' => 'PHILADELPHIA CHURCH',
        ];
    }

    private function officialStampSvg(array $stamp): string
    {
        $blue = '#173f9f';
        $red = '#dc2626';
        $top = $stamp['outer_top'] ?? 'EVANGELISM ASSEMBLY OF GOD';
        $bottom = $stamp['outer_bottom'] ?? 'TANZANIA';
        $innerBottom = $stamp['inner_bottom'] ?? 'PHILADELPHIA CHURCH';
        $boxOne = e($stamp['box_line_one'] ?? 'P. O. BOX 3236');
        $boxTwoText = strtoupper($stamp['box_line_two'] ?? 'DAR - ES - SALAAM');
        if (str_contains($boxTwoText, 'DAR') && str_contains($boxTwoText, 'SALAAM')) {
            $boxTwoText = 'DAR - ES - SALAAM';
        }

        $boxTwo = e($boxTwoText);
        $date = e($stamp['date'] ?? now()->format('d M Y'));
        $topArc = $this->stampArcText($top, 98, 208, 332, 14, 800, $blue, false);
        $innerBottomArc = $this->stampArcText($innerBottom, 74, 140, 40, 13, 800, $blue, true);
        $outerBottomArc = $this->stampArcText($bottom, 100, 105, 75, 14, 800, $blue, true);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="230" height="230" viewBox="0 0 230 230">
  <circle cx="115" cy="115" r="106" fill="none" stroke="{$blue}" stroke-width="3.2"/>
  <circle cx="115" cy="115" r="88" fill="none" stroke="{$blue}" stroke-width="2"/>
  <circle cx="115" cy="115" r="58" fill="none" stroke="{$blue}" stroke-width="1.8"/>
  {$topArc}
  {$innerBottomArc}
  {$outerBottomArc}
  <text x="31" y="123" fill="{$blue}" font-family="DejaVu Sans, Arial, sans-serif" font-size="18" font-weight="800" text-anchor="middle">*</text>
  <text x="199" y="123" fill="{$blue}" font-family="DejaVu Sans, Arial, sans-serif" font-size="18" font-weight="800" text-anchor="middle">*</text>
  <text x="115" y="91" fill="{$blue}" font-family="DejaVu Sans, Arial, sans-serif" font-size="15" font-weight="900" text-anchor="middle">{$boxOne}</text>
  <text x="115" y="115" fill="{$blue}" font-family="DejaVu Sans, Arial, sans-serif" font-size="15" font-weight="900" text-anchor="middle">{$boxTwo}</text>
  <text x="115" y="139" fill="{$red}" font-family="DejaVu Sans, Arial, sans-serif" font-size="13" font-weight="800" text-anchor="middle">{$date}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function stampArcText(string $text, int $radius, float $startAngle, float $endAngle, int $fontSize, int $fontWeight, string $color, bool $reverseDirection): string
    {
        $letters = str_split(strtoupper($text));
        $count = max(count($letters) - 1, 1);
        $pieces = [];

        foreach ($letters as $index => $letter) {
            if ($letter === ' ') {
                continue;
            }

            $angle = $startAngle + (($endAngle - $startAngle) * ($index / $count));
            $x = 115 + ($radius * cos(deg2rad($angle)));
            $y = 115 + ($radius * sin(deg2rad($angle)));
            $rotation = $reverseDirection ? $angle - 90 : $angle + 90;
            $safeLetter = e($letter);

            $pieces[] = sprintf(
                '<text x="%.2f" y="%.2f" transform="rotate(%.2f %.2f %.2f)" fill="%s" font-family="DejaVu Sans, Arial, sans-serif" font-size="%d" font-weight="%d" text-anchor="middle" dominant-baseline="middle">%s</text>',
                $x,
                $y,
                $rotation,
                $x,
                $y,
                $color,
                $fontSize,
                $fontWeight,
                $safeLetter
            );
        }

        return implode("\n  ", $pieces);
    }

    private function qrCodeBase64(string $url): ?string
    {
        try {
            $svg = (string) QrCode::format('svg')
                ->size(92)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($url);

            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to generate letter QR code: ' . $e->getMessage());
            return null;
        }
    }

    private function stampAddressLines(string $address): array
    {
        $address = strtoupper(trim(preg_replace('/\s+/', ' ', $address)));
        $address = str_replace(['P.O.BOX', 'P.O BOX', 'P.O. BOX.'], 'P.O. BOX', $address);

        if (strlen($address) <= 22) {
            return [$address, ''];
        }

        $parts = explode(',', $address, 2);
        if (count($parts) === 2) {
            return [trim($parts[0]), trim($parts[1])];
        }

        return [substr($address, 0, 22), trim(substr($address, 22))];
    }

    // Stream / Preview PDF inline in browser
    public function viewPdf($id)
    {
        $letter = Letter::findOrFail($id);
        $user   = Auth::user();

        // Authorization check: only owner, leader of dept, pastor or admin can view
        if ($letter->user_id !== $user->id && !$user->isKiongozi() && !$user->isMchungaji() && !$user->isAdmin()) {
            abort(403, 'Hauruhusiwi kutazama barua hii.');
        }

        $fullPath = $letter->pdf_path ? storage_path('app/public/' . $letter->pdf_path) : null;

        if (!$fullPath || !file_exists($fullPath)) {
            try {
                $this->generatePdf($letter);
                $letter->refresh();
                $fullPath = storage_path('app/public/' . $letter->pdf_path);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to generate PDF for letter ID {$letter->id}: " . $e->getMessage());
                return redirect()->back()->with('error', 'Imefeli kutengeneza PDF ya barua: ' . $e->getMessage());
            }
        }

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Faili la PDF halikupatikana.');
        }

        return redirect()->to(asset('storage/' . $letter->pdf_path));
    }

    // Download PDF
    public function downloadPdf($id)
    {
        $letter = Letter::findOrFail($id);
        $user   = Auth::user();

        // Authorization check: only owner, leader of dept, or pastor can download
        if ($letter->user_id !== $user->id && !$user->isKiongozi() && !$user->isMchungaji() && !$user->isAdmin()) {
            abort(403, 'Hauruhusiwi kuipakua barua hii.');
        }

        $fullPath = $letter->pdf_path ? storage_path('app/public/' . $letter->pdf_path) : null;

        if (!$fullPath || !file_exists($fullPath)) {
            try {
                $this->generatePdf($letter);
                $letter->refresh();
                $fullPath = storage_path('app/public/' . $letter->pdf_path);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to generate PDF for letter ID {$letter->id}: " . $e->getMessage());
                return redirect()->back()->with('error', 'Imefeli kutengeneza PDF ya barua: ' . $e->getMessage());
            }
        }

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Faili la PDF halikupatikana.');
        }

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'LETTER_DOWNLOADED',
            'description' => "Barua KUMB: {$letter->reference_number} imepakuliwa.",
            'ip_address'  => request()->ip(),
            'document_id' => (string)$letter->id,
        ]);

        return response()->download($fullPath, basename($fullPath), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // Public QR Verification Page
    public function verify($token)
    {
        $letter = Letter::where('verification_token', $token)->with(['user', 'department'])->first();

        return view('letters.verify', compact('letter'));
    }
}
