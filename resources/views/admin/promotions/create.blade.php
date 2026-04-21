@extends('layouts.admin')
@section('page-title', 'Thêm ưu đãi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Thêm chương trình ưu đãi</h1>
        </div>
        <a href="{{ route('admin.promotions.index') }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium hover:bg-slate-50">
            ← Danh sách ưu đãi
        </a>
    </div>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.promotions.store') }}" method="post" enctype="multipart/form-data"
          class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf

        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Thông tin cơ bản</h2>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Tên chương trình <span class="text-rose-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" required
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none"
                           placeholder="VD: Khuyến mãi tháng 4 – Giảm 10%" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Slug (URL)</label>
                    <input name="slug" value="{{ old('slug') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none font-mono"
                           placeholder="tu-dong-tao-neu-de-trong" />
                    <p class="mt-1 text-xs text-slate-400">Để trống để tự động tạo từ tên.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Mô tả chi tiết</label>
                    <textarea name="description" rows="5"
                              class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none"
                              placeholder="Nội dung chi tiết về chương trình ưu đãi...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Hình ảnh</h2>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Ảnh banner (hiển thị trang chi tiết)</label>
                    <input type="file" name="banner_image" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-600" />
                    <div class="mt-1.5 flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kích thước khuyến nghị: <strong>1200 × 400 px</strong> (tỉ lệ 3:1) · Tối đa 4MB · JPG/PNG/WEBP</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Ảnh thumbnail (hiển thị danh sách &amp; trang chủ)</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-600" />
                    <div class="mt-1.5 flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kích thước khuyến nghị: <strong>800 × 450 px</strong> (tỉ lệ 16:9) · Tối đa 2MB · JPG/PNG/WEBP</span>
                    </div>
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
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Số tiền cố định (đ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Giá trị giảm <span class="text-rose-500">*</span></label>
                    <input type="number" name="value" value="{{ old('value', 0) }}" min="0" step="0.01" required
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Mã ưu đãi (coupon)</label>
                    <input name="code" value="{{ old('code') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none font-mono uppercase"
                           placeholder="HONDA10" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-slate-900">Thời gian &amp; Trạng thái</h2>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Ngày bắt đầu</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Ngày kết thúc</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none" />
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="rounded border-slate-300 text-brand" />
                    <span class="text-sm font-medium text-slate-700">Kích hoạt ngay</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-brand py-3 text-sm font-bold text-white hover:bg-brand-dark">
                Tạo chương trình ưu đãi
            </button>
        </div>
    </form>
@endsection
