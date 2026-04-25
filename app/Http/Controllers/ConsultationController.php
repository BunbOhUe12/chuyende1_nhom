<?php

namespace App\Http\Controllers;

use App\Models\ConsultationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $messages = ConsultationMessage::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('consultation.index', [
            'messages' => $messages,
            'user' => $user,
            'social' => config('store.social', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferred_channel' => ['required', 'in:internal,zalo,facebook'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ConsultationMessage::query()->create([
            'user_id' => $request->user()->id,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?: null,
            'preferred_channel' => $data['preferred_channel'],
            'message' => trim($data['message']),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('consultation.index')
            ->with('success', 'Đã gửi tin nhắn tư vấn. Nhân viên sẽ phản hồi sớm.');
    }

    /** Gửi yêu cầu tư vấn nhanh — không cần đăng nhập */
    public function publicStore(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:255'],
            'product'   => ['nullable', 'string', 'max:255'],
            'area'      => ['nullable', 'string', 'max:255'],
            'note'      => ['nullable', 'string', 'max:1000'],
        ]);

        $lines = [];
        if (!empty($data['product'])) $lines[] = 'Sản phẩm: ' . $data['product'];
        if (!empty($data['area']))    $lines[] = 'Khu vực: '  . $data['area'];
        if (!empty($data['email']))   $lines[] = 'Email: '    . $data['email'];
        if (!empty($data['note']))    $lines[] = 'Ghi chú: '  . $data['note'];
        $message = implode("\n", $lines) ?: '(Yêu cầu tư vấn nhanh)';

        ConsultationMessage::create([
            'user_id'           => auth()->id(),
            'full_name'         => $data['full_name'],
            'phone'             => $data['phone'],
            'preferred_channel' => 'internal',
            'message'           => $message,
            'status'            => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã gửi yêu cầu tư vấn!']);
        }

        return back()->with('success', 'Cảm ơn! Nhân viên sẽ liên hệ với bạn sớm.');
    }
}
