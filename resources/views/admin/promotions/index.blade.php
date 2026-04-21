@extends('layouts.admin')
@section('page-title', 'Chương trình ưu đãi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Chương trình ưu đãi</h1>
            <p class="mt-1 text-sm text-slate-500">Quản lý các chương trình ưu đãi hiển thị trên website.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.promotions.create') }}" class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark">
                + Thêm ưu đãi
            </a>
            <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium hover:bg-slate-50">
                ← Dashboard
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 space-y-4">
        @forelse($promotions as $promo)
            <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center">
                @if ($promo->thumbnail)
                    <img
                        src="{{ Storage::url($promo->thumbnail) }}"
                        alt="{{ $promo->name }}"
                        class="h-20 w-32 flex-shrink-0 rounded-xl object-cover bg-slate-100"
                    />
                @else
                    <div class="flex h-20 w-32 flex-shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-semibold text-slate-900">{{ $promo->name }}</span>
                        @php $st = $promo->getStatus(); @endphp
                        @if ($st === 'active')
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Đang chạy</span>
                        @elseif ($st === 'upcoming')
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Sắp bắt đầu</span>
                        @elseif ($st === 'expired')
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Đã kết thúc</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Tạm dừng</span>
                        @endif
                        @if ($promo->code)
                            <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 font-mono">{{ $promo->code }}</span>
                        @endif
                    </div>
                    <div class="mt-1 text-sm text-slate-500">
                        @if ($promo->type === 'percent')
                            Giảm {{ number_format($promo->value) }}%
                        @else
                            Giảm {{ number_format($promo->value) }}đ
                        @endif
                        @if ($promo->starts_at || $promo->ends_at)
                            ·
                            @if ($promo->starts_at){{ $promo->starts_at->format('d/m/Y') }}@endif
                            –
                            @if ($promo->ends_at){{ $promo->ends_at->format('d/m/Y') }}@endif
                        @endif
                    </div>
                    @if ($promo->description)
                        <p class="mt-1 text-sm text-slate-400 truncate">{{ $promo->description }}</p>
                    @endif
                </div>

                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('promotions.show', $promo) }}" target="_blank"
                       class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">
                        Xem
                    </a>
                    <a href="{{ route('admin.promotions.edit', $promo) }}"
                       class="rounded-lg border border-brand/40 px-3 py-1.5 text-xs font-medium text-brand hover:bg-brand/5">
                        Sửa
                    </a>
                    <form action="{{ route('admin.promotions.destroy', $promo) }}" method="post"
                          onsubmit="return confirm('Xóa ưu đãi này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">
                            Xóa
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 py-16 text-center text-slate-400">
                Chưa có chương trình ưu đãi nào.
                <a href="{{ route('admin.promotions.create') }}" class="ml-1 text-brand underline">Tạo ngay</a>
            </div>
        @endforelse
    </div>
@endsection
