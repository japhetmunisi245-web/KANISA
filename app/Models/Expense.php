<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['user_id', 'expense_date', 'expense_type', 'amount', 'notes'];

    protected $casts = ['expense_date' => 'date'];
}
