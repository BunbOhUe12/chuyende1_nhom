<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmedForCustomer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with(['customer', 'items']);

        $month = $request->filled('month') ? $request->integer('month') : null;
        $year = $request->filled('year') ? $request->integer('year') : null;

        if ($month !== null && $year !== null) {
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        $pendingApprovals = Order::query()
            ->with('customer')
            ->whereIn('status', [Order::STATUS_PENDING_APPROVAL, Order::STATUS_PENDING])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.orders.index', [
            'orders' => $orders,
            'filterMonth' => $month,
            'filterYear' => $year,
            'pendingApprovals' => $pendingApprovals,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.motorcycle']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending_approval,approved,shipping,delivered,cancelled'],
        ]);

        $previous = $order->status;
        $new = $data['status'];

        $order->update([
            'status' => $new,
        ]);

        if (in_array($previous, [Order::STATUS_PENDING_APPROVAL, Order::STATUS_PENDING], true)
            && in_array($new, [Order::STATUS_APPROVED, Order::STATUS_SHIPPING, Order::STATUS_DELIVERED], true)) {
            $this->sendOrderConfirmedEmail($order->fresh(['customer.user', 'items.motorcycle']));
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Đã cập nhật trạng thái.');
    }

    /** Send confirmation email when status changes from pending to paid or fulfilled. */
    private function sendOrderConfirmedEmail(Order $order): void
    {
        $to = $this->customerOrderNotificationEmail($order);
        if ($to === null) {
            return;
        }

        try {
            Mail::to($to)->send(new OrderConfirmedForCustomer($order));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function customerOrderNotificationEmail(Order $order): ?string
    {
        $order->loadMissing('customer.user');
        $customer = $order->customer;
        if (! $customer) {
            return null;
        }
        if (! empty($customer->email)) {
            return $customer->email;
        }
        if ($customer->user && ! empty($customer->user->email)) {
            return $customer->user->email;
        }

        return null;
    }

    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.motorcycle']);

        return view('orders.invoice', [
            'order' => $order,
            'isAdminView' => true,
        ]);
    }

    public function destroy(Order $order)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Chỉ admin mới được xoá đơn hàng.');

        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', "Đã xoá đơn hàng {$order->order_number}.");
    }
}
