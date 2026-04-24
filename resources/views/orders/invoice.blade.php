@extends('layouts.invoice')

@section('content')
    @php
        $paymentLabel = match ($order->payment_method ?? '') {
            'showroom_deposit' => 'Đặt cọc tại showroom',
            'cod_consult' => 'Ship COD (tư vấn trước)',
            'bank_transfer_deposit' => 'Chuyển khoản đặt cọc',
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'vnpay' => 'VNPAY',
            'bank_transfer' => 'Chuyển khoản',
            default => $order->payment_method,
        };
    @endphp

    <style>
        @page {
            size: auto;
            margin: 10mm;
        }
        @media print {
            @page {
                margin: 8mm;
            }
            body {
                background: #fff !important;
            }
        }
        .retail-invoice {
            --invoice-red: #b91c1c;
            background: #fff;
            border: 2px solid var(--invoice-red);
            border-radius: 0.5rem;
            padding: 0.85rem 0.75rem;
            color: #7f1d1d;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            font-size: 0.7rem;
            line-height: 1.35;
        }
        .invoice-line {
            border-bottom: 1px dotted #ef4444;
            min-height: 1.15rem;
            padding: 0 0.2rem 0.1rem;
            color: #111827;
        }
        .retail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.65rem;
            color: #111827;
        }
        .retail-table th,
        .retail-table td {
            border: 1px solid #dc2626;
            padding: 0.25rem 0.35rem;
        }
        .retail-table thead th {
            color: #7f1d1d;
            background: #fff5f5;
        }
        .invoice-sign-block {
            margin-top: 0.75rem;
            padding-top: 0.6rem;
            border-top: 1px dashed #fca5a5;
        }
        .invoice-sign-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 0;
        }
        .invoice-sign-space {
            width: 100%;
            margin-top: 0.35rem;
            min-height: 5.5rem;
            border-bottom: 1px dotted #fecaca;
        }
        @media print {
            .retail-invoice {
                border-radius: 0;
                box-shadow: none;
                border-width: 1.5px;
                margin: 0 auto;
                max-width: 100%;
                padding: 0.5rem;
                padding-bottom: 0.75rem;
            }
            .invoice-sign-space {
                min-height: 32mm;
                margin-top: 0.5rem;
            }
        }
    </style>

    <div class="mx-auto max-w-[420px] px-2 print:max-w-none">
        <div class="retail-invoice">
            <div class="text-center">
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-red-700">Liên 1: Lưu</p>
                <h1 class="mt-0.5 text-lg font-black uppercase leading-tight tracking-wide text-red-700">Hóa đơn bán lẻ</h1>
            </div>

            <div class="mt-2 grid gap-1.5 text-[0.7rem]">
                <div class="flex items-end gap-1.5">
                    <span class="shrink-0 font-semibold text-red-700">Ngày:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="flex items-end gap-1.5">
                    <span class="shrink-0 font-semibold text-red-700">Số HĐ:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ $order->order_number }}</div>
                </div>
            </div>

            <div class="mt-2 space-y-1 text-[0.7rem]">
                <div class="flex flex-wrap items-end gap-x-1 gap-y-0.5">
                    <span class="shrink-0 font-semibold text-red-700">Đơn vị bán:</span>
                    <div class="invoice-line min-w-0 flex-1 font-semibold">{{ config('store.name') }}</div>
                </div>
                <div class="flex flex-wrap items-end gap-x-1 gap-y-0.5">
                    <span class="shrink-0 font-semibold text-red-700">Địa chỉ:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ config('store.address') }}</div>
                </div>
                <div class="flex flex-wrap items-end gap-x-1 gap-y-0.5">
                    <span class="shrink-0 font-semibold text-red-700">ĐT:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ config('store.hotline') }}</div>
                    <span class="shrink-0 font-semibold text-red-700">Email:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ config('store.email') }}</div>
                </div>
                <div class="flex flex-wrap items-end gap-x-1 gap-y-0.5">
                    <span class="shrink-0 font-semibold text-red-700">Người mua:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ $order->customer?->full_name ?? '—' }}</div>
                </div>
                <div class="flex flex-wrap items-end gap-x-1 gap-y-0.5">
                    <span class="shrink-0 font-semibold text-red-700">Địa chỉ:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ $order->customer?->address ?? '—' }}</div>
                </div>
                <div class="flex flex-wrap items-end gap-x-1 gap-y-0.5">
                    <span class="shrink-0 font-semibold text-red-700">SĐT:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ $order->customer?->phone ?? '—' }}</div>
                    <span class="shrink-0 font-semibold text-red-700">TT:</span>
                    <div class="invoice-line min-w-0 flex-1">{{ $paymentLabel }}</div>
                </div>
            </div>

            <div class="mt-3 overflow-hidden rounded border border-red-700">
                <table class="retail-table">
                    <thead>
                        <tr>
                            <th class="w-6 text-center">STT</th>
                            <th>Tên hàng</th>
                            <th class="w-10 text-center">Đơn vị</th>
                            <th class="w-8 text-center">SL</th>
                            <th class="w-20 text-right">Đơn giá</th>
                            <th class="w-[4.5rem] text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $idx => $it)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    {{ $it->motorcycle?->name ?? 'Sản phẩm' }}
                                    @if($it->selected_color)
                                        <div class="text-[0.6rem] text-slate-500">Màu: {{ $it->selected_color }}</div>
                                    @endif
                                </td>
                                <td class="text-center">Chiếc</td>
                                <td class="text-center">{{ $it->quantity }}</td>
                                <td class="text-right whitespace-nowrap">{{ number_format((float) $it->unit_price) }}</td>
                                <td class="text-right font-semibold whitespace-nowrap">{{ number_format((float) $it->line_total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="h-12 text-center text-slate-500">Không có sản phẩm</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2 ml-auto w-full max-w-[220px] space-y-1 text-[0.7rem]">
                @if($order->promotion_code)
                    <div class="flex items-end gap-1">
                        <span class="shrink-0 font-semibold text-red-700">Voucher:</span>
                        <div class="invoice-line flex-1 text-right">{{ $order->promotion_code }}</div>
                    </div>
                @endif
                <div class="flex items-end gap-1">
                    <span class="shrink-0 font-semibold text-red-700">Cộng tiền:</span>
                    <div class="invoice-line flex-1 text-right">{{ number_format((float) $order->subtotal) }} đ</div>
                </div>
                <div class="flex items-end gap-1">
                    <span class="shrink-0 font-semibold text-red-700">Giảm giá:</span>
                    <div class="invoice-line flex-1 text-right">{{ number_format((float) $order->discount_total) }} đ</div>
                </div>
                <div class="flex items-end gap-1">
                    <span class="shrink-0 font-bold text-red-700">Tổng cộng:</span>
                    <div class="invoice-line flex-1 text-right font-bold">{{ number_format((float) $order->total) }} đ</div>
                </div>
            </div>

            <div class="invoice-sign-block grid grid-cols-2 gap-3 text-center text-[0.7rem] font-semibold text-red-700">
                <div class="invoice-sign-col">
                    <p>Người mua hàng</p>
                    <p class="text-[0.6rem] font-normal text-slate-500">(Ký, ghi rõ họ tên)</p>
                    <div class="invoice-sign-space" aria-hidden="true"></div>
                </div>
                <div class="invoice-sign-col">
                    <p>Người bán hàng</p>
                    <p class="text-[0.6rem] font-normal text-slate-500">(Ký, ghi rõ họ tên)</p>
                    <div class="invoice-sign-space" aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap justify-center gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800">In hóa đơn</button>
            @if(!($isAdminView ?? false))
                <a href="{{ route('profile') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium hover:bg-slate-50">Về tài khoản</a>
            @else
                <a href="{{ route('admin.orders.show', $order) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium hover:bg-slate-50">Về đơn hàng</a>
            @endif
        </div>
    </div>
@endsection
