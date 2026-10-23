<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeaconVisit extends Model { protected $fillable=['deacon_id','member_id','visited_on','visit_type','notes','follow_up_on']; protected $casts=['visited_on'=>'date','follow_up_on'=>'date']; public function member(){return $this->belongsTo(User::class,'member_id');} }
