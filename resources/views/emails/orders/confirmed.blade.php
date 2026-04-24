<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đơn hàng đã được xác nhận</title>
</head>
<body style="margin:0;padding:0;font-family:system-ui,-apple-system,sans-serif;font-size:15px;line-height:1.5;color:#1e293b;background:#f8fafc;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" style="max-width:560px;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
                <tr>
                    <td style="padding:24px 28px;background:#FF5722;color:#fff;">
                        <h1 style="margin:0;font-size:18px;font-weight:700;">Đặt hàng thành công</h1>
                        <p style="margin:8px 0 0;font-size:14px;opacity:0.95;">{{ config('app.name') }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 28px;">
                        <p style="margin:0 0 16px;">Xin chào <strong>{{ $order->customer?->full_name ?? 'Quý khách' }}</strong>,</p>
                        <p style="margin:0 0 16px;">Cửa hàng đã <strong>xác nhận</strong> đơn hàng của bạn.</p>
                        <table role="presentation" cellspacing="0" cellpadding="0" style="width:100%;margin:16px 0;background:#f1f5f9;border-radius:8px;">
                            <tr>
                                <td style="padding:16px;">
                                    <p style="margin:0 0 8px;"><span style="color:#64748b;">Mã đơn:</span> <strong style="font-family:monospace;">{{ $order->order_number }}</strong></p>
                                    <p style="margin:0 0 8px;"><span style="color:#64748b;">Trạng thái:</span> <strong>{{ $order->statusLabel() }}</strong></p>
                                    <p style="margin:0;"><span style="color:#64748b;">Tổng thanh toán:</span> <strong>{{ number_format((float) $order->total, 0, ',', '.') }}&#8363;</strong></p>
                                </td>
                            </tr>
                        </table>
                        @if($order->items->isNotEmpty())
                            <p style="margin:16px 0 8px;font-weight:600;">Sản phẩm</p>
                            <ul style="margin:0;padding-left:20px;color:#475569;">
                                @foreach($order->items as $item)
                                    <li style="margin-bottom:6px;">
                                        {{ $item->motorcycle?->name ?? 'Sản phẩm' }} × {{ $item->quantity }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <p style="margin:24px 0 0;">
                            <a href="{{ route('orders.track', $order) }}" style="display:inline-block;padding:12px 20px;background:#FF5722;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;">Theo dõi đơn hàng</a>
                        </p>
                        <p style="margin:16px 0 0;font-size:13px;color:#64748b;">Đăng nhập bằng tài khoản đã đặt hàng để xem tiến độ giao hàng (sau đăng nhập bạn sẽ được chuyển tới trang theo dõi đơn).</p>
                        <p style="margin:24px 0 0;font-size:13px;color:#64748b;">Trân trọng,<br><strong>{{ config('app.name') }}</strong></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
