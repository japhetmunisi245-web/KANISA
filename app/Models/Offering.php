<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Offering extends Model { protected $fillable = ['user_id','offering_date','offering_type','amount','payment_method','notes']; protected $casts = ['offering_date' => 'date']; }
