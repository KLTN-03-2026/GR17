<?php

namespace App\Http\Controllers;

use App\Models\CauHinhAi;
use Illuminate\Http\Request;
use App\Services\AITourGuideService;

class AIConfigController extends Controller
{
    /**
     * Get current default prompt.
     */
    public function getPrompt(AITourGuideService $aiService)
    {
        $config = CauHinhAi::where('loai', 'prompt_mac_dinh')->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'prompt' => $config ? $config->noi_dung : $aiService->getDefaultPrompt(),
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
            ['loai' => 'prompt_mac_dinh'],
            ['noi_dung' => $request->prompt]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật cấu hình Prompt AI thành công.',
            'data' => $config,
        ]);
    }

    /**
     * Get API config (Mock for API compatibility)
     */
    public function getApiConfig()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'hasGeminiApiKey' => env('GEMINI_API_KEY') !== null,
                'hasPexelsApiKey' => false,
                'geminiApiKeyMasked' => env('GEMINI_API_KEY') ? '********' : '',
                'pexelsApiKeyMasked' => '',
                'geminiModelFallbacks' => 'gemini-2.5-flash',
            ],
        ]);
    }

    /**
     * Update API Config (Now handled by .env directly)
     */
    public function updateApiConfig(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật API keys giờ được quản lý trực tiếp qua file .env. Vui lòng liên hệ Admin hệ thống.',
        ]);
    }
}
