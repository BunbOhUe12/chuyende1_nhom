<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Danh sách đơn của tài khoản, lọc theo tab (giao diện ngang).
     */
    public function index(Request $request): View
    {
        $userId = (int) $request->user()->id;

        $tab = (string) $request->query('tab', 'all');
        $allowedTabs = [
            'all',
            'awaiting_payment',
            'shipping',
            'awaiting_delivery',
            'completed',
            'cancelled',
            'return_refund',
        ];
        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'all';
        }

        $orders = collect();
        if ($tab !== 'return_refund') {
            $orders = Order::query()
                ->whereHas('customer', fn ($q) => $q->where('user_id', $userId))
                ->with(['items.motorcycle'])
                ->latest()
                ->when($tab !== 'all', function ($q) use ($tab) {
                    match ($tab) {
                        'awaiting_payment' => $q->whereIn('status', [
                            Order::STATUS_PENDING_APPROVAL,
                            Order::STATUS_PENDING,
                        ]),
                        'shipping' => $q->where('status', Order::STATUS_SHIPPING),
                        'awaiting_delivery' => $q->whereIn('status', [
                            Order::STATUS_APPROVED,
                            Order::STATUS_PAID,
                        ]),
                        'completed' => $q->whereIn('status', [
                            Order::STATUS_DELIVERED,
                            Order::STATUS_FULFILLED,
                        ]),
                        'cancelled' => $q->where('status', Order::STATUS_CANCELLED),
                        default => $q,
                    };
                })
                ->get();
        }

        $orderTabs = [
            ['key' => 'all', 'label' => 'Tất cả'],
            ['key' => 'awaiting_payment', 'label' => 'Chờ thanh toán'],
            ['key' => 'shipping', 'label' => 'Vận chuyển'],
            ['key' => 'awaiting_delivery', 'label' => 'Chờ giao hàng'],
            ['key' => 'completed', 'label' => 'Hoàn thành'],
            ['key' => 'cancelled', 'label' => 'Đã hủy'],
            ['key' => 'return_refund', 'label' => 'Trả hàng/Hoàn tiền'],
        ];

        return view('order-lookup', [
            'orders' => $orders,
            'activeTab' => $tab,
            'orderTabs' => $orderTabs,
        ]);
    }

    /** Xem tracking từ trang đơn hàng (yêu cầu đăng nhập + chủ đơn) */
    public function show(Request $request, Order $order): View
    {
        $order->load(['customer', 'items.motorcycle.approvedCoverImage']);

        if ($order->customer && (int) $order->customer->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        return view('order-track', ['order' => $order]);
    }
}
