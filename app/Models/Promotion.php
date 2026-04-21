<?php

namespace App\Models;

use Database\Factories\PromotionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Promotion extends Model
{
    /** @use HasFactory<PromotionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_image',
        'thumbnail',
        'code',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $promotion) {
            if (empty($promotion->slug)) {
                $promotion->slug = Str::slug($promotion->name) ?: Str::random(8);
            }
        });
    }

    public function motorcycles(): HasMany
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function isActive(): bool
    {
        return $this->getStatus() === 'active';
    }

    /**
     * Returns: 'active' | 'upcoming' | 'expired' | 'disabled'
     */
    public function getStatus(): string
    {
        if (! $this->is_active) {
            return 'disabled';
        }
        $now = now();
        if ($this->starts_at && $this->starts_at->gt($now)) {
            return 'upcoming';
        }
        if ($this->ends_at && $this->ends_at->lt($now)) {
            return 'expired';
        }
        return 'active';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
