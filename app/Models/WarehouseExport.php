<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseExport extends Model
{
    protected $fillable = [
        'motorcycle_id',
        'quantity',
        'reason',
        'notes',
        'requested_by',
        'status',
        'warehouse_notes',
        'handled_by',
        'confirmed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity'     => 'integer',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    const STATUS_LABELS = [
        'pending'   => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'rejected'  => 'Từ chối',
    ];

    const STATUS_COLORS = [
        'pending'   => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'rejected'  => 'bg-rose-100 text-rose-700',
    ];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
