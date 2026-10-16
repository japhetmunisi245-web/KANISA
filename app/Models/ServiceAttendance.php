<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ServiceAttendance extends Model { protected $fillable = ['user_id','service_date','service_name','male_count','female_count','children_count','notes']; protected $casts = ['service_date' => 'date']; }
