<?php

namespace App\Models;

use Database\Factories\MotorcycleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Motorcycle extends Model
{
    /** @use HasFactory<MotorcycleFactory> */
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'category_id',
        'supplier_id',
        'promotion_id',
        'name',
        'slug',
        'description',
        'price',
        'engine_cc',
        'frame_number',
        'engine_number',
        'color',
        'technical_specs',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'           => 'decimal:2',
            'engine_cc'       => 'integer',
            'stock'           => 'integer',
            'is_active'       => 'boolean',
            'technical_specs' => 'array',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class);
    }

    public function approvedImages(): HasMany
    {
        return $this->images()->where('is_approved', true);
    }

    public function approvedCoverImage(): HasOne
    {
        return $this->hasOne(MotorcycleImage::class)
            ->where('is_approved', true)
            ->latestOfMany();
    }
}
