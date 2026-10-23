<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeaconReport extends Model { protected $fillable=['deacon_id','title','content','report_date']; protected $casts=['report_date'=>'date']; }
