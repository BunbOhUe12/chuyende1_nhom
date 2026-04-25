@extends('layouts.admin')
@section('page-title', 'Kho xe & sản phẩm')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold">Kho xe &amp; sản phẩm</h1>
            @if(auth()->user()->isAdmin() || auth()->user()->isKeToan())
            <p class="mt-1 text-sm text-slate-500">
                Báo cáo tồn kho:
                <a class="font-medium text-brand hover:underline" href="{{ route('admin.reports.inventory.preview') }}">Xem báo cáo</a>
            </p>
            @endif
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isKho())
        <a href="{{ route('admin.motorcycles.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm sản phẩm
        </a>
        @endif
    </div>

    {{-- Tabs xe / phụ tùng --}}
    <div class="mt-5 flex gap-1 rounded-xl border bg-white p-1 w-fit">
        <a href="{{ route('admin.motorcycles.index', ['tab' => 'xe']) }}"
           class="rounded-lg px-5 py-2 text-sm font-semibold transition
                  {{ $tab === 'xe' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Xe
            <span class="ml-1.5 rounded-full {{ $tab === 'xe' ? 'bg-white/20' : 'bg-slate-100' }} px-2 py-0.5 text-xs">{{ $xeCount }}</span>
        </a>
        <a href="{{ route('admin.motorcycles.index', ['tab' => 'phu-tung']) }}"
           class="rounded-lg px-5 py-2 text-sm font-semibold transition
                  {{ $tab === 'phu-tung' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Phụ tùng
            <span class="ml-1.5 rounded-full {{ $tab === 'phu-tung' ? 'bg-white/20' : 'bg-slate-100' }} px-2 py-0.5 text-xs">{{ $phuTungCount }}</span>
        </a>
    </div>

    {{-- Bảng sản phẩm --}}
    <div class="mt-4 overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-slate-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-slate-600">Tên sản phẩm</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">Hãng</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">Danh mục</th>
                    <th class="px-4 py-3 font-semibold text-right text-slate-600">Giá bán</th>
                    <th class="px-4 py-3 font-semibold text-center text-slate-600">Tồn kho</th>
                    <th class="px-4 py-3 font-semibold text-center text-slate-600">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($motorcycles as $m)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $m->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $m->brand?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $m->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format((float)$m->price) }}₫</td>
                        <td class="px-4 py-3 text-center font-bold
                            {{ $m->stock <= 0 ? 'text-rose-600' : ($m->stock <= 3 ? 'text-yellow-600' : 'text-slate-800') }}">
                            {{ $m->stock }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($m->is_active)
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Đang bán</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Đã ẩn</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if(auth()->user()->isAdmin() || auth()->user()->isKho())
                                    <a href="{{ route('admin.motorcycles.images.index', $m) }}" class="text-sm text-slate-500 hover:text-slate-800 hover:underline">Ảnh</a>
                                    <a href="{{ route('admin.motorcycles.edit', $m) }}" class="text-sm font-medium text-brand hover:underline">Sửa</a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Chỉ xem</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                            Không có sản phẩm nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $motorcycles->links() }}
    </div>
@endsection
