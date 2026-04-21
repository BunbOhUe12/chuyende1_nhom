<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\View\View;

class TradeInController extends Controller
{
    
    public function index(): View
    {
        $newModels = Motorcycle::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['brand', 'category', 'approvedCoverImage'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('trade-in', compact('newModels'));
    }
}
