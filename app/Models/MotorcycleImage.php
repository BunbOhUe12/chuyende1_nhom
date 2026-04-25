<?php

namespace App\Models;

use Database\Factories\MotorcycleImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorcycleImage extends Model
{
    /** @use HasFactory<MotorcycleImageFactory> */
    use HasFactory;

    protected $fillable = [
        'motorcycle_id',
        'path',
        'uploaded_by_user_id',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
