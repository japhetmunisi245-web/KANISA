<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChurchSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];
}
