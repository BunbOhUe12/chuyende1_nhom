<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuckyWheelSpin extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'spin_date',
        'reward_title',
        'reward_description',
        'spun_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'spin_date' => 'date',
            'spun_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(LuckyWheelReward::class, 'reward_id');
    }
}
