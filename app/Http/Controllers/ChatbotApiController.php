<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ChatbotKnowledge;
use App\Models\ChatbotSetting;
use App\Models\Motorcycle;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChatbotApiController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $message = trim($request->message);
        $setting = ChatbotSetting::instance();

        if (! $setting->is_active) {
            return response()->json(['reply' => 'Chatbot hiện đang tắt. Vui lòng liên hệ hotline 0899.798.920.']);
        }

        if ($setting->use_gemini && $setting->gemini_api_key) {
            $reply = $this->askGemini($message, $setting);
            if ($reply !== null) {
                return response()->json(['reply' => $reply]);
            }
        }

        $reply = $this->searchKnowledge($message);
        return response()->json(['reply' => $reply]);
    }

    private function askGemini(string $message, ChatbotSetting $setting): ?string
    {
        $basePrompt = ($setting->system_prompt && mb_strlen($setting->system_prompt) > 20)
            ? $setting->system_prompt
            : $this->defaultSystemPrompt();

        // Dữ liệu thực từ cửa hàng (cache 5 phút)
        $storeContext = Cache::remember('chatbot_store_context', 300, fn () => $this->buildStoreContext());
        $kbContext    = $this->buildKbContext();

        $systemPrompt = $basePrompt . "\n\n" . $storeContext;

        // Tài liệu huấn luyện do admin upload
        if ($setting->document_text && mb_strlen($setting->document_text) > 10) {
            $docName = $setting->document_name ? "({$setting->document_name})" : '';
            $systemPrompt .= "\n\n--- TÀI LIỆU THAM KHẢO {$docName} ---\n"
                . mb_substr($setting->document_text, 0, 30000); // giới hạn 30k ký tự
        }

        if ($kbContext) {
            $systemPrompt .= "\n\n--- CÂU HỎI THƯỜNG GẶP ---\n" . $kbContext;
        }

        $apiKey  = trim($setting->gemini_api_key);
        $payload = [
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents'           => [['role' => 'user', 'parts' => [['text' => $message]]]],
            'generationConfig'   => ['temperature' => 0.7, 'maxOutputTokens' => 2048],
        ];

        $models = ['gemini-2.5-flash', 'gemini-2.5-flash-lite', 'gemini-2.0-flash', 'gemini-2.0-flash-lite'];

        foreach ($models as $model) {
            try {
                $response = Http::withOptions(['verify' => false])->timeout(20)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    $payload
                );

                if ($response->successful()) {
                    $data      = $response->json();
                    $candidate = $data['candidates'][0] ?? null;
                    $text      = $candidate['content']['parts'][0]['text'] ?? null;
                    $reason    = $candidate['finishReason'] ?? 'STOP';

                    if ($text) {
                        // Nếu bị cắt giữa chừng do giới hạn token → thêm ghi chú
                        if ($reason === 'MAX_TOKENS') {
                            $text = rtrim($text) . '...<br><em style="color:#94a3b8;font-size:12px;">📞 Để biết thêm chi tiết, liên hệ hotline 0899.798.920</em>';
                        }
                        return $this->formatGeminiText($text);
                    }
                } elseif (in_array($response->status(), [429, 503, 529])) {
                    \Log::warning("Gemini {$model} bị {$response->status()}, thử model tiếp theo");
                    continue;
                } else {
                    \Log::error("Gemini {$model} lỗi", [
                        'status' => $response->status(),
                        'body'   => substr($response->body(), 0, 300),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error("Gemini {$model} exception: " . $e->getMessage());
            }
        }

        return null;
    }

    private function defaultSystemPrompt(): string
    {
        return <<<PROMPT
Bạn là trợ lý tư vấn bán xe máy của cửa hàng EAUT MOTOBIKE tại Hà Nội.

THÔNG TIN CỬA HÀNG (bắt buộc dùng đúng, KHÔNG được bịa):
- Tên: EAUT MOTOBIKE
- Địa chỉ: Phố Phan Tây Nhạc, Phường Xuân Phương, Từ Liêm, Hà Nội
- Hotline: 0899.798.920
- Email: EAUTMOTOBIKE@gmail.com
- Giờ mở cửa: Thứ 2–Thứ 6: 8:00–19:30 | Thứ 7–Chủ nhật: 8:00–18:00

QUY TẮC QUAN TRỌNG:
- Chỉ dùng thông tin được cung cấp bên dưới, KHÔNG bịa thêm
- Trả lời NGẮN GỌN, đủ ý, LUÔN hoàn chỉnh câu cuối — không bao giờ bỏ lửng
- Khi gợi ý xe: chỉ nêu tối đa 3 mẫu phù hợp nhất, kèm giá và lý do ngắn
- Dùng tiếng Việt, thân thiện, có thể dùng emoji nhẹ nhàng
- Nếu không có thông tin, gợi ý liên hệ hotline 0899.798.920
- KHÔNG bao giờ bịa địa chỉ, giá hay thông tin khác
- Kết thúc mỗi câu trả lời bằng một câu hỏi ngắn để hỏi thêm nhu cầu khách
PROMPT;
    }

    /**
     * Lấy dữ liệu thực từ DB: sản phẩm, thương hiệu, ưu đãi
     */
    private function buildStoreContext(): string
    {
        $parts = [];

        // --- Danh sách xe đang bán ---
        $motorcycles = Motorcycle::with('brand')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('price')
            ->get(['id', 'brand_id', 'name', 'price', 'engine_cc', 'color', 'stock', 'description']);

        if ($motorcycles->isNotEmpty()) {
            $lines = ["--- DANH SÁCH XE ĐANG BÁN (tồn kho thực tế) ---"];
            foreach ($motorcycles as $m) {
                $brand = $m->brand?->name ?? '';
                $price = number_format($m->price, 0, ',', '.') . ' đ';
                $line  = "• {$brand} {$m->name} | Giá: {$price} | Dung tích: {$m->engine_cc}cc | Màu: {$m->color} | Còn: {$m->stock} xe";
                if ($m->description) {
                    $line .= " | " . mb_substr(strip_tags($m->description), 0, 80);
                }
                $lines[] = $line;
            }
            $parts[] = implode("\n", $lines);
        }

        // --- Thương hiệu ---
        $brands = Brand::where('is_active', true)->pluck('name')->implode(', ');
        if ($brands) {
            $parts[] = "--- THƯƠNG HIỆU ĐANG PHÂN PHỐI ---\n{$brands}";
        }

        // --- Ưu đãi đang hoạt động ---
        $now = now();
        $promotions = Promotion::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->orderByDesc('created_at')
            ->get(['name', 'description', 'code', 'type', 'value', 'ends_at']);

        if ($promotions->isNotEmpty()) {
            $lines = ["--- CHƯƠNG TRÌNH ƯU ĐÃI ĐANG DIỄN RA ---"];
            foreach ($promotions as $p) {
                $discount = '';
                if ($p->value) {
                    $discount = $p->type === 'percent'
                        ? "Giảm {$p->value}%"
                        : "Giảm " . number_format($p->value, 0, ',', '.') . "đ";
                }
                $ends    = $p->ends_at ? " | Hết hạn: " . \Carbon\Carbon::parse($p->ends_at)->format('d/m/Y') : '';
                $coupon  = $p->code ? " | Mã: {$p->code}" : '';
                $desc    = $p->description ? " — " . mb_substr(strip_tags($p->description), 0, 100) : '';
                $lines[] = "• {$p->name}" . ($discount ? " ({$discount})" : '') . $ends . $coupon . $desc;
            }
            $parts[] = implode("\n", $lines);
        }

        // --- Chính sách chung ---
        $parts[] = <<<POLICY
--- CHÍNH SÁCH CỬA HÀNG ---
• Bảo hành: 3 năm hoặc 30.000km (xe mới chính hãng)
• Trả góp: hỗ trợ qua ngân hàng, chỉ cần CMND/CCCD + chứng minh thu nhập
• Thu cũ đổi mới: định giá minh bạch, nhanh chóng tại showroom
• Giao xe tận nơi: liên hệ để biết phạm vi giao hàng
• Thanh toán: tiền mặt, chuyển khoản, thẻ ngân hàng
POLICY;

        return implode("\n\n", $parts);
    }

    private function buildKbContext(): string
    {
        $items = ChatbotKnowledge::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['question', 'answer']);

        return $items->map(fn ($i) => "H: {$i->question}\nĐ: {$i->answer}")->implode("\n\n");
    }

    private function searchKnowledge(string $message): string
    {
        $messageLower = mb_strtolower($message);
        $items        = ChatbotKnowledge::where('is_active', true)->orderBy('sort_order')->get();

        foreach ($items as $item) {
            if ($item->keywords) {
                foreach ($item->keywords_array as $kw) {
                    if ($kw && mb_strpos($messageLower, mb_strtolower($kw)) !== false) {
                        return $item->answer;
                    }
                }
            }
            if (mb_strpos($messageLower, mb_strtolower($item->question)) !== false) {
                return $item->answer;
            }
        }

        return 'Cảm ơn bạn đã hỏi! 😊 Để được tư vấn chi tiết nhất, vui lòng liên hệ hotline <strong>0899.798.920</strong> hoặc <a href="/lien-he-tu-van" style="color:#FF5722;font-weight:600;">để lại thông tin tư vấn</a> để nhân viên hỗ trợ sớm nhất!';
    }

    private function formatGeminiText(string $text): string
    {
        $out = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        // Markdown bold/italic
        $out = preg_replace('/\*\*(.+?)\*\*/su', '<strong>$1</strong>', $out);
        $out = preg_replace('/\*(.+?)\*/su', '<em>$1</em>', $out);
        // Bullet list: dòng bắt đầu bằng •, -, *
        $out = preg_replace('/^[•\-\*]\s+/mu', '• ', $out);
        $out = nl2br($out);
        return $out;
    }

    public function getConfig(): JsonResponse
    {
        $setting = ChatbotSetting::instance();
        return response()->json([
            'bot_name'   => $setting->bot_name,
            'avatar_url' => $setting->avatar_url,
            'is_active'  => $setting->is_active,
        ]);
    }
}
