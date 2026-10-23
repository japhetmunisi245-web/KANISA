<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DutySchedule extends Model { protected $fillable = ['user_id','department_id','week_start','service_name','person_name','duty','notes']; protected $casts = ['week_start' => 'date']; }
