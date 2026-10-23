<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeaconSpiritualFollowUp extends Model { protected $fillable=['deacon_id','member_id','status','notes','next_follow_up_on']; protected $casts=['next_follow_up_on'=>'date']; public function member(){return $this->belongsTo(User::class,'member_id');} }
