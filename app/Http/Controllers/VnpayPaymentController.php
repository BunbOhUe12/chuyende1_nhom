<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Order;
use App\Services\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnpayPaymentController extends Controller
{
    public function __construct(
        private VnpayService $vnpay
    ) {}

    /**
     * Khách quay lại từ cổng VNPAY (trình duyệt).
     */
    public function return(Request $request): RedirectResponse
    {
        $verified = $this->vnpay->verifyCallback($request->query());

        if (! $verified['valid']) {
            return redirect()->route('home')->with('error', $verified['message']);
        }

        $txnRef = (string) ($verified['txn_ref'] ?? '');

        // Đặt cọc
        if (str_starts_with($txnRef, 'DEP-')) {
            return $this->handleDepositReturn($txnRef, $verified, $request);
        }

        // Đơn hàng thông thường
        return $this->handleOrderReturn($txnRef, $verified, $request);
    }

    /**
     * IPN: VNPAY gọi server-to-server.
     */
    public function ipn(Request $request): JsonResponse
    {
        $params   = array_merge($request->query(), $request->post());
        $verified = $this->vnpay->verifyCallback($params);

        if (! $verified['valid']) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $txnRef = (string) ($verified['txn_ref'] ?? '');

        if (str_starts_with($txnRef, 'DEP-')) {
            return $this->handleDepositIpn($txnRef, $verified);
        }

        return $this->handleOrderIpn($txnRef, $verified);
    }

    // -------------------------------------------------------
    //  ĐẶT CỌC
    // -------------------------------------------------------

    private function handleDepositReturn(string $txnRef, array $verified, Request $request): RedirectResponse
    {
        $deposit = Deposit::query()->where('deposit_number', $txnRef)->first();

        if (! $deposit || (int) $deposit->user_id !== (int) $request->user()?->id) {
            return redirect()->route('home')->with('error', 'Không tìm thấy phiếu đặt cọc.');
        }

        $this->markDepositPaidIfSuccess($deposit, $verified);
        $deposit->refresh();

        if (($verified['response_code'] ?? '') === '00') {
            return redirect()
                ->route('deposits.success', $deposit)
                ->with('success', 'Đặt cọc thành công! Xe đã được giữ cho bạn trong 7 ngày.');
        }

        $code = $verified['response_code'] ?? '';
        return redirect()
            ->route('deposits.success', $deposit)
            ->with('error', 'Thanh toán chưa hoàn tất (mã: ' . $code . '). Vui lòng thử lại hoặc liên hệ cửa hàng.');
    }

    private function handleDepositIpn(string $txnRef, array $verified): JsonResponse
    {
        $deposit = Deposit::query()->where('deposit_number', $txnRef)->first();

        if (! $deposit) {
            return response()->json(['RspCode' => '01', 'Message' => 'Deposit not found']);
        }

        $expected = round((float) $deposit->amount, 2);
        $got      = round((float) ($verified['amount_vnd'] ?? 0), 2);

        if (abs($expected - $got) > 0.01) {
            Log::warning('VNPAY IPN deposit amount mismatch', [
                'deposit' => $deposit->deposit_number,
                'expected' => $expected,
                'got' => $got,
            ]);
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        if ($this->markDepositPaidIfSuccess($deposit, $verified)) {
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        if ($deposit->status === 'paid') {
            return response()->json(['RspCode' => '02', 'Message' => 'Deposit already confirmed']);
        }

        return response()->json(['RspCode' => '99', 'Message' => 'Unknown error']);
    }

    private function markDepositPaidIfSuccess(Deposit $deposit, array $verified): bool
    {
        if (($verified['response_code'] ?? '') !== '00') {
            return false;
        }

        if ($deposit->status === 'paid') {
            return false;
        }

        $deposit->forceFill([
            'status'        => 'paid',
            'paid_at'       => now(),
            'vnpay_txn_no'  => $verified['txn_ref'] ?? null,
        ])->save();

        return true;
    }

    // -------------------------------------------------------
    //  ĐƠN HÀNG
    // -------------------------------------------------------

    private function handleOrderReturn(string $txnRef, array $verified, Request $request): RedirectResponse
    {
        $order = Order::query()->where('order_number', $txnRef)->first();

        if (! $order || ! $order->customer || (int) $order->customer->user_id !== (int) $request->user()?->id) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng hoặc không có quyền.');
        }

        if ($order->payment_method !== 'vnpay') {
            return redirect()->route('home')->with('error', 'Đơn không phải thanh toán VNPAY.');
        }

        $this->markOrderPaidIfSuccess($order, $verified);
        $order->refresh();

        $code = $verified['response_code'] ?? '';
        if ($code === '00') {
            return redirect()
                ->route('orders.success', $order)
                ->with('success', 'Thanh toán VNPAY thành công.');
        }

        return redirect()
            ->route('orders.success', $order)
            ->with('error', 'Thanh toán chưa hoàn tất (mã: ' . $code . '). Bạn có thể thử lại hoặc liên hệ cửa hàng.');
    }

    private function handleOrderIpn(string $txnRef, array $verified): JsonResponse
    {
        $order = Order::query()->where('order_number', $txnRef)->first();

        if (! $order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        if ($order->payment_method !== 'vnpay') {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid order']);
        }

        $expected = round((float) $order->total, 2);
        $got      = round((float) ($verified['amount_vnd'] ?? 0), 2);

        if (abs($expected - $got) > 0.01) {
            Log::warning('VNPAY IPN amount mismatch', [
                'order'    => $order->order_number,
                'expected' => $expected,
                'got'      => $got,
            ]);
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        $updated = $this->markOrderPaidIfSuccess($order, $verified);

        if ($updated) {
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        if (in_array($order->status, ['approved', 'paid'], true)) {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        return response()->json(['RspCode' => '99', 'Message' => 'Unknown error']);
    }

    private function markOrderPaidIfSuccess(Order $order, array $verified): bool
    {
        if (($verified['response_code'] ?? '') !== '00') {
            return false;
        }

        if (in_array($order->status, ['approved', 'paid'], true)) {
            return false;
        }

        $order->forceFill(['status' => 'approved'])->save();

        return true;
    }
}
