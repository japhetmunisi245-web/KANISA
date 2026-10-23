<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeaconServicePreparation extends Model { protected $fillable=['deacon_id','service_date','task','status','notes']; protected $casts=['service_date'=>'date']; }
