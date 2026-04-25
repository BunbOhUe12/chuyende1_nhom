<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $alreadyReviewed = Review::query()
            ->where('motorcycle_id', $motorcycle->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Bạn đã đánh giá xe này rồi.');
        }

        Review::create([
            'motorcycle_id' => $motorcycle->id,
            'user_id'       => auth()->id(),
            'rating'        => $data['rating'],
            'comment'       => $data['comment'] ?? null,
            'is_approved'   => false,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! Đánh giá đang chờ kiểm duyệt.');
    }
}
