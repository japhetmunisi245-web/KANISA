<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_sw',
        'name_en',
        'description_sw',
        'description_en',
        'icon',
        'color',
    ];

    /**
     * Get the users belonging to this department.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'cheo')
            ->withTimestamps();
    }

    public function contributionCampaigns(): HasMany
    {
        return $this->hasMany(ContributionCampaign::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
