<?php

namespace App\Http\Controllers;

use App\Models\CauHinhAi;
use Illuminate\Http\Request;

class AIConfigController extends Controller
{
    private const TYPE_PROMPT = 'prompt_mac_dinh';
    private const TYPE_GEMINI_API_KEY = 'gemini_api_key';
    private const TYPE_PEXELS_API_KEY = 'pexels_api_key';
    private const TYPE_GEMINI_MODEL_FALLBACKS = 'gemini_model_fallbacks';
    private const DEFAULT_GEMINI_MODELS = 'gemini-3.0-flash,gemini-3.1-pro,gemini-3.0-pro';

    private string $defaultPrompt = <<<'PROMPT'
Bạn là chuyên gia thiết kế lịch trình du lịch tại Việt Nam.

Yêu cầu:
- Điểm đến: {diemDen}
- Số ngày: {soNgay}
- Ngân sách: {nganSach}
- Sở thích bổ sung: {soThich}

Tôi đang có danh sách địa điểm trong hệ thống:
{dbJson}

Nhiệm vụ:
- Lập lịch trình {soNgay} ngày, mỗi ngày gồm 3 hoạt động: BUỔI SÁNG, BUỔI CHIỀU, BUỔI TỐI.
- Ưu tiên dùng địa điểm trong hệ thống.
- Nếu tự thêm địa điểm ngoài hệ thống thì đặt co_trong_db = false và hinhanh để trống.
- Nếu địa điểm nằm ngoài Việt Nam thì trả về JSON: {"error":"Điểm đến không hỗ trợ"}.

Bắt buộc trả về đúng một JSON array theo mẫu:
[
  {
    "tieuDe": "Ngày 1: ...",
    "thoiGian": "Thứ hai",
    "danhSachHoatDong": [
      {
        "buoi": "BUỔI SÁNG",
        "iconClass": "icon--morning",
        "icon": "fas fa-sun",
        "tieuDe": "...",
        "moTa": "...",
        "hinhanh": "",
        "co_trong_db": false,
        "gia": "Miễn phí",
        "thoiLuong": "3 giờ"
      }
    ]
  }
]
PROMPT;

    /**
     * Get current default prompt.
     */
    public function getPrompt()
    {
        $config = CauHinhAi::where('loai', self::TYPE_PROMPT)->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'prompt' => $config ? $config->noi_dung : $this->defaultPrompt,
            ],
        ]);
    }

    /**
     * Update default prompt.
     */
    public function updatePrompt(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:50',
        ]);

        $config = CauHinhAi::updateOrCreate(
            ['loai' => self::TYPE_PROMPT],
            ['noi_dung' => $request->prompt]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật cấu hình AI thành công.',
            'data' => $config,
        ]);
    }

    /**
     * Get API configuration for admin UI.
     * Never return raw API keys to frontend.
     */
    public function getApiConfig()
    {
        $geminiApiKey = $this->getConfigValue(
            self::TYPE_GEMINI_API_KEY,
            trim((string) env('GEMINI_API_KEY', ''))
        );

        $pexelsApiKey = $this->getConfigValue(
            self::TYPE_PEXELS_API_KEY,
            trim((string) env('PEXELS_API_KEY', ''))
        );

        $geminiModelFallbacks = $this->normalizeModelFallbacks(
            $this->getConfigValue(
                self::TYPE_GEMINI_MODEL_FALLBACKS,
                trim((string) env('GEMINI_MODEL_FALLBACKS', self::DEFAULT_GEMINI_MODELS))
            )
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'hasGeminiApiKey' => $geminiApiKey !== '',
                'hasPexelsApiKey' => $pexelsApiKey !== '',
                'geminiApiKeyMasked' => $this->maskSecret($geminiApiKey),
                'pexelsApiKeyMasked' => $this->maskSecret($pexelsApiKey),
                'geminiModelFallbacks' => $geminiModelFallbacks,
            ],
        ]);
    }

    /**
     * Update API configuration from admin UI.
     * Empty API key values are ignored (keep current values).
     */
    public function updateApiConfig(Request $request)
    {
        $request->validate([
            'geminiApiKey' => 'nullable|string|min:20|max:500',
            'pexelsApiKey' => 'nullable|string|min:20|max:500',
            'geminiModelFallbacks' => 'nullable|string|max:500',
        ]);

        $geminiApiKey = trim((string) $request->input('geminiApiKey', ''));
        $pexelsApiKey = trim((string) $request->input('pexelsApiKey', ''));
        $geminiModelFallbacks = trim((string) $request->input('geminiModelFallbacks', ''));

        if ($geminiApiKey !== '') {
            CauHinhAi::updateOrCreate(
                ['loai' => self::TYPE_GEMINI_API_KEY],
                ['noi_dung' => $geminiApiKey]
            );
        }

        if ($pexelsApiKey !== '') {
            CauHinhAi::updateOrCreate(
                ['loai' => self::TYPE_PEXELS_API_KEY],
                ['noi_dung' => $pexelsApiKey]
            );
        }

        if ($geminiModelFallbacks !== '') {
            CauHinhAi::updateOrCreate(
                ['loai' => self::TYPE_GEMINI_MODEL_FALLBACKS],
                ['noi_dung' => $this->normalizeModelFallbacks($geminiModelFallbacks)]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật cấu hình API thành công.',
            'data' => [
                'hasGeminiApiKey' => ($geminiApiKeyCurrent = $this->getConfigValue(
                    self::TYPE_GEMINI_API_KEY,
                    trim((string) env('GEMINI_API_KEY', ''))
                )) !== '',
                'hasPexelsApiKey' => ($pexelsApiKeyCurrent = $this->getConfigValue(
                    self::TYPE_PEXELS_API_KEY,
                    trim((string) env('PEXELS_API_KEY', ''))
                )) !== '',
                'geminiApiKeyMasked' => $this->maskSecret($geminiApiKeyCurrent),
                'pexelsApiKeyMasked' => $this->maskSecret($pexelsApiKeyCurrent),
                'geminiModelFallbacks' => $this->normalizeModelFallbacks(
                    $this->getConfigValue(
                        self::TYPE_GEMINI_MODEL_FALLBACKS,
                        trim((string) env('GEMINI_MODEL_FALLBACKS', self::DEFAULT_GEMINI_MODELS))
                    )
                ),
            ],
        ]);
    }

    private function getConfigValue(string $type, string $default = ''): string
    {
        $value = CauHinhAi::where('loai', $type)->value('noi_dung');

        if (! is_string($value) || trim($value) === '') {
            return trim($default);
        }

        return trim($value);
    }

    private function normalizeModelFallbacks(string $raw): string
    {
        $models = array_values(array_unique(array_filter(array_map(
            'trim',
            explode(',', $raw)
        ))));

        if ($models === []) {
            $models = explode(',', self::DEFAULT_GEMINI_MODELS);
        }

        return implode(',', $models);
    }

    private function maskSecret(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return '';
        }

        $length = strlen($raw);
        if ($length <= 8) {
            return str_repeat('*', max(4, $length));
        }

        return substr($raw, 0, 4) . str_repeat('*', max(4, $length - 8)) . substr($raw, -4);
    }
}
