@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Chương trình ưu đãi</h1>
        <p class="mt-2 text-slate-500">Các ưu đãi đặc biệt đang được áp dụng tại đại lý.</p>
    </div>

    @if ($promotions->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 py-20 text-center text-slate-400">
            Hiện chưa có chương trình ưu đãi nào đang chạy.
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($promotions->whereNotNull('slug') as $promo)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                    <a href="{{ route('promotions.show', $promo) }}" class="block overflow-hidden">
                        @if ($promo->thumbnail)
                            <img
                                src="{{ Storage::url($promo->thumbnail) }}"
                                alt="{{ $promo->name }}"
                                class="aspect-[16/9] w-full object-cover transition duration-300 group-hover:scale-105"
                            />
                        @else
                            <div class="flex aspect-[16/9] w-full items-center justify-center bg-gradient-to-br from-brand/20 to-brand/5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-brand/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex flex-wrap gap-2">
                            @if ($promo->type === 'percent')
                                <span class="rounded-full bg-brand px-2.5 py-0.5 text-xs font-bold text-white">
                                    Giảm {{ number_format($promo->value) }}%
                                </span>
                            @else
                                <span class="rounded-full bg-brand px-2.5 py-0.5 text-xs font-bold text-white">
                                    Giảm {{ number_format($promo->value) }}đ
                                </span>
                            @endif
                            @if ($promo->code)
                                <span class="rounded-full border border-slate-200 px-2.5 py-0.5 text-xs font-mono text-slate-600">
                                    {{ $promo->code }}
                                </span>
                            @endif
                        </div>

                        <h2 class="mt-3 font-bold text-slate-900 leading-snug">
                            <a href="{{ route('promotions.show', $promo) }}" class="hover:text-brand">
                                {{ $promo->name }}
                            </a>
                        </h2>

                        @if ($promo->description)
                            <p class="mt-2 text-sm text-slate-500 line-clamp-3 flex-1">{{ $promo->description }}</p>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        @if ($promo->ends_at)
                            <p class="mt-3 text-xs text-slate-400">
                                Hết hạn: {{ $promo->ends_at->format('d/m/Y') }}
                            </p>
                        @endif

                        <a href="{{ route('promotions.show', $promo) }}"
                           class="mt-4 inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-dark">
                            Xem chi tiết ưu đãi
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
