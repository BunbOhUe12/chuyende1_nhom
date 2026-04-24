@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 pb-10">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
        <div class="h-2 w-full bg-gradient-to-r from-brand via-brand-light to-emerald-400"></div>
        <div class="px-6 py-8 sm:px-10">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Theo dõi đơn hàng</p>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Đơn hàng của bạn</h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Chọn tab để lọc theo trạng thái.
                        <span class="text-slate-500">«Chờ thanh toán» gồm đơn chờ duyệt / chờ xử lý thanh toán; «Chờ giao hàng» là đơn đã duyệt, cửa hàng chuẩn bị giao.</span>
                    </p>
                </div>
                <a
                    href="{{ route('profile') }}"
                    class="shrink-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                >
                    Hồ sơ &amp; cài đặt →
                </a>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="-mx-1 border-b border-slate-200 px-2">
            <nav class="flex gap-1 overflow-x-auto pb-px scrollbar-thin" aria-label="Lọc đơn hàng">
                @foreach($orderTabs as $t)
                    @php $isActive = $activeTab === $t['key']; @endphp
                    <a
                        href="{{ route('orders.lookup', ['tab' => $t['key']]) }}"
                        class="shrink-0 whitespace-nowrap border-b-2 px-3 py-3 text-sm font-semibold transition-colors {{ $isActive ? 'border-brand text-brand' : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                    >
                        {{ $t['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="p-5 sm:p-6">
            @if($activeTab === 'return_refund')
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-600">
                    Cửa hàng chưa mở kênh trả hàng / hoàn tiền trực tuyến. Vui lòng liên hệ hotline nếu cần hỗ trợ.
                </div>
            @else
                <div class="space-y-3">
                    @forelse($orders as $o)
                        @include('partials.order-lookup-row', ['order' => $o])
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/80 px-4 py-10 text-center text-sm text-slate-600">
                            Không có đơn nào trong mục này.
                        </p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <p class="mt-8 text-center text-xs text-slate-500">
        Cần hỗ trợ?
        <a href="tel:{{ preg_replace('/\s+/', '', config('store.hotline')) }}" class="font-semibold text-brand hover:underline">{{ config('store.hotline') }}</a>
    </p>
</div>
@endsection
