<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'motorcycle_id',
        'motorcycle_name',
        'frame_number',
        'engine_number',
        'purchase_date',
        'warranty_start',
        'warranty_end',
        'warranty_type',
        'notes',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date'  => 'date',
            'warranty_start' => 'date',
            'warranty_end'   => 'date',
        ];
    }

    const STATUS_LABELS = [
        'active'    => 'Còn hiệu lực',
        'expired'   => 'Hết hạn',
        'claimed'   => 'Đã sử dụng',
        'cancelled' => 'Đã huỷ',
    ];

    const STATUS_COLORS = [
        'active'    => 'bg-emerald-100 text-emerald-700',
        'expired'   => 'bg-slate-100 text-slate-500',
        'claimed'   => 'bg-amber-100 text-amber-700',
        'cancelled' => 'bg-rose-100 text-rose-600',
    ];

    const TYPE_LABELS = [
        'chinh_hang' => 'Bảo hành chính hãng',
        'mo_rong'    => 'Bảo hành mở rộng',
        'cuahang'    => 'Bảo hành cửa hàng',
    ];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'bg-slate-100 text-slate-500';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->warranty_type] ?? $this->warranty_type;
    }

    public function isExpired(): bool
    {
        return $this->warranty_end < now()->toDateString();
    }
}
