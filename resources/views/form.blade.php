@php
    $inputCls = 'mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100';
    $labelCls = 'block text-sm font-medium text-slate-700';
    $errorCls = 'mt-1 text-xs text-rose-600';

    // Danh sách thông số kỹ thuật mặc định
    $specKeys = [
        'kieu_dong_co'     => 'Kiểu động cơ',
        'dung_tich'        => 'Dung tích xy lanh (cc)',
        'cong_suat_toi_da' => 'Công suất tối đa',
        'momen_xoan'       => 'Mô-men xoắn tối đa',
        'hop_so'           => 'Hộp số',
        'trong_luong_xe'   => 'Trọng lượng xe (kg)',
        'chieu_dai'        => 'Chiều dài tổng thể (mm)',
        'chieu_rong'       => 'Chiều rộng tổng thể (mm)',
        'chieu_cao_yen'    => 'Chiều cao yên (mm)',
        'dung_tich_binh'   => 'Dung tích bình xăng (lít)',
        'he_thong_phanhT'  => 'Hệ thống phanh trước',
        'he_thong_phanhS'  => 'Hệ thống phanh sau',
        'loai_lop_banh'    => 'Loại lốp bánh',
    ];
    $existingSpecs = old('technical_specs', $motorcycle?->technical_specs ?? []);
@endphp

