@extends('layouts.admin')
@section('page-title', 'Chi tiết đơn hàng')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold">Chi tiết đơn hàng</h1>
            <div class="mt-1 text-sm text-slate-600">
                Mã đơn: <span class="font-medium text-slate-900">{{ $order->order_number }}</span>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="text-sm text-brand hover:underline">In hóa đơn</a>
            @if(auth()->user()->isAdmin())
            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                  onsubmit="return confirm('Xoá đơn hàng {{ $order->order_number }}? Hành động này không thể hoàn tác.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm text-rose-500 hover:underline">Xoá đơn hàng</button>
            </form>
            @endif
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-600 hover:underline">Quay lại</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border bg-white p-6">
            <h2 class="font-semibold">Sản phẩm</h2>
            <div class="mt-4 space-y-3 text-sm">
                @foreach($order->items as $it)
                    <div class="flex items-start justify-between rounded-xl border bg-slate-50 p-4">
                        <div class="min-w-0">
                            <div class="truncate font-medium">{{ $it->motorcycle?->name }}</div>
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

        <aside class="space-y-4">
            <div class="rounded-2xl border bg-white p-6">
                <h2 class="font-semibold">Khách hàng</h2>
                <div class="mt-3 text-sm">
                    <div class="text-slate-600">Tên</div>
                    <div class="font-medium">{{ $order->customer?->full_name ?? '—' }}</div>
                </div>
                <div class="mt-3 text-sm">
                    <div class="text-slate-600">SĐT</div>
                    <div class="font-medium">{{ $order->customer?->phone ?? '—' }}</div>
                </div>
                <div class="mt-3 text-sm">
                    <div class="text-slate-600">Địa chỉ</div>
                    <div class="font-medium">{{ $order->customer?->address ?? '—' }}</div>
                </div>
                @if($order->customer)
                    <a href="{{ route('admin.customers.show', $order->customer) }}"
                       class="mt-4 inline-block text-sm font-medium text-brand hover:underline">Xem ho so CRM</a>
                @endif
            </div>

            <div class="rounded-2xl border bg-white p-6">
                <h2 class="font-semibold">Thanh toán</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Phương thức:
                    @php
                        $pm = $order->payment_method ?? '';
                        echo match ($pm) {
                            'cod' => 'Thanh toán khi nhận hàng (COD)',
                            'vnpay' => 'VNPAY',
                            'showroom_deposit' => 'Đặt cọc showroom',
                            'cod_consult' => 'COD (tư vấn)',
                            'bank_transfer_deposit' => 'Chuyển khoản cọc',
                            default => $pm ?: '—',
                        };
                    @endphp
                </p>
                @if($order->promotion_code)
                    <p class="mt-2 text-sm text-slate-600">Voucher áp dụng: <span class="font-medium">{{ $order->promotion_code }}</span></p>
                @endif
            </div>

            <div class="rounded-2xl border bg-white p-6">
                <h2 class="font-semibold">Trạng thái</h2>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="post" class="mt-4 space-y-3 text-sm">
                    @csrf
                    @method('PATCH')

                    <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        @foreach([
                            'pending_approval' => 'Chờ duyệt',
                            'approved' => 'Đã duyệt',
                            'shipping' => 'Đang giao hàng',
                            'delivered' => 'Đã giao hàng',
                            'cancelled' => 'Đã hủy',
                        ] as $st => $label)
                            <option value="{{ $st }}" @selected($order->status === $st)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="text-rose-600">{{ $message }}</div>@enderror

                    <button class="w-full rounded-lg bg-slate-900 px-4 py-2 font-medium text-white">Cập nhật</button>
                </form>

                <div class="mt-4 border-t pt-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tạm tính</span>
                        <span class="font-medium">{{ number_format((float)$order->subtotal) }}₫</span>
                    </div>
                    <div class="mt-2 flex justify-between">
                        <span class="text-slate-600">Giảm giá</span>
                        <span class="font-medium">-{{ number_format((float)$order->discount_total) }}₫</span>
                    </div>
                    <div class="mt-2 flex justify-between">
                        <span class="text-slate-600">Tổng</span>
                        <span class="font-semibold">{{ number_format((float)$order->total) }}₫</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection

