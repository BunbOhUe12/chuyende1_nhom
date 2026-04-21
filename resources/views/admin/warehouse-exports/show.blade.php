@extends('layouts.admin')
@section('page-title', 'Chi tiết phiếu xuất kho')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">
                Phiếu xuất kho #{{ str_pad($export->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="mt-0.5 text-sm text-slate-500">Tạo lúc {{ $export->created_at->format('H:i d/m/Y') }}</p>
        </div>
        <span class="inline-flex items-center rounded-xl px-3 py-1.5 text-sm font-bold {{ \App\Models\WarehouseExport::STATUS_COLORS[$export->status] ?? 'bg-slate-100 text-slate-600' }}">
            {{ \App\Models\WarehouseExport::STATUS_LABELS[$export->status] ?? $export->status }}
        </span>
    </div>

    {{-- Thông tin phiếu --}}
    <div class="rounded-2xl border border-slate-200 bg-white divide-y divide-slate-100 shadow-sm">
        <div class="grid grid-cols-2 gap-4 p-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sản phẩm</p>
                <p class="mt-1 font-semibold text-slate-800">{{ $export->motorcycle?->name ?? '—' }}</p>
                @if($export->motorcycle?->frame_number)
                    <p class="text-xs text-slate-500 font-mono">Khung: {{ $export->motorcycle->frame_number }}</p>
                @endif
                @if($export->motorcycle?->engine_number)
                    <p class="text-xs text-slate-500 font-mono">Máy: {{ $export->motorcycle->engine_number }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Số lượng xuất</p>
                <p class="mt-1 text-3xl font-bold text-slate-800">{{ $export->quantity }} <span class="text-base font-normal text-slate-500">chiếc</span></p>
            </div>
        </div>
        <div class="p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lý do xuất kho</p>
            <p class="mt-1 text-slate-700">{{ $export->reason }}</p>
        </div>
        @if($export->notes)
        <div class="p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Ghi chú của người tạo</p>
            <p class="mt-1 text-slate-600">{{ $export->notes }}</p>
        </div>
        @endif
        <div class="grid grid-cols-2 gap-4 p-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Người tạo phiếu</p>
                <p class="mt-1 text-slate-700">{{ $export->requester?->name ?? '—' }}</p>
            </div>
            @if($export->handler)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Người xử lý</p>
                <p class="mt-1 text-slate-700">{{ $export->handler->name }}</p>
                @if($export->confirmed_at)<p class="text-xs text-slate-400">Xác nhận: {{ $export->confirmed_at->format('H:i d/m/Y') }}</p>@endif
                @if($export->completed_at)<p class="text-xs text-slate-400">Hoàn thành: {{ $export->completed_at->format('H:i d/m/Y') }}</p>@endif
            </div>
            @endif
        </div>
        @if($export->warehouse_notes)
        <div class="p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Ghi chú của bộ phận kho</p>
            <p class="mt-1 text-slate-600">{{ $export->warehouse_notes }}</p>
        </div>
        @endif
    </div>

    {{-- Xử lý phiếu (Kho) --}}
    @if(in_array($export->status, ['pending', 'confirmed']) && (auth()->user()->isAdmin() || auth()->user()->isKho()))
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
        <h3 class="mb-4 text-sm font-bold text-amber-800">Xử lý phiếu xuất kho</h3>
        <form action="{{ route('admin.warehouse-exports.updateStatus', $export) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="mb-1.5 block text-sm font-medium text-amber-900">Cập nhật trạng thái</label>
                <select name="status" class="w-full rounded-xl border border-amber-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100">
                    @if($export->status === 'pending')
                        <option value="confirmed">Xác nhận phiếu</option>
                        <option value="rejected">Từ chối</option>
                    @endif
                    @if($export->status === 'confirmed')
                        <option value="completed">Hoàn thành xuất kho (trừ tồn kho)</option>
                        <option value="rejected">Từ chối</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-amber-900">Ghi chú của bộ phận kho</label>
                <textarea name="warehouse_notes" rows="2" placeholder="Ghi chú thêm về việc xuất kho..."
                    class="w-full rounded-xl border border-amber-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100">{{ $export->warehouse_notes }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">
                    Lưu trạng thái
                </button>
            </div>
        </form>
    </div>
    @endif

    <a href="{{ route('admin.warehouse-exports.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Quay lại danh sách
    </a>
</div>
@endsection
