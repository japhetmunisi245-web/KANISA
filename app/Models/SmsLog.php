<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'message',
        'recipients_count',
        'sent_count',
        'failed_count',
        'status',        // sent, partial, failed
        'api_response',  // raw JSON response from Beem
        'error_message', // error string if sending failed
    ];

    protected $casts = [
        'api_response' => 'array',
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
