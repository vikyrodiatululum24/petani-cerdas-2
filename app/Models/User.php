<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected static function booted(): void
    {
        static::deleting(function (self $user): void {
            $user->profile?->delete();
            $user->subscriptions()->get()->each->delete();
            $user->sentChats()->get()->each->delete();
            $user->receivedChats()->get()->each->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
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

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function approvedSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'approved_by');
    }

    public function sentChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function receivedChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function chatLimits(): HasMany
    {
        return $this->hasMany(ChatLimits::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class, 'created_by');
    }
}