<div class="space-y-6">

    {{-- ── Thông tin cơ bản ─────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-500">Thông tin cơ bản</h3>
        <div class="grid gap-4 sm:grid-cols-2 text-sm">

            <div class="sm:col-span-2">
                <label class="{{ $labelCls }}" for="name">Tên sản phẩm <span class="text-rose-500">*</span></label>
                <input id="name" name="name" value="{{ old('name', $motorcycle?->name) }}" placeholder="VD: Honda Wave Alpha 110" class="{{ $inputCls }} @error('name') border-rose-400 @enderror">
                @error('name')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $labelCls }}" for="brand_id">Hãng xe <span class="text-rose-500">*</span></label>
                <select id="brand_id" name="brand_id" class="{{ $inputCls }} @error('brand_id') border-rose-400 @enderror">
                    <option value="">-- Chọn hãng --</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" @selected(old('brand_id', $motorcycle?->brand_id) == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
                @error('brand_id')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $labelCls }}" for="category_id">Danh mục <span class="text-rose-500">*</span></label>
                <select id="category_id" name="category_id" class="{{ $inputCls }} @error('category_id') border-rose-400 @enderror">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $motorcycle?->category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $labelCls }}" for="supplier_id">Nhà cung cấp</label>
                <select id="supplier_id" name="supplier_id" class="{{ $inputCls }}">
                    <option value="">-- Không chọn --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(old('supplier_id', $motorcycle?->supplier_id) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $labelCls }}" for="promotion_id">Chương trình khuyến mãi</label>
                <select id="promotion_id" name="promotion_id" class="{{ $inputCls }}">
                    <option value="">-- Không có --</option>
                    @foreach($promotions as $p)
                        <option value="{{ $p->id }}" @selected(old('promotion_id', $motorcycle?->promotion_id) == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="{{ $labelCls }}" for="price">Giá bán (₫) <span class="text-rose-500">*</span></label>
                <input id="price" name="price" type="number" step="1000" min="0" value="{{ old('price', $motorcycle?->price) }}" placeholder="VD: 28500000" class="{{ $inputCls }} @error('price') border-rose-400 @enderror">
                @error('price')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $labelCls }}" for="stock">Số lượng tồn kho <span class="text-rose-500">*</span></label>
                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $motorcycle?->stock ?? 0) }}" class="{{ $inputCls }} @error('stock') border-rose-400 @enderror">
                @error('stock')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2 flex items-center gap-3 pt-1">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 accent-violet-600"
                    @checked((bool) old('is_active', $motorcycle?->is_active ?? true)) >
                <label for="is_active" class="text-sm font-medium text-slate-700">Đang bán (hiển thị trên website)</label>
            </div>
        </div>
    </div>

    {{-- ── Mô tả sản phẩm ───────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-500">Mô tả sản phẩm</h3>
        <div>
            <label class="{{ $labelCls }}" for="description">Mô tả chi tiết</label>
            <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả đặc điểm, ưu điểm của sản phẩm..." class="{{ $inputCls }}">{{ old('description', $motorcycle?->description) }}</textarea>
            @error('description')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- ── Nhận dạng xe ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-500">Nhận dạng xe</h3>
        <div class="grid gap-4 sm:grid-cols-2 text-sm">

            <div>
                <label class="{{ $labelCls }}" for="frame_number">Số khung (VIN) <span class="text-rose-500">*</span></label>
                <input id="frame_number" name="frame_number" value="{{ old('frame_number', $motorcycle?->frame_number) }}" placeholder="VD: RLHSC110XPY123456" class="{{ $inputCls }} font-mono @error('frame_number') border-rose-400 @enderror">
                @error('frame_number')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-400">Số khung phải duy nhất, tra cứu trên cà vẹt xe.</p>
            </div>

            <div>
                <label class="{{ $labelCls }}" for="engine_number">Số máy <span class="text-rose-500">*</span></label>
                <input id="engine_number" name="engine_number" value="{{ old('engine_number', $motorcycle?->engine_number) }}" placeholder="VD: SC110E123456" class="{{ $inputCls }} font-mono @error('engine_number') border-rose-400 @enderror">
                @error('engine_number')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-400">Số máy ghi trên thân động cơ.</p>
            </div>

            <div>
                <label class="{{ $labelCls }}" for="color">Màu xe</label>
                <input id="color" name="color" value="{{ old('color', $motorcycle?->color) }}" placeholder="VD: Đỏ đen, Trắng ngọc trai, Xanh dương..." class="{{ $inputCls }}">
                @error('color')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $labelCls }}" for="engine_cc">Dung tích xy lanh (cc)</label>
                <input id="engine_cc" name="engine_cc" type="number" min="0" max="5000" value="{{ old('engine_cc', $motorcycle?->engine_cc) }}" placeholder="VD: 110" class="{{ $inputCls }}">
                @error('engine_cc')<p class="{{ $errorCls }}">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── Thông số kỹ thuật ────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500">Thông số kỹ thuật</h3>
            <button type="button" id="btn-add-spec"
                class="inline-flex items-center gap-1 rounded-lg border border-dashed border-violet-300 px-3 py-1.5 text-xs font-medium text-violet-600 hover:bg-violet-50 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm thông số
            </button>
        </div>

        <div id="spec-rows" class="space-y-2">
            @forelse($specKeys as $key => $label)
            @php $val = $existingSpecs[$key] ?? ''; @endphp
            @if($val || true)
            <div class="flex items-center gap-2 spec-row">
                <span class="w-56 shrink-0 text-xs font-medium text-slate-600">{{ $label }}</span>
                <input type="text" name="technical_specs[{{ $key }}]" value="{{ $val }}"
                    placeholder="Nhập giá trị..."
                    class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-100">
            </div>
            @endif
            @endforeach

            {{-- Custom specs từ dữ liệu đã lưu (ngoài danh sách mặc định) --}}
            @foreach($existingSpecs as $k => $v)
                @if(!array_key_exists($k, $specKeys) && $k !== '' && $v !== '')
                <div class="flex items-center gap-2 spec-row spec-custom">
                    <input type="text" name="spec_custom_keys[]" value="{{ $k }}" placeholder="Tên thông số"
                        class="w-56 shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 placeholder-slate-400 focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-100">
                    <input type="text" name="spec_custom_vals[]" value="{{ $v }}" placeholder="Giá trị"
                        class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-100">
                    <button type="button" onclick="this.closest('.spec-row').remove()" class="shrink-0 text-rose-400 hover:text-rose-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Template for new spec row --}}
        <template id="spec-row-tpl">
            <div class="flex items-center gap-2 spec-row spec-custom">
                <input type="text" name="spec_custom_keys[]" placeholder="Tên thông số (VD: Hệ thống điện)"
                    class="w-56 shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 placeholder-slate-400 focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-100">
                <input type="text" name="spec_custom_vals[]" placeholder="Giá trị"
                    class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-100">
                <button type="button" onclick="this.closest('.spec-row').remove()" class="shrink-0 text-rose-400 hover:text-rose-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btn-add-spec')?.addEventListener('click', () => {
    const tpl = document.getElementById('spec-row-tpl');
    const clone = tpl.content.cloneNode(true);
    document.getElementById('spec-rows').appendChild(clone);
});
</script>
@endpush
