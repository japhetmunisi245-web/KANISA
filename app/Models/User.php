<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'cheo',
        'avatar',
        'gender',
        'dob',
        'address',
        'street_name', 'ward', 'marital_status', 'spouse_names', 'children_count',
        'saved_date', 'baptism_date', 'education', 'service',
        'member_id',
        'status',
        'joined_as_visitor',
        'joined_church_at',
        'origin_location',
        'current_location',
        'previous_church_name',
        'previous_church_location',
        'previous_pastor_name',
        'previous_pastor_phone',
        'introduction_letter_path',
        'onboarding_completed_at',
        'language',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'dob' => 'date',
            'saved_date' => 'date',
            'baptism_date' => 'date',
            'joined_as_visitor' => 'boolean',
            'joined_church_at' => 'date',
            'password' => 'hashed',
        ];
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class)
                    ->withPivot('role', 'cheo')
                    ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isKiongozi(): bool
    {
        if ($this->getAttribute('dashboard_view_mode') === 'member') {
            return false;
        }

        return $this->role === 'kiongozi' || $this->hasRole('kiongozi') || $this->hasRole('mchungaji') || $this->hasRole('admin');
    }

    public function isMshirika(): bool
    {
        if ($this->getAttribute('dashboard_view_mode') === 'member') {
            return true;
        }

        return $this->role === 'mshirika' || $this->hasRole('mshiriki');
    }
    
    public function isMchungaji(): bool
    {
        return $this->role === 'mchungaji' || $this->hasRole('mchungaji');
    }

    public function isSecretary(): bool
    {
        return $this->hasRole('secretary');
    }

    public function isDeacon(): bool
    {
        return $this->hasRole('deacon');
    }

    public function isAccountant(): bool
    {
        return $this->hasRole('accountant');
    }
}
