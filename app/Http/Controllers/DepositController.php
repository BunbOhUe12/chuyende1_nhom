<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Motorcycle;
use App\Services\VnpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    public const DEPOSIT_AMOUNT = 1_000_000;

    /**
     * Tạo phiếu đặt cọc và chuyển hướng sang VNPAY.
     */
    public function store(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        if ($motorcycle->stock <= 0) {
            return back()->with('error', 'Xe này đã hết hàng, không thể đặt cọc.');
        }

        $user = $request->user();

        // Kiểm tra xem khách đã có cọc pending/paid cho xe này chưa
        $existing = Deposit::query()
            ->where('user_id', $user->id)
            ->where('motorcycle_id', $motorcycle->id)
            ->whereIn('status', ['pending', 'paid'])
            ->latest()
            ->first();

        if ($existing && $existing->isPaid()) {
            return back()->with('error', 'Bạn đã đặt cọc xe này thành công rồi.');
        }

        if ($existing && ! $existing->isPaid()) {
            // Tái sử dụng phiếu pending → chuyển thẳng sang VNPAY
            $deposit = $existing;
        } else {
            $deposit = Deposit::create([
                'deposit_number' => 'DEP-' . strtoupper(Str::random(8)),
                'motorcycle_id'  => $motorcycle->id,
                'user_id'        => $user->id,
                'status'         => 'pending',
                'amount'         => self::DEPOSIT_AMOUNT,
                'expires_at'     => now()->addDays(7),
            ]);
        }

        $vnpay = app(VnpayService::class);

        if (! $vnpay->isConfigured()) {
            return back()->with('error', 'Cổng thanh toán VNPAY chưa được cấu hình. Vui lòng liên hệ cửa hàng.');
        }

        $payUrl = $vnpay->createDepositPaymentUrl($deposit, $request->ip() ?: '127.0.0.1');

        return redirect()->away($payUrl);
    }

    /**
     * Trang xác nhận đặt cọc thành công / thất bại.
     */
    public function success(Deposit $deposit): \Illuminate\View\View
    {
        abort_unless($deposit->user_id === auth()->id(), 403);
        $deposit->load('motorcycle');

        return view('deposit-success', compact('deposit'));
    }
}
