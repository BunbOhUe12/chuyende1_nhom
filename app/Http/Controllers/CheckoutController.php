<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promotion;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cart = $request->session()->get('cart', ['items' => []]);
        $rawItems = collect($cart['items'] ?? []);

        if ($rawItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $selectedQuery = $request->query('selected');
        $selectedIds = [];
        if ($selectedQuery !== null && $selectedQuery !== '') {
            $selectedIds = is_array($selectedQuery) ? $selectedQuery : [$selectedQuery];
            $selectedIds = array_values(array_unique(array_filter(array_map('intval', $selectedIds))));
        }

        if (! empty($selectedIds)) {
            $items = $rawItems->filter(fn (array $item) => in_array((int) ($item['motorcycle_id'] ?? 0), $selectedIds, true))->values();
            if ($items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Không có sản phẩm được chọn hoặc sản phẩm không còn trong giỏ.');
            }
        } else {
            // Mở /checkout không kèm ?selected= — coi như toàn bộ giỏ (tương thích link cũ)
            $items = $rawItems->values();
        }

        $subtotal = (float) $items->sum('line_total');

        return view('checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'user' => $request->user(),
            'promotions' => Promotion::query()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->orderByDesc('value')
                ->get(),
        ]);
    }

    public function placeOrder(Request $request)
    {
        $cart = $request->session()->get('cart', ['items' => []]);
        $cartItems = collect($cart['items'] ?? []);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $user = Auth::user();
        $existingCustomer = Customer::query()->where('user_id', $user->id)->first();

        $data = $request->validate([
            'checkout_item_ids' => ['required', 'array', 'min:1'],
            'checkout_item_ids.*' => ['integer'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('customers', 'phone')->ignore($existingCustomer?->id),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cod,vnpay'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'accept_terms' => ['accepted'],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
        ], [
            'checkout_item_ids.required' => 'Thiếu danh sách sản phẩm thanh toán.',
            'phone.unique' => 'Số điện thoại này đã được dùng cho hồ sơ khách khác. Vui lòng dùng số khác hoặc liên hệ cửa hàng.',
        ]);

        $checkoutIds = collect($data['checkout_item_ids'])->map(fn ($id) => (int) $id)->unique()->values()->all();

        $items = collect();
        foreach ($checkoutIds as $id) {
            $key = (string) $id;
            if (isset($cart['items'][$key]) && is_array($cart['items'][$key])) {
                $items->push($cart['items'][$key]);
            }
        }

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy sản phẩm hợp lệ trong giỏ để đặt hàng.');
        }

        $order = DB::transaction(function () use ($data, $user, $items) {
            $promotion = null;
            if (! empty($data['promotion_id'])) {
                $promotion = Promotion::query()->find($data['promotion_id']);
                if (! $promotion || ! $promotion->isActive()) {
                    abort(422, 'Voucher không còn hiệu lực.');
                }
            }

            $customer = Customer::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $customer) {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'email' => $user->email,
                    'address' => $data['address'] ?? null,
                ]);
            } else {
                $customer->update([
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? $customer->address,
                ]);
            }

            $subtotal = 0.0;
            $lineItems = [];

            foreach ($items as $item) {
                $motorcycle = Motorcycle::query()->lockForUpdate()->findOrFail($item['motorcycle_id']);
                $qty = (int) $item['quantity'];

                if (! $motorcycle->is_active || $motorcycle->stock < $qty) {
                    abort(422, "Sản phẩm '{$motorcycle->name}' không đủ tồn kho.");
                }

                $unitPrice = (float) $motorcycle->price;
                $lineTotal = round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'motorcycle_id' => $motorcycle->id,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'selected_color' => $item['selected_color'] ?? null,
                    'line_total' => $lineTotal,
                ];

                $motorcycle->decrement('stock', $qty);
            }

            $orderNumber = 'ORD-'.strtoupper(Str::random(10));
            $discountTotal = $promotion ? $this->calculateDiscount($promotion, $subtotal) : 0;
            $total = max(0, $subtotal - $discountTotal);

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'promotion_id' => $promotion?->id,
                'promotion_code' => $promotion?->code,
                'status' => Order::STATUS_PENDING_APPROVAL,
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total' => 0,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lineItems as $li) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'motorcycle_id' => $li['motorcycle_id'],
                    'selected_color' => $li['selected_color'],
                    'unit_price' => $li['unit_price'],
                    'quantity' => $li['quantity'],
                    'line_total' => $li['line_total'],
                ]);
            }

            return $order;
        });

        foreach ($checkoutIds as $id) {
            unset($cart['items'][(string) $id]);
        }
        if (empty($cart['items'])) {
            $request->session()->forget('cart');
        } else {
            $remaining = collect($cart['items']);
            $cart['quantity'] = (int) $remaining->sum('quantity');
            $cart['subtotal'] = round((float) $remaining->sum('line_total'), 2);
            $request->session()->put('cart', $cart);
        }

        if ($data['payment_method'] === 'vnpay') {
            $vnpay = app(VnpayService::class);
            if (! $vnpay->isConfigured()) {
                return redirect()
                    ->route('checkout.show')
                    ->with('error', 'Chưa cấu hình VNPAY: thêm VNPAY_TMN_CODE và VNPAY_HASH_SECRET vào file .env.');
            }

            $payUrl = $vnpay->createPaymentUrl($order, $request->ip() ?: '127.0.0.1');

            return redirect()->away($payUrl);
        }

        return redirect()->route('orders.success', $order);
    }

    public function success(Order $order)
    {
        $order->load(['items.motorcycle', 'customer']);

        return view('order-success', [
            'order' => $order,
        ]);
    }

    public function invoice(Request $request, Order $order)
    {
        $order->load(['items.motorcycle', 'customer']);
        $user = $request->user();

        if (! $order->customer || (int) $order->customer->user_id !== (int) $user->id) {
            abort(403, 'Bạn không có quyền xem hóa đơn này.');
        }

        return view('orders.invoice', [
            'order' => $order,
            'isAdminView' => false,
        ]);
    }

    private function calculateDiscount(Promotion $promotion, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $discount = match ($promotion->type) {
            'percent' => round($subtotal * ((float) $promotion->value / 100), 2),
            default => (float) $promotion->value,
        };

        return round(min($subtotal, max(0, $discount)), 2);
    }
}
