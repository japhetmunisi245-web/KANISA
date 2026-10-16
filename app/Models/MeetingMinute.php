<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MeetingMinute extends Model { protected $fillable = ['user_id','meeting_type','meeting_date','title','minutes']; protected $casts = ['meeting_date' => 'date']; }
