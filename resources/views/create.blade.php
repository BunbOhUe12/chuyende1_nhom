@extends('layouts.admin')
@section('page-title', 'Thêm xe mới')

@section('content')
    <div class="flex items-end justify-between">
        <h1 class="text-xl font-semibold">Thêm xe</h1>
        <a href="{{ route('admin.motorcycles.index') }}" class="text-sm text-slate-600 hover:underline">Quay lại</a>
    </div>

    <form action="{{ route('admin.motorcycles.store') }}" method="post" class="mt-6 rounded-2xl border bg-white p-6 space-y-4">
        @csrf

        @include('admin.motorcycles.partials.form', ['motorcycle' => null])

        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Lưu</button>
    </form>
@endsection

