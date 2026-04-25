@extends('layouts.admin')
@section('page-title', 'Quản lý đơn hàng')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-xl font-semibold">Quản lý đơn hàng</h1>
        <div class="flex flex-wrap items-center gap-2">
            <form method="get" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-2 text-sm">
                <label class="text-slate-600">Tháng</label>
                <select name="month" class="rounded-lg border border-slate-300 px-2 py-1.5">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected((int)($filterMonth ?? now()->month) === $m)>{{ $m }}</option>
                    @endfor
                </select>
                <label class="text-slate-600">Năm</label>
                <select name="year" class="rounded-lg border border-slate-300 px-2 py-1.5">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected((int)($filterYear ?? now()->year) === $y)>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="rounded-lg bg-slate-900 px-3 py-1.5 text-white hover:bg-slate-800">Lọc</button>
                <a href="{{ route('admin.orders.index') }}" class="text-slate-600 hover:underline">Xóa lọc</a>
            </form>
        </div>
    </div>

    @if($filterMonth && $filterYear)
        <p class="mt-3 text-sm text-slate-600">
            Đang lọc đơn tạo trong tháng {{ $filterMonth }}/{{ $filterYear }} ·
            <a class="font-medium text-brand hover:underline" href="{{ route('admin.reports.orders.export', ['format' => 'csv', 'month' => $filterMonth, 'year' => $filterYear]) }}">Xuất Excel (CSV)</a>
            ·
            <a class="font-medium text-brand hover:underline" href="{{ route('admin.reports.orders.export', ['format' => 'pdf', 'month' => $filterMonth, 'year' => $filterYear]) }}">Xuất PDF</a>
        </p>
    @endif

    @if(session('success'))
        <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(($pendingApprovals ?? collect())->isNotEmpty())
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="text-sm font-semibold text-amber-900">
                    Có {{ $pendingApprovals->count() }} đơn hàng mới chờ duyệt
                </div>
                <span class="text-xs text-amber-700">Admin vui lòng vào chi tiết đơn để duyệt</span>
            </div>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach($pendingApprovals as $pendingOrder)
                    <a href="{{ route('admin.orders.show', $pendingOrder) }}" class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm hover:bg-amber-100/40">
                        <div class="font-medium text-slate-900">{{ $pendingOrder->order_number }}</div>
                        <div class="text-xs text-slate-600">
                            {{ $pendingOrder->customer?->full_name ?? 'Khách lẻ' }} · {{ number_format((float) $pendingOrder->total) }}₫
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="px-4 py-3">Mã đơn</th>
                <th class="px-4 py-3">Khách</th>
                <th class="px-4 py-3">Trạng thái</th>
                <th class="px-4 py-3">Tổng</th>
                <th class="px-4 py-3">Ngày</th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $o)
                <tr class="border-t">
                    <td class="px-4 py-3 font-medium">{{ $o->order_number }}</td>
                    <td class="px-4 py-3">{{ $o->customer?->full_name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $o->statusColor() }}">{{ $o->statusLabel() }}</span>
                    </td>
                    <td class="px-4 py-3">{{ number_format((float)$o->total) }}₫</td>
                    <td class="px-4 py-3">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('admin.orders.show', $o) }}" class="hover:underline">Xem</a>
                            <a href="{{ route('admin.orders.invoice', $o) }}" class="text-brand hover:underline">In hóa đơn</a>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('admin.orders.destroy', $o) }}" method="POST"
                                  onsubmit="return confirm('Xoá đơn hàng {{ $o->order_number }}? Hành động này không thể hoàn tác.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:underline text-sm">Xoá</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection
