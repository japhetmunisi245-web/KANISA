<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'letter_type',
        'reference_number',
        'subject',
        'recipient',
        'body',
        'status',
        'rejection_reason',
        'pdf_path',
        'verification_token',
        'approved_by',
        'approved_at',
        'recipient_type', // all, leaders, members
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
