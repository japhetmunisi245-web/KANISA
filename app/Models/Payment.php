<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'recorded_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'contribution_campaign_id',
        'amount',
        'type',
        'payment_method',
        'transaction_id',
        'payment_date',
        'status',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributionCampaign(): BelongsTo
    {
        return $this->belongsTo(ContributionCampaign::class);
    }
}
