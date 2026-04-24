<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS_PENDING_APPROVAL = 'pending_approval';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_SHIPPING = 'shipping';

    public const STATUS_DELIVERED = 'delivered';

    // Legacy statuses kept for backward compatibility with existing data.
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FULFILLED = 'fulfilled';

    public const STATUS_CANCELLED = 'cancelled';

    /** Các trạng thái được tính vào doanh thu (đã thanh toán / đã hoàn tất). */
    public const REVENUE_STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_SHIPPING,
        self::STATUS_DELIVERED,
        self::STATUS_PAID,
        self::STATUS_FULFILLED,
    ];

    protected $fillable = [
        'order_number',
        'customer_id',
        'promotion_id',
        'promotion_code',
        'status',
        'payment_method',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED,
            self::STATUS_PAID => 'Đã duyệt',
            self::STATUS_SHIPPING => 'Đang giao hàng',
            self::STATUS_DELIVERED,
            self::STATUS_FULFILLED => 'Đã giao hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800',
            self::STATUS_APPROVED,
            self::STATUS_PAID => 'bg-blue-100 text-blue-800',
            self::STATUS_SHIPPING => 'bg-indigo-100 text-indigo-800',
            self::STATUS_DELIVERED,
            self::STATUS_FULFILLED => 'bg-emerald-100 text-emerald-800',
            self::STATUS_CANCELLED => 'bg-rose-100 text-rose-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    /**
     * Đơn tính doanh thu (không pending, không cancelled).
     */
    public function scopeRevenueEligible($query)
    {
        return $query->whereIn('status', self::REVENUE_STATUSES);
    }
}
