@extends('layouts.app')

@section('content')
    <div class="mb-5 inline-flex flex-wrap items-center gap-3 rounded-2xl border border-white/70 bg-white/85 p-2 shadow-sm backdrop-blur">
        <a
            href="{{ route('shop', array_merge(request()->except(['page', 'type', 'category_id']), ['type' => 'xe'])) }}"
            class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ ($productType ?? 'xe') === 'xe' ? 'border-brand bg-brand text-white' : 'border-slate-300 bg-white text-slate-800 hover:border-brand/40 hover:text-brand' }}"
        >
            Xe ({{ $vehicleCount ?? 0 }})
        </a>
        <a
            href="{{ route('shop', array_merge(request()->except(['page', 'type', 'category_id']), ['type' => 'phu-tung'])) }}"
            class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ ($productType ?? 'xe') === 'phu-tung' ? 'border-brand bg-brand text-white' : 'border-slate-300 bg-white text-slate-800 hover:border-brand/40 hover:text-brand' }}"
        >
            Phụ tùng ({{ $partsCount ?? 0 }})
        </a>
    </div>

    <div class="flex flex-col gap-6 lg:flex-row">
        <aside class="w-full lg:w-72">
            <div class="rounded-xl border bg-white p-4">
                <h2 class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 font-semibold text-slate-900">Bộ lọc</h2>
                <form action="{{ route('shop') }}" method="get" class="mt-4 space-y-3 text-sm">
                    <input type="hidden" name="type" value="{{ $productType ?? 'xe' }}"/>
                    <div>
                        <label class="block text-slate-600">Từ khóa</label>
                        <input name="q" value="{{ $filters['q'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"/>
                    </div>
                    <div>
                        <label class="block text-slate-600">Hãng</label>
                        <select name="brand_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                            <option value="">Tất cả</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" @selected(($filters['brand_id'] ?? '') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-600">Danh mục</label>
                        <select name="category_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                            <option value="">Tất cả</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(($filters['category_id'] ?? '') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-600">Giá từ</label>
                            <input name="min_price" value="{{ $filters['min_price'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"/>
                        </div>
                        <div>
                            <label class="block text-slate-600">Đến</label>
                            <input name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"/>
                        </div>
                    </div>
                    <button class="w-full rounded-lg bg-brand px-3 py-2 font-medium text-white hover:bg-brand-dark">Áp dụng</button>
                    <a href="{{ route('shop') }}" class="block text-center text-slate-600 hover:underline">Xóa lọc</a>
                </form>
            </div>
        </aside>

        <section class="flex-1">
            <div class="flex flex-wrap items-center justify-between gap-2 rounded-2xl border border-white/70 bg-white/85 px-3 py-2 shadow-sm backdrop-blur">
                <h1 class="rounded-lg bg-slate-100 px-3 py-1.5 text-xl font-semibold text-slate-900">{{ ($productType ?? 'xe') === 'phu-tung' ? 'Danh mục phụ tùng' : 'Danh mục xe' }}</h1>
                <div class="rounded-lg bg-slate-100 px-2.5 py-1 text-sm font-medium text-slate-700">
                    @if(method_exists($motorcycles, 'total'))
                        {{ $motorcycles->total() }} sản phẩm
                    @endif
                </div>
            </div>

            @if(($productType ?? 'xe') === 'xe')
                <div class="mt-3 rounded-2xl border border-white/70 bg-white/85 p-2 shadow-sm backdrop-blur">
                    <div class="flex flex-wrap items-center gap-2">
                        <a
                            href="{{ route('shop', array_merge(request()->except(['page', 'brand_id']), ['type' => 'xe'])) }}"
                            class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ empty($filters['brand_id']) ? 'border-brand bg-brand text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-brand/40 hover:text-brand' }}"
                        >
                            Tất cả xe
                        </a>
                        @foreach($brands as $b)
                            <a
                                href="{{ route('shop', array_merge(request()->except(['page', 'brand_id']), ['type' => 'xe', 'brand_id' => $b->id])) }}"
                                class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ (string)($filters['brand_id'] ?? '') === (string)$b->id ? 'border-brand bg-brand text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-brand/40 hover:text-brand' }}"
                            >
                                {{ $b->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="relative z-0 mt-4 grid gap-4 overflow-visible sm:grid-cols-2 lg:grid-cols-3">
                @forelse($motorcycles as $m)
                    <article
                        tabindex="0"
                        data-hover-card
                        class="group relative rounded-xl border bg-white p-4 transition duration-200 hover:z-30 hover:-translate-y-1 hover:border-brand/30 hover:shadow-xl focus-within:z-30 focus-within:-translate-y-1 focus-within:border-brand/30 focus-within:shadow-xl focus-within:outline-none focus-visible:ring-2 focus-visible:ring-brand"
                    >
                        <div
                            data-hover-panel
                            class="pointer-events-none absolute left-0 top-full z-50 mt-2 w-[min(20rem,calc(100vw-1.5rem))] max-h-[min(28rem,72vh)] translate-y-1 scale-[0.98] overflow-y-auto overscroll-contain rounded-xl border border-slate-200 bg-white p-4 text-left opacity-0 shadow-2xl shadow-slate-900/20 ring-1 ring-slate-900/5 transition duration-200 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:scale-100 group-hover:opacity-100 group-focus-within:pointer-events-auto group-focus-within:translate-y-0 group-focus-within:scale-100 group-focus-within:opacity-100 lg:left-auto lg:right-0"
                            role="region"
                            aria-label="Thông tin {{ $m->name }}"
                        >
                            <div class="border-b border-brand/25 pb-2">
                                <p class="text-sm font-extrabold leading-snug text-slate-900">{{ $m->name }}</p>
                                <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-wide text-brand">{{ $m->brand->name ?? '' }} · {{ $m->category->name ?? '' }}</p>
                            </div>
                            <dl class="mt-3 space-y-2 text-xs text-slate-800">
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Hãng</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $m->brand->name ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Danh mục</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $m->category->name ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Giá</dt>
                                    <dd class="text-right text-sm font-bold text-brand">{{ number_format((float)$m->price) }}đ</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Tồn kho</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $m->stock }}</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Thông số</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $m->engine_cc ? $m->engine_cc.' cc' : 'Phụ tùng / không áp dụng' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Màu</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $m->color ?: '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                                    <dt class="shrink-0 font-medium text-slate-500">Mã SP</dt>
                                    <dd class="max-w-[58%] break-all text-right font-mono text-[10px] font-medium text-slate-700">{{ $m->frame_number ?: '—' }}</dd>
                                </div>
                                @if($m->promotion)
                                    <div class="flex justify-between gap-3 pb-1">
                                        <dt class="shrink-0 font-medium text-slate-500">Khuyến mãi</dt>
                                        <dd class="text-right font-semibold text-amber-700">{{ $m->promotion->name }}</dd>
                                    </div>
                                @endif
                            </dl>
                            @if($m->description)
                                <p class="mt-3 border-t border-slate-100 pt-3 text-xs leading-relaxed text-slate-600">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($m->description), 240) }}
                                </p>
                            @endif
                        </div>
                        <div class="aspect-[4/3] overflow-hidden rounded-lg border bg-slate-100">
                            <img src="{{ \App\Support\MotoImage::cardUrl($m) }}" class="h-full w-full object-cover" alt="{{ $m->name }}" onerror="this.onerror=null;this.src={{ json_encode(\App\Support\MotoImage::fallbackUrl()) }}" />
                        </div>
                        <div class="mt-3 text-xs text-slate-500">{{ $m->brand->name ?? '' }} · {{ $m->category->name ?? '' }}</div>
                        <a href="{{ route('motorcycles.show', $m) }}" class="mt-1 block font-medium hover:underline">{{ $m->name }}</a>
                        <div class="mt-2 text-sm"><span class="font-semibold">{{ number_format((float)$m->price) }}đ</span><span class="text-slate-500"> · Tồn {{ $m->stock }}</span></div>
                        <form action="{{ route('cart.add', $m) }}" method="post" class="mt-3">
                            @csrf
                            <button class="w-full rounded-lg bg-brand px-3 py-2 text-sm font-medium text-white hover:bg-brand-dark">Thêm giỏ hàng</button>
                        </form>
                    </article>
                @empty
                    <div class="rounded-xl border bg-white p-6 text-sm text-slate-600">Không tìm thấy xe hoặc phụ tùng phù hợp.</div>
                @endforelse
            </div>

            @if(method_exists($motorcycles, 'links'))
                <div class="mt-6">{{ $motorcycles->links() }}</div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var cards = document.querySelectorAll('[data-hover-card]');
            if (!cards.length) return;

            function placePanel(card, panel) {
                panel.style.left = '0';
                panel.style.right = 'auto';
                panel.style.top = '100%';
                panel.style.bottom = 'auto';
                panel.style.marginTop = '0.5rem';
                panel.style.marginBottom = '0';

                var prevVis = panel.style.visibility;
                panel.style.visibility = 'hidden';
                var cardRect = card.getBoundingClientRect();
                var panelRect = panel.getBoundingClientRect();
                panel.style.visibility = prevVis;

                var viewportW = window.innerWidth;
                var viewportH = window.innerHeight;
                var gap = 8;

                var panelW = panelRect.width;
                var panelH = panelRect.height;

                var canOpenDown = cardRect.bottom + gap + panelH <= viewportH - gap;
                var canOpenUp = cardRect.top - gap - panelH >= gap;

                if (!canOpenDown && canOpenUp) {
                    panel.style.top = 'auto';
                    panel.style.bottom = '100%';
                    panel.style.marginTop = '0';
                    panel.style.marginBottom = '0.5rem';
                }

                if (cardRect.left + panelW > viewportW - gap) {
                    panel.style.left = 'auto';
                    panel.style.right = '0';
                }
            }

            cards.forEach(function (card) {
                var panel = card.querySelector('[data-hover-panel]');
                if (!panel) return;

                var recalc = function () {
                    placePanel(card, panel);
                };

                card.addEventListener('mouseenter', recalc);
                card.addEventListener('focusin', recalc);
                window.addEventListener('resize', recalc, { passive: true });
                window.addEventListener('scroll', recalc, { passive: true });
            });
        })();
    </script>
@endpush
