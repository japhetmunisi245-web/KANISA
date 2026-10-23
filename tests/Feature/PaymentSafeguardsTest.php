<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSafeguardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_requires_independent_approval_before_a_receipt_is_created(): void
    {
        $recorder = User::factory()->create(['role' => 'admin']);
        $approver = User::factory()->create(['role' => 'mchungaji']);

        $this->actingAs($recorder)->post(route('payments.store'), [
            'amount' => 15000,
            'type' => 'Sadaka',
            'payment_method' => 'Cash',
            'payment_date' => now()->toDateString(),
            'transaction_id' => 'CASH-20261023-001',
        ])->assertSessionHas('success');

        $payment = Payment::firstOrFail();
        $this->assertSame('pending', $payment->status);
        $this->assertSame($recorder->id, $payment->recorded_by);
        $this->assertDatabaseCount('receipts', 0);

        $this->actingAs($recorder)->post(route('payments.approve', $payment))
            ->assertSessionHas('error');
        $this->assertSame('pending', $payment->fresh()->status);

        $this->actingAs($approver)->post(route('payments.approve', $payment))
            ->assertSessionHas('success');

        $payment->refresh();
        $this->assertSame('approved', $payment->status);
        $this->assertSame($approver->id, $payment->approved_by);
        $this->assertNotNull($payment->approved_at);
        $this->assertDatabaseCount('receipts', 1);
        $this->assertTrue(AuditLog::where('action', 'PAYMENT_APPROVED')->where('document_id', (string) $payment->id)->exists());
    }

    public function test_transaction_reference_cannot_be_recorded_twice(): void
    {
        $recorder = User::factory()->create(['role' => 'admin']);
        $payload = [
            'amount' => 15000,
            'type' => 'Sadaka',
            'payment_method' => 'M-Pesa',
            'payment_date' => now()->toDateString(),
            'transaction_id' => 'MPESA-UNIQUE-001',
        ];

        $this->actingAs($recorder)->post(route('payments.store'), $payload);
        $this->actingAs($recorder)->post(route('payments.store'), $payload)
            ->assertSessionHasErrors('transaction_id');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_receipt_generation_is_idempotent_for_an_approved_payment(): void
    {
        $member = User::factory()->create();
        $payment = Payment::create([
            'user_id' => $member->id,
            'recorded_by' => $member->id,
            'amount' => 15000,
            'type' => 'Sadaka',
            'payment_method' => 'Cash',
            'payment_date' => now()->toDateString(),
            'status' => 'approved',
        ]);
        $controller = app(\App\Http\Controllers\PaymentController::class);

        $first = $controller->generateReceipt($payment);
        $second = $controller->generateReceipt($payment);

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('receipts', 1);
        $this->assertInstanceOf(Receipt::class, $first);
    }
}
