@extends('layouts.app')

@section('content')
    @php
        $specLabels = [
            'kieu_dong_co' => 'Kiểu động cơ',
            'dung_tich' => 'Dung tích xy lanh',
            'cong_suat_toi_da' => 'Công suất tối đa',
            'momen_xoan' => 'Mô-men xoắn tối đa',
            'hop_so' => 'Hộp số',
            'trong_luong_xe' => 'Trọng lượng xe',
            'chieu_dai' => 'Chiều dài tổng thể',
            'chieu_rong' => 'Chiều rộng tổng thể',
            'chieu_cao_yen' => 'Chiều cao yên',
            'dung_tich_binh' => 'Dung tích bình xăng',
            'he_thong_phanhT' => 'Hệ thống phanh trước',
            'he_thong_phanhS' => 'Hệ thống phanh sau',
            'loai_lop_banh' => 'Loại lốp bánh',
        ];
        $technicalSpecs = collect($motorcycle->technical_specs ?? [])
            ->filter(fn ($value) => filled($value));
        $engineDisplay = $motorcycle->engine_cc
            ? $motorcycle->engine_cc.' cc'
            : ($technicalSpecs->get('dung_tich') ?: '—');
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-2xl border bg-white p-6">
                <div class="text-sm text-slate-500">
                    {{ $motorcycle->brand->name ?? '' }} · {{ $motorcycle->category->name ?? '' }}
                </div>
                <h1 class="mt-1 text-2xl font-semibold">{{ $motorcycle->name }}</h1>

                <div class="mt-5">
                    <h2 class="font-semibold">Hình ảnh</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($motorcycle->approvedImages as $img)
                            <div class="aspect-[4/3] overflow-hidden rounded-xl border bg-slate-100">
                                <img
                                    src="{{ \App\Support\MotoImage::storageUrl($img->path) }}"
                                    class="h-full w-full object-cover"
                                    alt=""
                                    onerror="this.onerror=null;this.src={{ json_encode(\App\Support\MotoImage::fallbackUrl()) }}"
                                />
                            </div>
                        @empty
                            <div class="aspect-[4/3] overflow-hidden rounded-xl border bg-slate-100 sm:col-span-2 lg:col-span-3">
                                <img
                                    src="{{ \App\Support\MotoImage::cardUrl($motorcycle) }}"
                                    class="h-full w-full object-cover"
                                    alt="{{ $motorcycle->name }}"
                                    onerror="this.onerror=null;this.src={{ json_encode(\App\Support\MotoImage::fallbackUrl()) }}"
                                />
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2 text-sm">
                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-slate-500">Giá</div>
                        <div class="mt-1 text-lg font-semibold">{{ number_format((float)$motorcycle->price) }}₫</div>
                    </div>
                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-slate-500">Tồn kho</div>
                        <div class="mt-1 text-lg font-semibold">{{ $motorcycle->stock }}</div>
                    </div>
                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-slate-500">Dung tích</div>
                        <div class="mt-1 text-lg font-semibold">{{ $engineDisplay }}</div>
                    </div>
                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-slate-500">Màu</div>
                        <div class="mt-1 text-lg font-semibold">{{ $motorcycle->color ?: '—' }}</div>
                    </div>
                </div>

                @if($technicalSpecs->isNotEmpty())
                    <div class="mt-6">
                        <h2 class="font-semibold">Thông số kỹ thuật</h2>
                        <div class="mt-3 divide-y rounded-xl border bg-slate-50">
                            @foreach($technicalSpecs as $specName => $specValue)
                                <div class="grid gap-2 px-4 py-3 text-sm sm:grid-cols-2">
                                    <div class="font-medium text-slate-500">{{ $specLabels[$specName] ?? str_replace('_', ' ', ucfirst($specName)) }}</div>
                                    <div class="text-slate-800 sm:text-right">{{ $specValue }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($motorcycle->description)
                    <div class="mt-6">
                        <h2 class="font-semibold">Mô tả</h2>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $motorcycle->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Đánh giá sản phẩm --}}
            <div class="mt-6 rounded-2xl border bg-white p-6">
                @php
                    $approvedReviews = $motorcycle->reviews->where('is_approved', true);
                    $avgRating = $approvedReviews->avg('rating');
                    $totalReviews = $approvedReviews->count();
                @endphp

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold">Đánh giá sản phẩm</h2>
                        @if($totalReviews > 0)
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($avgRating, 1) }}</span>
                                <div>
                                    <div class="flex gap-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                            <svg class="h-5 w-5 {{ $s <= round($avgRating) ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $totalReviews }} đánh giá</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Danh sách đánh giá --}}
                <div class="mt-5 space-y-4">
                    @forelse($approvedReviews as $r)
                        <div class="rounded-xl border bg-slate-50 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="flex gap-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                            <svg class="h-4 w-4 {{ $s <= $r->rating ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <div class="mt-1 text-sm font-semibold text-slate-800">{{ $r->user?->name ?? 'Khách hàng' }}</div>
                                </div>
                                <div class="text-xs text-slate-400">{{ $r->created_at->format('d/m/Y') }}</div>
                            </div>
                            @if($r->comment)
                                <p class="mt-2 text-sm text-slate-700">{{ $r->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-200 py-6 text-center text-sm text-slate-500">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                    @endforelse
                </div>

                {{-- Form gửi đánh giá --}}
                <div class="mt-6 border-t pt-6">
                    <h3 class="font-semibold">Viết đánh giá của bạn</h3>
                    @auth
                        <form action="{{ route('reviews.store', $motorcycle) }}" method="post" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Số sao <span class="text-rose-500">*</span></label>
                                <div class="mt-2 flex gap-2" id="star-picker">
                                    @for($s = 1; $s <= 5; $s++)
                                        <button type="button" data-star="{{ $s }}"
                                            class="star-btn text-slate-200 transition hover:text-amber-400 focus:outline-none">
                                            <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}" required>
                                @error('rating')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Nhận xét</label>
                                <textarea name="comment" rows="3" maxlength="1000"
                                    placeholder="Chia sẻ trải nghiệm của bạn về xe này..."
                                    class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand">{{ old('comment') }}</textarea>
                                @error('comment')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="rounded-xl bg-brand px-6 py-2.5 text-sm font-bold text-white hover:bg-brand-dark">
                                Gửi đánh giá
                            </button>
                        </form>
                        @push('scripts')
                        <script>
                        (function () {
                            var btns = document.querySelectorAll('#star-picker .star-btn');
                            var input = document.getElementById('rating-input');
                            var selected = parseInt(input.value) || 0;

                            function paint(n) {
                                btns.forEach(function (b, i) {
                                    b.classList.toggle('text-amber-400', i < n);
                                    b.classList.toggle('text-slate-200', i >= n);
                                });
                            }
                            paint(selected);

                            btns.forEach(function (b) {
                                b.addEventListener('mouseenter', function () { paint(parseInt(b.dataset.star)); });
                                b.addEventListener('mouseleave', function () { paint(selected); });
                                b.addEventListener('click', function () {
                                    selected = parseInt(b.dataset.star);
                                    input.value = selected;
                                    paint(selected);
                                });
                            });
                        })();
                        </script>
                        @endpush
                    @else
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-5 text-center text-sm text-slate-600">
                            <a href="{{ route('login') }}" class="font-semibold text-brand hover:underline">Đăng nhập</a> để viết đánh giá.
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <aside class="space-y-4">
            {{-- Mua ngay --}}
            <div class="rounded-2xl border bg-white p-6">
                <h2 class="font-semibold">Mua ngay</h2>
                <form action="{{ route('cart.add', $motorcycle) }}" method="post" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm text-slate-600">Số lượng</label>
                        <input name="quantity" type="number" min="1" max="99" value="1" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"/>
                    </div>
                    <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white" @disabled($motorcycle->stock <= 0)>
                        Thêm vào giỏ hàng
                    </button>
                </form>
            </div>

            {{-- Đặt cọc giữ xe --}}
            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-6">
                <h2 class="font-semibold text-violet-900">Đặt cọc giữ xe</h2>
                <p class="mt-2 text-sm text-violet-700">
                    Đặt cọc <span class="font-bold">1.000.000₫</span> qua VNPAY để giữ xe trong <span class="font-semibold">7 ngày</span>.
                    Số tiền cọc sẽ được trừ vào giá mua khi bạn đến nhận xe.
                </p>

                @if($motorcycle->stock <= 0)
                    <div class="mt-4 rounded-lg bg-rose-100 px-4 py-3 text-sm font-medium text-rose-700">
                        Xe đã hết hàng, không thể đặt cọc.
                    </div>
                @elseif(auth()->check())
                    @php
                        $existingDeposit = \App\Models\Deposit::query()
                            ->where('user_id', auth()->id())
                            ->where('motorcycle_id', $motorcycle->id)
                            ->whereIn('status', ['pending', 'paid'])
                            ->latest()
                            ->first();
                    @endphp

                    @if($existingDeposit?->isPaid())
                        <div class="mt-4 rounded-lg bg-green-100 px-4 py-3 text-sm font-medium text-green-800">
                            ✓ Bạn đã đặt cọc xe này thành công.
                            @if($existingDeposit->expires_at)
                                Xe được giữ đến <strong>{{ $existingDeposit->expires_at->format('d/m/Y') }}</strong>.
                            @endif
                        </div>
                    @else
                        @if(session('error'))
                            <div class="mt-3 rounded-lg bg-rose-100 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
                        @endif
                        <form action="{{ route('deposits.store', $motorcycle) }}" method="post" class="mt-4">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-lg bg-violet-700 px-3 py-2.5 text-sm font-semibold text-white hover:bg-violet-800 active:scale-95 transition">
                                Đặt cọc 1.000.000₫ qua VNPAY
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}"
                        class="mt-4 flex w-full items-center justify-center rounded-lg bg-violet-700 px-3 py-2.5 text-sm font-semibold text-white hover:bg-violet-800 transition">
                        Đăng nhập để đặt cọc
                    </a>
                @endif
            </div>
        </aside>
    </div>
@endsection

