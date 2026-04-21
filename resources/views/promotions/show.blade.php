@extends('layouts.app')

@section('content')
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-brand">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('promotions.index') }}" class="hover:text-brand">Ưu đãi</a>
        <span>/</span>
        <span class="text-slate-700">{{ $promotion->name }}</span>
    </nav>

    <article class="mx-auto max-w-4xl">
        {{-- Banner --}}
        @if ($promotion->banner_image)
            <div class="overflow-hidden rounded-2xl">
                <img
                    src="{{ Storage::url($promotion->banner_image) }}"
                    alt="{{ $promotion->name }}"
                    class="w-full object-cover max-h-[420px]"
                />
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-10">
            {{-- Tags --}}
            <div class="flex flex-wrap gap-2">
                @if ($promotion->type === 'percent')
                    <span class="rounded-full bg-brand px-3 py-1 text-sm font-bold text-white">
                        Giảm {{ number_format($promotion->value) }}%
                    </span>
                @else
                    <span class="rounded-full bg-brand px-3 py-1 text-sm font-bold text-white">
                        Giảm {{ number_format($promotion->value) }}đ
                    </span>
                @endif
                @if ($promotion->code)
                    <span class="rounded-full border border-slate-300 bg-slate-50 px-3 py-1 text-sm font-mono text-slate-700">
                        Mã: {{ $promotion->code }}
                    </span>
                @endif
                @php $status = $promotion->getStatus(); @endphp
                @if ($status === 'active')
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">
                        Đang áp dụng
                    </span>
                @elseif ($status === 'upcoming')
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">
                        Sắp bắt đầu
                    </span>
                @elseif ($status === 'expired')
                    <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-sm font-medium text-slate-500">
                        Đã kết thúc
                    </span>
                @else
                    <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-sm font-medium text-slate-400">
                        Tạm dừng
                    </span>
                @endif
            </div>

            <h1 class="mt-4 text-2xl font-extrabold text-slate-900 md:text-3xl leading-snug">
                {{ $promotion->name }}
            </h1>

            @if ($promotion->starts_at || $promotion->ends_at)
                <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-500">
                    @if ($promotion->starts_at)
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Bắt đầu: <strong>{{ $promotion->starts_at->format('d/m/Y H:i') }}</strong>
                        </span>
                    @endif
                    @if ($promotion->ends_at)
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Hết hạn: <strong>{{ $promotion->ends_at->format('d/m/Y H:i') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

            @if ($promotion->description)
                <div class="prose prose-slate mt-6 max-w-none text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $promotion->description }}
                </div>
            @else
                <p class="mt-6 text-slate-400 italic">Chương trình này hiện chưa có mô tả chi tiết.</p>
            @endif

            @if ($promotion->code)
                <div class="mt-8 flex flex-col items-start gap-3 rounded-xl border border-brand/20 bg-brand/5 p-5 sm:flex-row sm:items-center">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-700">Mã ưu đãi của bạn</p>
                        <p class="mt-1 text-xs text-slate-500">Nhập mã này khi mua hàng để được hưởng ưu đãi.</p>
                    </div>
                    <div class="flex items-center gap-2 rounded-xl border-2 border-brand/30 bg-white px-5 py-2.5">
                        <span class="text-xl font-extrabold tracking-widest text-brand font-mono" id="promo-code">{{ $promotion->code }}</span>
                        <button type="button" onclick="copyCode()" title="Sao chép"
                                class="ml-2 rounded-lg bg-brand/10 p-1.5 text-brand hover:bg-brand/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('shop') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-brand px-6 py-3 text-sm font-bold text-white hover:bg-brand-dark">
                    Mua xe ngay
                </a>
                <a href="{{ route('promotions.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    ← Xem tất cả ưu đãi
                </a>
            </div>
        </div>
    </article>
@endsection

@push('scripts')
<script>
function copyCode() {
    var code = document.getElementById('promo-code').textContent.trim();
    navigator.clipboard.writeText(code).then(function () {
        var btn = document.querySelector('[onclick="copyCode()"]');
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
        setTimeout(function () {
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>';
        }, 2000);
    });
}
</script>
@endpush
