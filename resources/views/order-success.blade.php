@extends('layouts.app')

@section('content')
    <div class="rounded-2xl border bg-white p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <h1 class="text-2xl font-bold text-slate-900">Đặt hàng thành công!</h1>
                </div>
                <p class="mt-2 text-sm text-slate-600">
                    Mã đơn: <span class="font-mono font-semibold text-slate-900">{{ $order->order_number }}</span>
                </p>
            </div>
            <a href="{{ route('orders.track', $order) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-brand/30 hover:bg-brand-dark">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Theo dõi đơn hàng
            </a>
        </div>
        @php
            $pm = $order->payment_method ?? '';
            $isPaymentConfirmed = in_array($order->status, ['approved', 'shipping', 'delivered', 'paid', 'fulfilled'], true);
            $pmLabel = match ($pm) {
                'cod' => 'Thanh toán khi nhận hàng (COD)',
                'vnpay' => 'VNPAY',
                default => $pm,
            };
        @endphp
        <p class="mt-1 text-sm text-slate-600">
            Phương thức: <span class="font-medium text-slate-900">{{ $pmLabel }}</span>
            · Trạng thái thanh toán:
            <span class="font-medium text-slate-900">{{ $isPaymentConfirmed ? 'Đã xác nhận đơn' : 'Chờ duyệt' }}</span>
        </p>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">{{ session('error') }}</div>
        @endif

        @if($pm === 'vnpay' && ! $isPaymentConfirmed)
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                Đơn VNPAY chưa ghi nhận thanh toán thành công. Nếu bạn đã thanh toán trên cổng VNPAY, trạng thái sẽ cập nhật sau vài phút (IPN) hoặc vui lòng liên hệ cửa hàng kèm mã đơn.
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h2 class="font-semibold">Chi tiết đơn</h2>
                <div class="mt-3 space-y-3">
                    @foreach($order->items as $it)
                        <div class="flex items-start justify-between rounded-xl border bg-slate-50 p-4 text-sm">
                            <div>
                                <div class="font-medium">{{ $it->motorcycle?->name }}</div>
                                <div class="text-slate-600">{{ $it->quantity }} × {{ number_format((float)$it->unit_price) }}₫</div>
                                @if($it->selected_color)
                                    <div class="text-xs text-slate-500">Màu: {{ $it->selected_color }}</div>
                                @endif
                            </div>
                            <div class="font-semibold">{{ number_format((float)$it->line_total) }}₫</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="rounded-2xl border p-6">
                <h2 class="font-semibold">Tổng kết</h2>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tạm tính</span>
                        <span class="font-medium">{{ number_format((float)$order->subtotal) }}₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Giảm giá</span>
                        <span class="font-medium">{{ number_format((float)$order->discount_total) }}₫</span>
                    </div>
                    @if($order->promotion_code)
                        <div class="flex justify-between">
                            <span class="text-slate-600">Voucher</span>
                            <span class="font-medium">{{ $order->promotion_code }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tổng</span>
                        <span class="font-semibold">{{ number_format((float)$order->total) }}₫</span>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="inline-flex rounded-lg border border-brand/30 bg-brand-muted px-4 py-2 text-sm font-medium text-brand hover:bg-brand/10">
                        Xem hóa đơn
                    </a>
                    <a href="{{ route('shop') }}" class="inline-flex rounded-lg border px-4 py-2 text-sm font-medium hover:bg-slate-50">
                        Mua thêm
                    </a>
                    <a href="{{ route('profile') }}" class="inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">
                        Xem lịch sử
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection

