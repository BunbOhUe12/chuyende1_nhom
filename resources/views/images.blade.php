@extends('layouts.admin')
@section('page-title', 'Quản lý ảnh xe')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold">Quản lý ảnh sản phẩm</h1>
            <div class="mt-1 text-sm text-slate-600">{{ $motorcycle->name }}</div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.motorcycles.edit', $motorcycle) }}" class="rounded-lg border px-4 py-2 text-sm font-medium hover:bg-slate-50">
                Quay lại
            </a>
            <a href="{{ route('motorcycles.show', $motorcycle) }}" class="rounded-lg border px-4 py-2 text-sm font-medium hover:bg-slate-50">
                Xem ngoài shop
            </a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border bg-white p-6">
            <h2 class="font-semibold">Upload ảnh (Admin)</h2>
            <form action="{{ route('admin.motorcycles.images.store', $motorcycle) }}" method="post" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"/>
                @error('image')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror
                <button class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Upload</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="flex items-end justify-between">
                <h2 class="font-semibold">Danh sách ảnh</h2>
                <div class="text-sm text-slate-600">{{ $motorcycle->images->count() }} ảnh</div>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($motorcycle->images as $img)
                    <div class="overflow-hidden rounded-xl border bg-white">
                        <div class="aspect-[4/3] bg-slate-100">
                            <img src="{{ asset('storage/'.$img->path) }}" class="h-full w-full object-cover" alt="image"/>
                        </div>
                        <div class="p-3 text-xs text-slate-600">
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-full {{ $img->is_approved ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2 py-1">
                                    {{ $img->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                                <span>{{ $img->created_at->format('d/m/Y') }}</span>
                            </div>

                            <div class="mt-3 flex gap-2">
                                @if(! $img->is_approved)
                                    <form action="{{ route('admin.motorcycle-images.approve', $img) }}" method="post" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button class="w-full rounded-lg border px-3 py-2 text-xs font-medium hover:bg-slate-50">Duyệt</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.motorcycle-images.destroy', $img) }}" method="post" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-100">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border bg-white p-6 text-sm text-slate-600">
                        Chưa có ảnh nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

