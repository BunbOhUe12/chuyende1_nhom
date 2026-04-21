<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $now = now();

        $promotions = Promotion::query()
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                // Chưa hết hạn (hoặc không đặt ngày kết thúc)
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('promotions.index', compact('promotions'));
    }

    public function show(Promotion $promotion): View
    {
        return view('promotions.show', compact('promotion'));
    }
}
