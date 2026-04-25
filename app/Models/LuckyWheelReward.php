<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LuckyWheelReward extends Model
{
    protected $fillable = [
        'title',
        'description',
        'weight',
        'valid_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'integer',
            'valid_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function spins(): HasMany
    {
        return $this->hasMany(LuckyWheelSpin::class, 'reward_id');
    }
}
