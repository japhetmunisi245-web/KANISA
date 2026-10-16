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
        'member_id',
        'status',
        'onboarding_completed_at',
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
        return $this->role === 'kiongozi' || $this->hasRole('kiongozi') || $this->hasRole('mchungaji') || $this->hasRole('admin');
    }

    public function isMshirika(): bool
    {
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
