@extends('layouts.admin')
@section('page-title', 'Chỉnh sửa xe')

@section('content')
    <div class="flex items-end justify-between">
        <h1 class="text-xl font-semibold">Sửa xe</h1>
        <a href="{{ route('admin.motorcycles.index') }}" class="text-sm text-slate-600 hover:underline">Quay lại</a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <form action="{{ route('admin.motorcycles.update', $motorcycle) }}" method="post" class="lg:col-span-2 rounded-2xl border bg-white p-6 space-y-4">
            @csrf
            @method('PUT')

            @include('admin.motorcycles.partials.form', ['motorcycle' => $motorcycle])

            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Lưu thay đổi</button>
        </form>

        <aside class="rounded-2xl border bg-white p-6">
            <h2 class="font-semibold">Thao tác</h2>
            <div class="mt-4 space-y-3">
                <a href="{{ route('motorcycles.show', $motorcycle) }}" class="inline-flex w-full justify-center rounded-lg border px-4 py-2 text-sm font-medium hover:bg-slate-50">
                    Xem ngoài shop
                </a>
                <a href="{{ route('admin.motorcycles.images.index', $motorcycle) }}" class="inline-flex w-full justify-center rounded-lg border px-4 py-2 text-sm font-medium hover:bg-slate-50">
                    Quản lý ảnh
                </a>
                <form action="{{ route('admin.motorcycles.destroy', $motorcycle) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="w-full rounded-lg border border-rose-300 bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700 hover:bg-rose-100">
                        Xoá
                    </button>
                </form>
            </div>
        </aside>
    </div>
@endsection

