<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, GeneratesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'phone',
        'avatar',
        'balance',
        'is_seller',
        'is_active',
        'google_id',
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
            'balance' => 'decimal:2',
            'is_seller' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function notificationPreferences()
    {
        return $this->hasMany(\App\Models\NotificationPreference::class);
    }

    public function calculations()
    {
        return $this->hasMany(Calculation::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function factories()
    {
        return $this->hasMany(Factory::class);
    }

    public function umkms()
    {
        return $this->hasMany(Umkm::class);
    }

    public function storeReviews()
    {
        return $this->hasMany(StoreReview::class);
    }

    public function factoryReviews()
    {
        return $this->hasMany(FactoryReview::class);
    }

    // Helper methods
    public function isBuyer(): bool
    {
        return $this->hasRole('buyer');
    }

    public function isSeller(): bool
    {
        return $this->hasRole('seller') || $this->is_seller;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
