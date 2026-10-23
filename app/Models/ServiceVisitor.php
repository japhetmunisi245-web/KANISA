<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVisitor extends Model
{
    protected $fillable = ['user_id', 'member_id', 'service_attendance_id', 'visit_date', 'name', 'phone', 'has_moved'];

    protected $casts = ['visit_date' => 'date', 'has_moved' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function member(): BelongsTo { return $this->belongsTo(User::class, 'member_id'); }
    public function attendance(): BelongsTo { return $this->belongsTo(ServiceAttendance::class, 'service_attendance_id'); }
}
