<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeaconMemberAssignment extends Model { protected $fillable=['deacon_id','member_id','assigned_by']; public function member(){return $this->belongsTo(User::class,'member_id');} }
