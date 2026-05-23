<?php

namespace App\Services;

use App\Models\SeedingScenario;
use App\Models\OutboundLead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    public static function generateSeedingComments(SeedingScenario $scenario): array
    {
        $apiKey = env('GEMINI_API_KEY') ?: config('services.gemini.key');

        if (!$apiKey) {
            return self::generateMockComments($scenario);
        }

        $prompt = "Bạn là một chuyên gia tâm lý học đám đông và điều hướng dư luận xã hội trong lĩnh vực marketing. 
Dưới đây là một chiến dịch seeding:
- Thương hiệu: {$scenario->brand_name}
- Link bài viết đích cần tương tác: {$scenario->target_link}
- Tình huống thực tế cần xử lý: {$scenario->context}

Hãy viết ra các câu bình luận (seeding comments) mẫu bằng tiếng Việt chia làm 4 góc độ tâm lý xã hội:
1. THAM (Lòng tham): Tập trung vào lợi ích cá nhân, khuyến mãi, voucher, quà tặng, so sánh giá rẻ, lợi ích kinh tế mà người tiêu dùng nhận được. Sinh ra 2 mẫu câu.
2. SÂN (Sự phẫn nộ/tranh cãi/kích động): Tạo ra phản biện nhẹ, so sánh gay gắt hoặc bênh vực thương hiệu trước chỉ trích, phản đối đối thủ cạnh tranh một cách khéo léo để điều hướng ý kiến đám đông. Sinh ra 2 mẫu câu.
3. SI (Sự mê muội/tò mò/FOMO/hùa theo): Viết theo dạng hùa theo đám đông, tò mò hỏi thêm thông tin, comment ngắn theo trào lưu, hoặc meme hài hước mang tính Viral thúc đẩy tương tác. Sinh ra 2 mẫu câu.
4. BRAND_RECALL (Nhắc nhớ thương hiệu): Tập trung khen ngợi chất lượng trực tiếp của {$scenario->brand_name}, chia sẻ trải nghiệm thực tế tích cực của bản thân để người khác ghi nhớ tên sản phẩm một cách chân thực nhất. Sinh ra 2 mẫu câu.

Vui lòng trả về kết quả dưới dạng JSON có cấu trúc sau (không được giải thích gì thêm, chỉ trả về chuỗi JSON thô):
{
  \"tham\": [\"bình luận 1\", \"bình luận 2\"],
  \"san\": [\"bình luận 1\", \"bình luận 2\"],
  \"si\": [\"bình luận 1\", \"bình luận 2\"],
  \"brand_recall\": [\"bình luận 1\", \"bình luận 2\"]
}";

        try {
            // Call Gemini API (using gemini-2.5-flash)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Clean markdown response if any
                $text = trim($text);
                if (str_starts_with($text, '```json')) {
                    $text = substr($text, 7);
                }
                if (str_ends_with($text, '```')) {
                    $text = substr($text, 0, -3);
                }
                $text = trim($text);

                $data = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    return $data;
                }
            }

            Log::warning('Gemini API request failed or returned invalid JSON. Using mock fallback.', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
        }

        return self::generateMockComments($scenario);
    }

    private static function generateMockComments(SeedingScenario $scenario): array
    {
        $brand = $scenario->brand_name;
        $contextLower = mb_strtolower($scenario->context);

        // Custom generation based on keywords
        if (str_contains($contextLower, 'đắt') || str_contains($contextLower, 'giá cao') || str_contains($contextLower, 'tiền')) {
            return [
                'tham' => [
                    "Nghe nói bên {$brand} đang có mã giảm giá 30% cho khách hàng mới đó mọi người ơi, tính ra rẻ hơn nhiều á!",
                    "Ai chê đắt chứ mình thấy mua combo trên Shopee của {$brand} được tặng thêm quà với freeship nữa, tính ra hời cực kì."
                ],
                'san' => [
                    "Đắt xắt ra miếng chứ dùng mấy cái loại rẻ tiền trôi nổi trên mạng rồi mang họa vào thân, lúc đó lại kêu.",
                    "So sánh {$brand} với mấy hãng cỏ rẻ tiền thì khập khiễng quá. Tiền nào của nấy thôi, chất lượng dịch vụ người ta thế cơ mà."
                ],
                'si' => [
                    "Ủa thấy hot rần rần trên Tiktok bữa giờ nè, có ai xài thật chưa cho xin review công tâm đi ạ?",
                    "Nhìn bao bì sang xịn mịn ghê, thấy mọi người đua nhau mua làm mình cũng nôn nao muốn đặt thử ghê á."
                ],
                'brand_recall' => [
                    "Nhà mình xài sản phẩm của {$brand} được hơn 1 năm nay rồi, cực kì ưng ý và sẽ tiếp tục ủng hộ sản phẩm này.",
                    "Cứ nhắc tới đồ chất lượng là đầu mình nảy số ra {$brand} liền, đắt tí nhưng xài bền và yên tâm lắm."
                ]
            ];
        }

        if (str_contains($contextLower, 'lỗi') || str_contains($contextLower, 'phốt') || str_contains($contextLower, 'chậm') || str_contains($contextLower, 'kém')) {
            return [
                'tham' => [
                    "Bên {$brand} mới thông báo đền bù voucher 50k cho những đơn hàng bị trễ đó, thôi thì cũng có chút an ủi.",
                    "Lỗi tí nhưng được hoàn tiền 100% kèm quà xin lỗi thế này thì khách hàng lại lời quá rồi còn gì."
                ],
                'san' => [
                    "Hãng lớn làm ăn cả triệu đơn thì thi thoảng lỗi 1 đơn là bình thường, làm gì mà mấy ông đối thủ vào dìm hàng ghê thế?",
                    "Người ta lỗi nhưng có trách nhiệm xử lý đổi trả ngay lập tức, chứ không như mấy hãng khác im lặng block khách."
                ],
                'si' => [
                    "Ủa phốt gì vậy mọi người hóng với, thấy thương hiệu {$brand} này nổi tiếng xưa giờ mà ta?",
                    "Chấm hóng xem giải quyết thế nào, thấy cộng đồng mạng đang xôn xao chia sẻ quá trời."
                ],
                'brand_recall' => [
                    "Mình vẫn luôn tin dùng {$brand} vì chế độ bảo hành sản phẩm của họ luôn đi đầu thị trường.",
                    "Mọi người cứ yên tâm dùng {$brand} nhé, dịch vụ hỗ trợ khách hàng của họ xử lý nhanh lắm."
                ]
            ];
        }

        // General default generator
        return [
            'tham' => [
                "Đang có chương trình mua 1 tặng 1 của {$brand} kìa mọi người, nhanh tay mua đi kẻo hết slot ngon á!",
                "Săn sale đợt này của {$brand} giảm tận nửa giá luôn kìa, cơ hội trải nghiệm đồ xịn giá hời."
            ],
            'san' => [
                "Ai thích dùng đồ nhái thì cứ mua bên khác, còn mình thì chỉ tin tưởng hàng chính hãng của {$brand} thôi.",
                "Mấy người chê sản phẩm {$brand} chắc chưa bao giờ được dùng thử hàng cao cấp thật sự rồi."
            ],
            'si' => [
                "Review sản phẩm này thấy trên mạng rầm rộ quá, có ai mua chung để được gom freeship không nè?",
                "Trend mới của giới trẻ bây giờ là xài đồ {$brand} à mọi người, đi đâu cũng thấy nhắc luôn."
            ],
            'brand_recall' => [
                "Sản phẩm {$brand} dùng thực sự hiệu quả và đúng như quảng cáo, mình đã giới thiệu cho cả nhà dùng.",
                "Luôn là lựa chọn số một của mình khi cần tìm sản phẩm chất lượng cao chính là thương hiệu {$brand}."
            ]
        ];
    }

    public static function researchSalon(OutboundLead $lead): array
    {
        // Mocking Research Agent logic using Clay/Hunter
        return [
            'tech_stack' => ['Laravel', 'Tailwind', 'Livewire'],
            'estimated_revenue' => '$10k - $50k',
            'recent_news' => 'Just launched a new UI update.',
            'decision_maker' => 'John Doe (CEO)',
            'pain_points' => ['Low conversion rate', 'High bounce rate'],
        ];
    }

    public static function generateOutboundDrafts(OutboundLead $lead, array $researchData): string
    {
        // Mocking the Message Factory (Gemini integration would go here)
        $brand = $lead->salon_name ?: 'Your Company';
        $dm = $researchData['decision_maker'] ?? 'there';
        
        return "Hi {$dm},\n\nI noticed {$brand} recently launched a new UI update and is using Laravel. We help companies like yours improve conversion rates.\n\nWould you be open to a quick chat next week?";
    }
}
