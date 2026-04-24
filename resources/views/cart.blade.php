@extends('layouts.app')

@section('content')
    @php
        use App\Support\MotoImage;
    @endphp

    <div class="mx-auto max-w-6xl pb-32 pt-2 sm:pt-4">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Giỏ hàng</h1>
            @if($items->isNotEmpty())
                <form action="{{ route('cart.clear') }}" method="post" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-2 hover:text-brand">Xóa giỏ hàng</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        @forelse($items as $it)
            @once
                {{-- Bố cục bảng ngang kiểu sàn TMĐT: 1 cửa hàng — toàn bộ sản phẩm --}}
                <div class="mt-6 overflow-hidden rounded border border-slate-200/90 bg-white shadow-sm">
                    {{-- Dòng cửa hàng + chọn tất cả --}}
                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-white px-3 py-3 sm:px-5">
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" id="cart-select-all" checked class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand/40" />
                            <span>Chọn tất cả</span>
                        </label>
                        <span class="text-slate-300">|</span>
                        <span class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Cửa hàng') }}</span>
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide text-slate-600">Uy tín</span>
                    </div>

                    {{-- Header cột (desktop) --}}
                    <div class="hidden border-b border-slate-100 bg-slate-50/90 text-xs font-medium uppercase tracking-wide text-slate-500 md:grid md:grid-cols-[2.75rem_minmax(0,1fr)_112px_168px_120px_88px] md:items-center md:gap-3 md:px-5 md:py-2.5 lg:grid-cols-[2.75rem_minmax(0,1fr)_120px_180px_128px_96px]">
                        <div class="sr-only">Chọn</div>
                        <div class="pl-0">Sản phẩm</div>
                        <div class="text-center">Đơn giá</div>
                        <div class="text-center">Số lượng</div>
                        <div class="text-right">Số tiền</div>
                        <div class="text-center">Thao tác</div>
                    </div>
            @endonce

            @php
                $m = $it['motorcycle'] ?? null;
                $mid = (int) $it['motorcycle_id'];
                $stock = $m ? (int) $m->stock : 0;
                $imgUrl = $m ? MotoImage::cardUrl($m) : MotoImage::fallbackUrl();
            @endphp

            <div
                class="cart-row relative border-b border-slate-100 last:border-b-0 md:grid md:grid-cols-[2.75rem_minmax(0,1fr)_112px_168px_120px_88px] md:items-stretch md:gap-3 md:px-5 md:py-4 lg:grid-cols-[2.75rem_minmax(0,1fr)_120px_180px_128px_96px]"
            >
                {{-- Checkbox --}}
                <div class="absolute left-0 top-0 flex items-start pl-4 pt-4 md:relative md:flex md:items-center md:justify-center md:pl-0 md:pt-0">
                    <input
                        type="checkbox"
                        name="cart_select[]"
                        value="{{ $mid }}"
                        class="cart-item-cb h-4 w-4 shrink-0 rounded border-slate-300 text-brand focus:ring-brand/40"
                        checked
                        data-line-total="{{ (float) $it['line_total'] }}"
                        data-qty="{{ (int) $it['quantity'] }}"
                    />
                </div>
                {{-- Cột sản phẩm (ảnh + tên + phân loại) --}}
                <div class="relative flex min-w-0 gap-3 border-t border-slate-50 p-4 pl-11 md:border-t-0 md:pl-0 md:items-center md:gap-4 md:py-0 md:pr-3">
                    <a href="{{ route('motorcycles.show', $it['slug']) }}" class="relative h-24 w-24 shrink-0 overflow-hidden rounded border border-slate-100 bg-slate-50 sm:h-[100px] sm:w-[100px]">
                        <img src="{{ $imgUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" onerror="this.onerror=null;this.src={{ json_encode(MotoImage::fallbackUrl()) }}" />
                    </a>
                    <div class="min-w-0 flex-1 space-y-2">
                        <a href="{{ route('motorcycles.show', $it['slug']) }}" class="line-clamp-2 text-sm font-medium leading-snug text-slate-900 hover:text-brand hover:underline md:pr-2">
                            {{ $it['name'] }}
                        </a>
                        @if(!empty($it['color_options']))
                            <form action="{{ route('cart.update', $mid) }}" method="post" class="max-w-xs">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $it['quantity'] }}" />
                                <label class="sr-only" for="color-{{ $mid }}">Phân loại hàng</label>
                                <select
                                    name="selected_color"
                                    id="color-{{ $mid }}"
                                    onchange="this.form.submit()"
                                    class="w-full rounded border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-700 focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand/30"
                                >
                                    @foreach($it['color_options'] as $color)
                                        <option value="{{ $color }}" @selected(($it['selected_color'] ?? '') === $color)>Phân loại: {{ $color }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif(!empty($it['selected_color']))
                            <p class="text-xs text-slate-500">Phân loại: <span class="font-medium text-slate-700">{{ $it['selected_color'] }}</span></p>
                        @endif
                        <p class="text-xs text-slate-500 md:hidden">Đơn giá: <span class="font-semibold tabular-nums text-slate-900">{{ number_format((float) $it['price']) }}₫</span></p>
                    </div>
                </div>

                {{-- Đơn giá --}}
                <div class="hidden items-center justify-center tabular-nums text-sm text-slate-900 md:flex">
                    {{ number_format((float) $it['price'], 0, ',', '.') }}₫
                </div>

                {{-- Số lượng --}}
                <div class="flex flex-col items-stretch gap-1 px-4 pb-3 md:flex md:items-center md:justify-center md:p-0">
                    <form action="{{ route('cart.update', $mid) }}" method="post" class="inline-flex items-center justify-center gap-0 overflow-hidden rounded border border-slate-200 bg-white" data-cart-qty-form data-max-stock="{{ max(0, $stock) }}">
                        @csrf
                        @method('PATCH')
                        @if(!empty($it['color_options']))
                            <input type="hidden" name="selected_color" value="{{ $it['selected_color'] ?? $it['color_options'][0] }}" />
                        @endif
                        <button type="button" data-qty-step="-1" class="flex h-9 w-9 items-center justify-center text-lg text-slate-600 hover:bg-slate-50 disabled:opacity-40" aria-label="Giảm">−</button>
                        <input name="quantity" type="number" min="0" max="{{ max(0, $stock) }}" value="{{ $it['quantity'] }}" class="h-9 w-12 border-x border-slate-200 bg-white text-center text-sm font-semibold tabular-nums text-slate-900 focus:outline-none" />
                        <button type="button" data-qty-step="1" class="flex h-9 w-9 items-center justify-center text-lg text-slate-600 hover:bg-slate-50 disabled:opacity-40" aria-label="Tăng">+</button>
                    </form>
                    @if($m)
                        <span class="text-center text-[11px] text-slate-500 md:text-xs">Còn {{ $stock }} sản phẩm</span>
                    @endif
                </div>

                {{-- Thành tiền dòng --}}
                <div class="hidden items-center justify-end tabular-nums text-sm font-semibold text-brand md:flex">
                    {{ number_format((float) $it['line_total'], 0, ',', '.') }}₫
                </div>

                {{-- Thao tác --}}
                <div class="flex flex-col items-end gap-2 px-4 pb-4 md:flex md:items-center md:justify-center md:p-0">
                    <form action="{{ route('cart.remove', $mid) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-medium text-slate-600 hover:text-rose-600">Xóa</button>
                    </form>
                    <a href="{{ route('shop', ['q' => $it['name']]) }}" class="text-center text-xs text-brand hover:underline">Tìm sản phẩm tương tự</a>
                </div>

                {{-- Mobile: nhắc lại thành tiền --}}
                <div class="flex items-center justify-between border-t border-slate-50 px-4 py-2 text-sm max-md:flex md:hidden">
                    <span class="text-slate-500">Thành tiền (dòng)</span>
                    <span class="line-total-mob font-bold tabular-nums text-brand">{{ number_format((float) $it['line_total'], 0, ',', '.') }}₫</span>
                </div>
            </div>

            @if($loop->last)
                </div>
            @endif
        @empty
            <div class="mt-8 rounded border border-slate-200 bg-white px-6 py-14 text-center">
                <p class="text-slate-600">Giỏ hàng trống.</p>
                <a href="{{ route('shop') }}" class="mt-4 inline-flex rounded-lg bg-brand px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">Tiếp tục mua sắm</a>
            </div>
        @endforelse

        @if($items->isNotEmpty())
            {{-- Thanh mua hàng cố định dưới đáy (kiểu Shopee) --}}
            <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white/95 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur supports-[backdrop-filter]:bg-white/90">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-end gap-4 px-4 py-3 sm:flex-nowrap sm:justify-between sm:py-3.5">
                    <div class="order-2 flex w-full flex-1 flex-wrap items-center gap-2 sm:order-1 sm:w-auto sm:gap-4">
                        <form action="{{ route('cart.clear') }}" method="post" class="hidden sm:block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-slate-600 hover:text-brand">Xóa toàn bộ</button>
                        </form>
                    </div>
                    <div class="order-1 flex w-full flex-wrap items-center justify-end gap-4 sm:order-2 sm:w-auto">
                        <div class="text-right">
                            <div id="cart-footer-label" class="text-xs text-slate-500">
                                Tổng thanh toán<span id="cart-footer-qty-wrap"> (<span id="cart-footer-qty">{{ $summary['quantity'] }}</span> sản phẩm)</span>:
                            </div>
                            <div id="cart-footer-total" class="text-lg font-bold tabular-nums text-brand sm:text-xl">
                                {{ number_format((float) $summary['subtotal'], 0, ',', '.') }}₫
                            </div>
                            <p id="cart-footer-hint" class="mt-1 hidden text-[11px] text-amber-700">Chọn ít nhất một sản phẩm để thanh toán.</p>
                        </div>
                        <button type="button" id="cart-checkout-btn" class="inline-flex min-w-[140px] items-center justify-center rounded bg-brand px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow-sm hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-50">
                            Mua hàng
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @if($items->isNotEmpty())
        <script>
            (function () {
                var checkoutBase = @json(route('checkout.show'));

                function formatVnd(n) {
                    try {
                        return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + '₫';
                    } catch (e) {
                        return Math.round(n) + '₫';
                    }
                }

                function selectedTotals() {
                    var total = 0;
                    var qty = 0;
                    document.querySelectorAll('.cart-item-cb:checked').forEach(function (cb) {
                        total += parseFloat(cb.getAttribute('data-line-total') || '0', 10) || 0;
                        qty += parseInt(cb.getAttribute('data-qty') || '0', 10) || 0;
                    });
                    return { total: total, qty: qty };
                }

                function refreshCartFooter() {
                    var elQty = document.getElementById('cart-footer-qty');
                    var elTotal = document.getElementById('cart-footer-total');
                    var btn = document.getElementById('cart-checkout-btn');
                    var hint = document.getElementById('cart-footer-hint');
                    var t = selectedTotals();
                    if (elQty) elQty.textContent = String(t.qty);
                    if (elTotal) elTotal.textContent = formatVnd(t.total);
                    var ok = t.qty > 0;
                    if (btn) btn.disabled = !ok;
                    if (hint) hint.classList.toggle('hidden', ok);
                }

                document.querySelectorAll('.cart-item-cb').forEach(function (cb) {
                    cb.addEventListener('change', function () {
                        var all = document.querySelectorAll('.cart-item-cb');
                        var allOn = Array.prototype.every.call(all, function (x) { return x.checked; });
                        var none = Array.prototype.every.call(all, function (x) { return !x.checked; });
                        var master = document.getElementById('cart-select-all');
                        if (master) {
                            master.checked = allOn && all.length > 0;
                            master.indeterminate = !allOn && !none && all.length > 0;
                        }
                        refreshCartFooter();
                    });
                });

                var master = document.getElementById('cart-select-all');
                if (master) {
                    master.addEventListener('change', function () {
                        var on = master.checked;
                        document.querySelectorAll('.cart-item-cb').forEach(function (cb) {
                            cb.checked = on;
                        });
                        master.indeterminate = false;
                        refreshCartFooter();
                    });
                }

                var goBtn = document.getElementById('cart-checkout-btn');
                if (goBtn) {
                    goBtn.addEventListener('click', function () {
                        var ids = Array.prototype.map.call(document.querySelectorAll('.cart-item-cb:checked'), function (cb) {
                            return cb.value;
                        });
                        if (!ids.length) {
                            return;
                        }
                        var qs = ids.map(function (id) { return 'selected[]=' + encodeURIComponent(id); }).join('&');
                        window.location.href = checkoutBase + (checkoutBase.indexOf('?') >= 0 ? '&' : '?') + qs;
                    });
                }

                document.querySelectorAll('[data-cart-qty-form]').forEach(function (form) {
                    var max = parseInt(form.getAttribute('data-max-stock'), 10);
                    if (isNaN(max)) max = 99;
                    var input = form.querySelector('input[name="quantity"]');
                    if (!input) return;

                    form.querySelectorAll('[data-qty-step]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var step = parseInt(btn.getAttribute('data-qty-step'), 10);
                            if (!step) return;
                            var v = parseInt(input.value, 10) || 0;
                            v += step;
                            if (v < 0) v = 0;
                            if (v > max) v = max;
                            input.value = String(v);
                            form.submit();
                        });
                    });
                });

                refreshCartFooter();
            })();
        </script>
    @endif
@endpush
