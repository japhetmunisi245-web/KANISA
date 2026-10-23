<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ServiceAttendance extends Model { protected $fillable = ['user_id','service_date','service_name','male_count','female_count','children_count','notes']; protected $casts = ['service_date' => 'date']; public function user(): BelongsTo { return $this->belongsTo(User::class); } }
