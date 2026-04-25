<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationMessageController extends Controller
{
    public function index(): View
    {
        $messages = ConsultationMessage::query()
            ->with(['user', 'replier'])
            ->latest()
            ->paginate(20);

        return view('admin.consultations.index', [
            'messages' => $messages,
        ]);
    }

    public function reply(Request $request, ConsultationMessage $consultation): RedirectResponse
    {
        $data = $request->validate([
            'admin_reply' => ['required', 'string', 'max:3000'],
        ]);

        $consultation->update([
            'admin_reply' => trim($data['admin_reply']),
            'status' => 'replied',
            'replied_by' => $request->user()->id,
            'replied_at' => now(),
        ]);

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Đã gửi phản hồi cho khách hàng.');
    }
}
