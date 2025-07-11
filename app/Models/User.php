<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable  {  
  
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,CanResetPassword, MustVerifyEmail    ;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'cpf',
        'cnpj',
        'birthday', 
        'phone', 
        'role', 
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password' => 'hashed',
        ];
    } 

    public function subscriptions(): HasOne
    {
        return $this->hasOne(PlanSubscriptions::class, 'id_user', 'id');
    }

    public function activeSubscriptions(): HasOne
    {
        return $this->subscriptions()
            ->where(function ($query) {
                $query->where('end_date', '>', now())
                    ->orWhere('trial_end_date', '>', now());
            })->where('stats', 'active');
    }

    public function activeSubscriptionsPay(): HasOne
    {
        return $this->subscriptions()->where('stats', 'active');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->first_name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    
}

      }
 