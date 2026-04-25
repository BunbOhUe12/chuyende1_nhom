<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LuckyWheelReward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LuckyWheelRewardController extends Controller
{
    public function index(): View
    {
        $rewards = LuckyWheelReward::query()
            ->orderByDesc('is_active')
            ->orderByDesc('weight')
            ->orderBy('id')
            ->get();

        return view('admin.lucky-wheel.index', [
            'rewards' => $rewards,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:1', 'max:1000'],
            'valid_days' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        LuckyWheelReward::query()->create([
            'title' => trim($data['title']),
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'weight' => (int) $data['weight'],
            'valid_days' => (int) $data['valid_days'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.lucky-wheel.index')->with('success', 'Đã thêm phần thưởng.');
    }

    public function update(Request $request, LuckyWheelReward $reward): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:1', 'max:1000'],
            'valid_days' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $reward->update([
            'title' => trim($data['title']),
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'weight' => (int) $data['weight'],
            'valid_days' => (int) $data['valid_days'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.lucky-wheel.index')->with('success', 'Đã cập nhật phần thưởng.');
    }

    public function destroy(LuckyWheelReward $reward): RedirectResponse
    {
        $reward->delete();

        return redirect()->route('admin.lucky-wheel.index')->with('success', 'Đã xóa phần thưởng.');
    }
}
