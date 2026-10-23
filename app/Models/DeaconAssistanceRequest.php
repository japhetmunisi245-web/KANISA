<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeaconAssistanceRequest extends Model { protected $fillable=['deacon_id','member_id','category','details','status']; public function member(){return $this->belongsTo(User::class,'member_id');} }
