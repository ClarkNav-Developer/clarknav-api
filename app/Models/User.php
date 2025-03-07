<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\VerifyEmailCustom;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',  // Add this line
        'lastname',   // Add this line
        'email',
        'password',
        'isAdmin',
        'isUser',
        'remember_token',
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
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new VerifyEmailCustom);
    // }

    public function isUser()
    {
        return $this->isUser;
    }

    public function isAdmin()
    {
        return $this->isAdmin;
    }

    protected $casts = [
        'isAdmin' => 'boolean',
        'isUser' => 'boolean',
    ];
}
