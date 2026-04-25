<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotKnowledge;
use App\Models\ChatbotSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatbotController extends Controller
{
    public function settings()
    {
        $setting = ChatbotSetting::instance();
        return view('admin.chatbot.settings', compact('setting'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'bot_name'       => 'required|string|max:80',
            'avatar'         => 'nullable|image|max:2048',
            'system_prompt'  => 'nullable|string|max:5000',
            'gemini_api_key' => 'nullable|string|max:500',
            'document_file'  => 'nullable|file|mimes:txt,pdf,doc,docx|max:5120',
            'document_text'  => 'nullable|string|max:100000',
        ]);

        $setting = ChatbotSetting::instance();

        $data = [
            'bot_name'       => $request->bot_name,
            'system_prompt'  => $request->system_prompt,
            'gemini_api_key' => $request->filled('gemini_api_key')
                ? trim($request->gemini_api_key)
                : $setting->gemini_api_key,
            'use_gemini'  => $request->boolean('use_gemini'),
            'is_active'   => $request->boolean('is_active'),
        ];

        // Avatar
        if ($request->hasFile('avatar')) {
            if ($setting->avatar) {
                Storage::disk('public')->delete($setting->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('chatbot', 'public');
        }
        if ($request->boolean('remove_avatar')) {
            if ($setting->avatar) {
                Storage::disk('public')->delete($setting->avatar);
            }
            $data['avatar'] = null;
        }

        // Xử lý file tài liệu upload
        if ($request->hasFile('document_file')) {
            $file      = $request->file('document_file');
            $ext       = strtolower($file->getClientOriginalExtension());
            $extracted = $this->extractTextFromFile($file->getRealPath(), $ext);
            if ($extracted !== null) {
                $data['document_text'] = $extracted;
                $data['document_name'] = $file->getClientOriginalName();
            }
        } elseif ($request->filled('document_text')) {
            // Paste text trực tiếp
            $data['document_text'] = $request->document_text;
            $data['document_name'] = 'Nội dung nhập tay';
        }

        // Xóa tài liệu nếu được yêu cầu
        if ($request->boolean('remove_document')) {
            $data['document_text'] = null;
            $data['document_name'] = null;
        }

        $setting->update($data);

        // Xóa cache store context để Gemini dùng tài liệu mới
        \Cache::forget('chatbot_store_context');

        return back()->with('success', 'Đã lưu cài đặt chatbot!');
    }

    private function extractTextFromFile(string $path, string $ext): ?string
    {
        if ($ext === 'txt') {
            return file_get_contents($path) ?: null;
        }

        if ($ext === 'pdf') {
            // Dùng pdfparser nếu có
            if (class_exists('\Smalot\PdfParser\Parser')) {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf    = $parser->parseFile($path);
                    return $pdf->getText() ?: null;
                } catch (\Throwable) {}
            }
            return null;
        }

        if (in_array($ext, ['doc', 'docx'])) {
            // Dùng PhpWord nếu có
            if (class_exists('\PhpOffice\PhpWord\IOFactory')) {
                try {
                    $word = \PhpOffice\PhpWord\IOFactory::load($path);
                    $text = '';
                    foreach ($word->getSections() as $section) {
                        foreach ($section->getElements() as $el) {
                            if (method_exists($el, 'getText')) {
                                $text .= $el->getText() . "\n";
                            }
                        }
                    }
                    return $text ?: null;
                } catch (\Throwable) {}
            }
            return null;
        }

        return null;
    }

    public function knowledge()
    {
        $items = ChatbotKnowledge::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.chatbot.knowledge', compact('items'));
    }

    public function storeKnowledge(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string|max:3000',
            'keywords' => 'nullable|string|max:500',
        ]);

        ChatbotKnowledge::create([
            'question'   => $request->question,
            'answer'     => $request->answer,
            'keywords'   => $request->keywords,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Đã thêm câu hỏi!');
    }

    public function updateKnowledge(Request $request, ChatbotKnowledge $knowledge)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string|max:3000',
            'keywords' => 'nullable|string|max:500',
        ]);

        $knowledge->update([
            'question'   => $request->question,
            'answer'     => $request->answer,
            'keywords'   => $request->keywords,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Đã cập nhật!');
    }

    public function destroyKnowledge(ChatbotKnowledge $knowledge)
    {
        $knowledge->delete();
        return back()->with('success', 'Đã xóa!');
    }
}
