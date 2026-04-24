@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg">
        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 p-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-3xl">✓</div>
                <h1 class="mt-4 text-xl font-semibold text-green-800">Đặt cọc thành công!</h1>
                <p class="mt-2 text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @elseif(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-3xl">✗</div>
                <h1 class="mt-4 text-xl font-semibold text-rose-800">Thanh toán chưa hoàn tất</h1>
                <p class="mt-2 text-sm text-rose-700">{{ session('error') }}</p>
            </div>
        @endif

        <div class="mt-6 rounded-2xl border bg-white p-6">
            <h2 class="font-semibold">Thông tin phiếu cọc</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Mã phiếu</dt>
                    <dd class="font-medium">{{ $deposit->deposit_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Xe</dt>
                    <dd class="font-medium">{{ $deposit->motorcycle->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Số tiền cọc</dt>
                    <dd class="font-semibold">{{ number_format((float)$deposit->amount) }}₫</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Trạng thái</dt>
                    <dd>
                        @if($deposit->status === 'paid')
                            <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Đã thanh toán</span>
                        @elseif($deposit->status === 'pending')
                            <span class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Chờ thanh toán</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $deposit->status }}</span>
                        @endif
                    </dd>
                </div>
                @if($deposit->expires_at)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Giữ xe đến</dt>
                        <dd class="font-medium">{{ $deposit->expires_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
                @if($deposit->paid_at)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Thanh toán lúc</dt>
                        <dd class="font-medium">{{ $deposit->paid_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        @if($deposit->status === 'paid')
            <div class="mt-4 rounded-2xl border border-violet-200 bg-violet-50 p-5 text-sm text-violet-800">
                <p class="font-semibold">Xe đã được giữ cho bạn!</p>
                <p class="mt-1">Vui lòng đến showroom trước ngày <strong>{{ $deposit->expires_at?->format('d/m/Y') }}</strong> để hoàn tất mua xe. Mang theo mã phiếu <strong>{{ $deposit->deposit_number }}</strong> khi đến.</p>
                <p class="mt-2">Nếu cần hỗ trợ, liên hệ cửa hàng qua trang <a href="{{ route('consultation.index') }}" class="underline">tư vấn</a>.</p>
            </div>
        @endif

        <div class="mt-6 flex gap-3">
            @if($deposit->motorcycle)
                <a href="{{ route('motorcycles.show', $deposit->motorcycle) }}"
                    class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Xem xe
                </a>
            @endif
            <a href="{{ route('profile') }}"
                class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-center text-sm font-medium text-white hover:bg-slate-700">
                Xem phiếu cọc của tôi
            </a>
        </div>
    </div>
@endsection
