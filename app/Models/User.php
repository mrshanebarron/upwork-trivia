<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthdate',
        'is_admin',
        'show_name_publicly',
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
            'birthdate' => 'date',
            'is_admin' => 'boolean',
            'show_name_publicly' => 'boolean',
            'total_winnings' => 'decimal:2',
            'last_won_at' => 'datetime',
        ];
    }

    /**
     * Check if user is at least 18 years old
     */
    public function isOfAge(): bool
    {
        return $this->birthdate->diffInYears(now()) >= 18;
    }

    /**
     * Check if user can win (not won in last 30 days)
     */
    public function canWin(): bool
    {
        if (!$this->last_won_at) {
            return true;
        }

        return $this->last_won_at->diffInDays(now()) >= 30;
    }

    /**
     * Get total winnings accessor
     */
    protected function totalWinnings(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => (float) $this->giftCards()->where('status', 'delivered')->sum('amount')
        );
    }

    /**
     * Get last won at accessor
     */
    protected function lastWonAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->winners()->latest('created_at')->first()?->created_at
        );
    }

    // Relationships
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    public function giftCards()
    {
        return $this->hasMany(GiftCard::class);
    }

    public function stickerScans()
    {
        return $this->hasMany(StickerScan::class);
    }
}
