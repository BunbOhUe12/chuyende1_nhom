@extends('layouts.admin')
@section('page-title', 'Sửa ưu đãi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Sửa chương trình ưu đãi</h1>
            <p class="mt-0.5 text-sm text-slate-500">{{ $promotion->name }}</p>
        </div>
        <a href="{{ route('admin.promotions.index') }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium hover:bg-slate-50">
            ← Danh sách ưu đãi
        </a>
    </div>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.promotions.update', $promotion) }}" method="post" enctype="multipart/form-data"
          class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf
        @method('PUT')

        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Thông tin cơ bản</h2>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Tên chương trình <span class="text-rose-500">*</span></label>
                    <input name="name" value="{{ old('name', $promotion->name) }}" required
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Slug (URL)</label>
                    <input name="slug" value="{{ old('slug', $promotion->slug) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none font-mono" />
                    <p class="mt-1 text-xs text-slate-400">URL: <span class="text-brand">{{ url('/uu-dai/' . $promotion->slug) }}</span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Mô tả chi tiết</label>
                    <textarea name="description" rows="6"
                              class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none">{{ old('description', $promotion->description) }}</textarea>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Hình ảnh</h2>

                @if ($promotion->banner_image)
                    <div>
                        <p class="text-xs text-slate-500 mb-2">Banner hiện tại:</p>
                        <img src="{{ Storage::url($promotion->banner_image) }}" alt="banner"
                             class="h-32 rounded-xl object-cover w-full" />
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700">{{ $promotion->banner_image ? 'Thay ảnh banner' : 'Ảnh banner' }}</label>
                    <input type="file" name="banner_image" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-600" />
                    <p class="mt-1 text-xs text-blue-600">Kích thước chuẩn: <strong>1200 × 400 px</strong> (tỉ lệ 3:1) · tối đa 4MB</p>
                </div>

                @if ($promotion->thumbnail)
                    <div>
                        <p class="text-xs text-slate-500 mb-2">Thumbnail hiện tại:</p>
                        <img src="{{ Storage::url($promotion->thumbnail) }}" alt="thumbnail"
                             class="h-20 w-32 rounded-xl object-cover" />
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700">{{ $promotion->thumbnail ? 'Thay thumbnail' : 'Ảnh thumbnail' }}</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-600" />
                    <p class="mt-1 text-xs text-blue-600">Kích thước chuẩn: <strong>800 × 450 px</strong> (tỉ lệ 16:9) · tối đa 2MB</p>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Thiết lập giảm giá</h2>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Loại giảm <span class="text-rose-500">*</span></label>
                    <select name="type" required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none">
                        <option value="percent" {{ old('type', $promotion->type) === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('type', $promotion->type) === 'fixed' ? 'selected' : '' }}>Số tiền cố định (đ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Giá trị giảm <span class="text-rose-500">*</span></label>
                    <input type="number" name="value" value="{{ old('value', $promotion->value) }}" min="0" step="0.01" required
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Mã ưu đãi (coupon)</label>
                    <input name="code" value="{{ old('code', $promotion->code) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none font-mono uppercase"
                           placeholder="HONDA10" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Thời gian &amp; Trạng thái</h2>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Ngày bắt đầu</label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d\TH:i')) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Ngày kết thúc</label>
                    <input type="datetime-local" name="ends_at"
                           value="{{ old('ends_at', $promotion->ends_at?->format('Y-m-d\TH:i')) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-brand" />
                    <span class="text-sm font-medium text-slate-700">Kích hoạt</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-brand py-3 text-sm font-bold text-white hover:bg-brand-dark">
                Lưu thay đổi
            </button>
        </div>
    </form>

    {{-- Form xóa phải đặt NGOÀI form cập nhật để tránh lồng <form> --}}
    <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="post"
          onsubmit="return confirm('Xóa chương trình ưu đãi này?')"
          class="mt-3 lg:col-start-3">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="w-full rounded-xl border border-rose-200 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50">
            Xóa ưu đãi
        </button>
    </form>
@endsection
