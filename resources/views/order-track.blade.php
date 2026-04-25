@extends('layouts.app')

@section('content')
@php
    $status = $order->status;

    // Bước tiến trình
    $steps = [
        ['key' => 'pending_approval', 'label' => 'Chờ\nduyệt',         'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['key' => 'approved',         'label' => 'Đã\nduyệt',          'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['key' => 'shipping',         'label' => 'Đang giao\nhàng',     'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
        ['key' => 'delivered',        'label' => 'Đã giao\nhàng',       'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ];

    // Xác định bước hiện tại
    $activeStep = match($status) {
        'pending_approval', 'pending' => 1,
        'approved', 'paid' => 2,
        'shipping' => 3,
        'delivered', 'fulfilled' => 4,
        default => 0,
    };
    $isCancelled = $status === 'cancelled';
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">Theo dõi đơn hàng</p>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $order->order_number }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('orders.invoice', $order) }}" target="_blank"
               class="rounded-xl border border-brand/30 bg-brand-muted px-4 py-2 text-sm font-semibold text-brand hover:bg-brand/10">
                Xem hóa đơn
            </a>
            <a href="{{ route('orders.lookup') }}"
               class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Danh sách đơn hàng
            </a>
        </div>
    </div>

    {{-- Trạng thái hủy --}}
    @if($isCancelled)
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50 p-6 text-center">
            <svg class="mx-auto h-14 w-14 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="mt-3 text-xl font-bold text-rose-700">Đơn hàng đã bị hủy</h2>
            <p class="mt-1 text-sm text-rose-600">Đơn hàng này đã bị hủy. Vui lòng liên hệ cửa hàng nếu cần hỗ trợ.</p>
            <a href="tel:{{ preg_replace('/\s+/', '', config('store.hotline')) }}"
               class="mt-4 inline-flex items-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700">
                Liên hệ hotline: {{ config('store.hotline') }}
            </a>
        </div>
    @else
        {{-- Thanh tiến trình --}}
        <div class="rounded-2xl border border-slate-200 bg-white px-8 py-8 shadow-sm">
            <h2 class="mb-6 text-sm font-bold uppercase tracking-widest text-slate-500">Trạng thái đơn hàng</h2>

            <div class="relative">
                {{-- Đường kẻ nền --}}
                <div class="absolute left-0 top-6 h-1 w-full rounded-full bg-slate-200"></div>
                {{-- Đường kẻ tiến độ --}}
                @php
                    $pct = match($activeStep) { 1 => '0%', 2 => '33.33%', 3 => '66.66%', 4 => '100%', default => '0%' };
                @endphp
                <div class="absolute left-0 top-6 h-1 rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $pct }}"></div>

                {{-- Các bước --}}
                <div class="relative grid grid-cols-4">
                    @foreach($steps as $i => $step)
                        @php
                            $stepNum   = $i + 1;
                            $done      = $stepNum <= $activeStep;
                            $isCurrent = $stepNum === $activeStep;
                            $circleClass = $done
                                ? 'bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-200'
                                : 'border-2 border-slate-200 bg-white text-slate-400';
                        @endphp
                        <div class="flex flex-col items-center gap-3">
                            {{-- Circle --}}
                            <div class="relative flex h-12 w-12 items-center justify-center rounded-full {{ $circleClass }} transition-all duration-300">
                                @if($done)
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $step['icon'] }}"/>
                                    </svg>
                                    @if($isCurrent)
                                        <span class="absolute -inset-1 animate-ping rounded-full bg-emerald-400 opacity-25"></span>
                                    @endif
                                @else
                                    <span class="text-sm font-bold">{{ $stepNum }}</span>
                                @endif
                            </div>
                            {{-- Label --}}
                            <div class="text-center">
                                <span class="whitespace-pre-line text-xs font-semibold leading-tight {{ $done ? 'text-emerald-700' : 'text-slate-400' }}">{{ $step['label'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ghi chú trạng thái --}}
            <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                @switch($status)
                    @case('pending_approval')
                    @case('pending')
                        🎉 Đơn hàng đã được đặt thành công! Cửa hàng đang chờ duyệt đơn của bạn.
                        @break
                    @case('approved')
                    @case('paid')
                        ✅ Đơn hàng đã được duyệt. Chúng tôi đang chuẩn bị giao xe cho bạn.
                        @break
                    @case('shipping')
                        🚚 Đơn hàng đang được giao. Vui lòng giữ liên lạc để nhận xe.
                        @break
                    @case('delivered')
                    @case('fulfilled')
                        🏍️ Xe đã được giao thành công! Cảm ơn bạn đã tin tưởng mua xe tại <strong>{{ config('app.name') }}</strong>.
                        @break
                @endswitch
            </div>
        </div>
    @endif

    {{-- Chi tiết đơn hàng --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="font-bold uppercase tracking-widest text-slate-500" style="font-size:0.7rem;">Đơn hàng gồm có</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Hình ảnh</th>
                        <th class="px-4 py-3 text-left">Sản phẩm</th>
                        <th class="px-4 py-3 text-right">Giá</th>
                        <th class="px-4 py-3 text-right">Số lượng</th>
                        <th class="px-4 py-3 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-6 py-4">
                                <div class="h-16 w-20 overflow-hidden rounded-xl bg-slate-100">
                                    <img src="{{ \App\Support\MotoImage::cardUrl($item->motorcycle) }}"
                                         class="h-full w-full object-cover"
                                         alt="{{ $item->motorcycle?->name }}"
                                         onerror="this.onerror=null;this.src={{ json_encode(\App\Support\MotoImage::fallbackUrl()) }}">
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $item->motorcycle?->name ?? '—' }}</p>
                                @if($item->motorcycle?->brand)
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $item->motorcycle->brand->name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right font-medium">{{ number_format((float)$item->unit_price) }}₫</td>
                            <td class="px-4 py-4 text-right">
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold">{{ $item->quantity }}</span>
                            </td>
                            <td class="px-4 py-4 text-right font-bold text-slate-900">{{ number_format((float)$item->line_total) }}₫</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-right text-sm font-semibold text-slate-600">Tổng cộng</td>
                        <td class="px-4 py-4 text-right text-lg font-extrabold text-brand">{{ number_format((float)$order->total) }}₫</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Thông tin liên hệ hỗ trợ --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-500">Cần hỗ trợ?</h2>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="tel:{{ preg_replace('/\s+/', '', config('store.hotline')) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Hotline: {{ config('store.hotline') }}
            </a>
            <a href="{{ config('store.social.zalo', '#') }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl bg-[#0068FF] px-4 py-2.5 text-sm font-bold text-white hover:opacity-90">
                Zalo hỗ trợ
            </a>
        </div>
    </div>

</div>
@endsection
