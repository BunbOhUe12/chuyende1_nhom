<?php

namespace App\Http\Controllers;

use App\Models\LuckyWheelReward;
use App\Models\LuckyWheelSpin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LuckyWheelController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();
        $alreadySpun = LuckyWheelSpin::query()
            ->where('user_id', $user->id)
            ->where('spin_date', $today)
            ->exists();

        $rewards = LuckyWheelReward::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'weight', 'valid_days']);

        return response()->json([
            'can_spin' => ! $alreadySpun && $rewards->isNotEmpty(),
            'rewards' => $rewards,
        ]);
    }

    public function spin(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();

        $result = DB::transaction(function () use ($user, $today) {
            $already = LuckyWheelSpin::query()
                ->where('user_id', $user->id)
                ->where('spin_date', $today)
                ->lockForUpdate()
                ->first();

            if ($already) {
                return ['error' => 'Bạn đã quay hôm nay rồi. Mỗi ngày chỉ được 1 lần.'];
            }

            $rewards = LuckyWheelReward::query()
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($rewards->isEmpty()) {
                return ['error' => 'Hiện chưa có phần thưởng khả dụng.'];
            }

            $selected = $this->pickRandomReward($rewards);
            $spunAt = now();
            $expiresAt = Carbon::parse($spunAt)->addDays(max(1, (int) $selected->valid_days))->endOfDay();

            LuckyWheelSpin::query()->create([
                'user_id' => $user->id,
                'reward_id' => $selected->id,
                'spin_date' => $today,
                'reward_title' => $selected->title,
                'reward_description' => $selected->description,
                'spun_at' => $spunAt,
                'expires_at' => $expiresAt,
            ]);

            return [
                'reward' => [
                    'id' => $selected->id,
                    'title' => $selected->title,
                    'description' => $selected->description,
                    'expires_at' => $expiresAt->format('d/m/Y H:i'),
                ],
            ];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }

        return response()->json([
            'message' => 'Chúc mừng! Bạn đã nhận thưởng.',
            'reward' => $result['reward'],
        ]);
    }

    /** Mỗi phần thưởng có xác suất bằng nhau (1/n). */
    private function pickRandomReward($rewards): LuckyWheelReward
    {
        $n = $rewards->count();

        return $rewards[random_int(0, $n - 1)];
    }
}
