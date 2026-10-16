<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ChurchSetting;
use App\Models\ContributionCampaign;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function storeContributionCampaign(Request $request)
    {
        $request->validate([
            'department_id'      => 'required|exists:departments,id',
            'title'              => 'required|string|max:255',
            'amount_per_member'  => 'required|numeric|min:100',
            'due_date'           => 'required|date',
            'notes'              => 'nullable|string|max:2000',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin() && !$currentUser->isMchungaji() && !$currentUser->departments()->where('department_id', $request->department_id)->exists()) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuunda mchango kwa idara hii.');
        }

        $campaign = ContributionCampaign::create([
            'department_id' => $request->department_id,
            'created_by' => $currentUser->id,
            'title' => $request->title,
            'amount_per_member' => $request->amount_per_member,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        AuditLog::create([
            'user_id'     => $currentUser->id,
            'action'      => 'CONTRIBUTION_CREATED',
            'description' => "Mchango '{$campaign->title}' umeanzishwa kwa idara.",
            'ip_address'  => $request->ip(),
            'document_id' => (string) $campaign->id,
        ]);

        return redirect()->back()->with('success', 'Mchango wa idara umeanzishwa kikamilifu.');
    }

    // Record payment
    public function store(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:100',
            'type'           => 'nullable|string',
            'contribution_campaign_id' => 'nullable|exists:contribution_campaigns,id',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'transaction_id' => 'nullable|string',
            'notes'          => 'nullable|string',
            'user_id'        => 'nullable|exists:users,id',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $targetUserId = $currentUser->id;

        if (($currentUser->isMchungaji() || $currentUser->isKiongozi()) && $request->filled('user_id')) {
            $targetUserId = $request->user_id;
        }

        $campaign = null;
        $paymentType = $request->type ?: 'Malipo ya Idara';

        if ($request->filled('contribution_campaign_id')) {
            $campaign = ContributionCampaign::with('department')->findOrFail($request->contribution_campaign_id);

            if (!$currentUser->isAdmin() && !$currentUser->isMchungaji() && !$currentUser->departments()->where('department_id', $campaign->department_id)->exists()) {
                return redirect()->back()->with('error', 'Huna ruhusa ya kurekodi malipo ya mchango huu.');
            }

            $targetUser = User::findOrFail($targetUserId);
            if (!$targetUser->departments()->where('department_id', $campaign->department_id)->exists()) {
                return redirect()->back()->with('error', 'Mshirika huyu hayupo kwenye idara ya mchango huu.');
            }

            $paymentType = $campaign->title;
        }

        $payment = Payment::create([
            'user_id'        => $targetUserId,
            'contribution_campaign_id' => $campaign?->id,
            'amount'         => $request->amount,
            'type'           => $paymentType,
            'payment_method' => $request->payment_method,
            'payment_date'   => $request->payment_date,
            'transaction_id' => $request->transaction_id,
            'notes'          => $request->notes,
            'status'         => 'approved',
        ]);

        // Generate receipt
        $this->generateReceipt($payment);

        AuditLog::create([
            'user_id'     => $currentUser->id,
            'action'      => 'PAYMENT_RECORDED',
            'description' => "Malipo ya Tsh " . number_format($payment->amount) . " ({$payment->type}) yamehifadhiwa.",
            'ip_address'  => $request->ip(),
            'document_id' => (string)$payment->id,
        ]);

        return redirect()->back()->with('success', 'Malipo yamehifadhiwa kikamilifu na Risiti imetengenezwa!');
    }

    // Helper: Generate PDF Receipt
    public function generateReceipt(Payment $payment)
    {
        $payment->load('user');

        $year = date('Y');
        $count = Receipt::whereYear('created_at', $year)->count() + 1;
        $receiptNumber = sprintf("EAGT-KBP-RCPT-%s-%04d", $year, $count);

        $receipt = Receipt::create([
            'receipt_number' => $receiptNumber,
            'payment_id'     => $payment->id,
            'user_id'        => $payment->user_id,
        ]);

        $churchName  = ChurchSetting::where('key', 'church_name')->value('value') ?? 'E.A.G.T KIBURUGWA PHILADELPHIA CHURCH';
        $address     = ChurchSetting::where('key', 'address')->value('value') ?? 'P.O. BOX 3236, DAR ES SALAAM';
        $logoPath    = ChurchSetting::where('key', 'logo_path')->value('value');
        $stampPath   = ChurchSetting::where('key', 'stamp_path')->value('value');

        $data = [
            'payment'       => $payment,
            'receipt'       => $receipt,
            'churchName'    => $churchName,
            'address'       => $address,
            'logoPath'      => $logoPath ? public_path('storage/' . $logoPath) : null,
            'stampPath'     => $stampPath ? public_path('storage/' . $stampPath) : null,
        ];

        $pdf = Pdf::loadView('pdfs.receipt', $data)->setPaper('a5', 'landscape');
        $filename = "receipts/EAGT_KBP_RCPT_" . str_replace('-', '_', $receiptNumber) . ".pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $receipt->update(['pdf_path' => $filename]);
    }

    // Download Receipt PDF
    public function downloadReceipt($id)
    {
        $receipt = Receipt::with('payment')->findOrFail($id);
        $user    = Auth::user();

        if ($receipt->user_id !== $user->id && !$user->isMchungaji() && !$user->isKiongozi()) {
            abort(403, 'Hauruhusiwi kuipakua risiti hii.');
        }

        if (!$receipt->pdf_path || !Storage::disk('public')->exists($receipt->pdf_path)) {
            $this->generateReceipt($receipt->payment);
        }

        return Storage::disk('public')->download($receipt->pdf_path);
    }
}
