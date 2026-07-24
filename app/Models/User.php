<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone_number', 'birth_date', 'photo_path', 'status', 'is_admin', 'password', 'menu_sales_record', 'menu_goods_inventory', 'menu_sales_monitoring', 'menu_expenses', 'menu_laundry', 'unique_code', 'menu_split_groups', 'bypass_split_limit', 'profile_photo', 'banner_photo', 'business_address', 'category', 'contact_channel', 'payment_instructions', 'store_name', 'store_description', 'public_email', 'support_phone', 'operating_status', 'timezone', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'birth_date' => 'date',
            'is_admin' => 'boolean',
            'menu_sales_record' => 'boolean',
            'menu_goods_inventory' => 'boolean',
            'menu_sales_monitoring' => 'boolean',
            'menu_expenses' => 'boolean',
            'menu_laundry' => 'boolean',
            'menu_split_groups' => 'boolean',
            'bypass_split_limit' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->unique_code)) {
                $user->unique_code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(\Illuminate\Support\Str::random(8));
        } while (static::where('unique_code', $code)->exists());

        return $code;
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function groups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function groupMemberships(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function joinedGroups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id')->withTimestamps();
    }

    public function merchants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'merchant_customer', 'customer_id', 'merchant_id')->withTimestamps();
    }

    public function customers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'merchant_customer', 'merchant_id', 'customer_id')->withTimestamps();
    }

    public function laundryServices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LaundryService::class);
    }

    public function laundryOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }

    public function merchantSetting(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LaundryMerchantSetting::class);
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->photo_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->photo_path)
            : null;
    }

}
