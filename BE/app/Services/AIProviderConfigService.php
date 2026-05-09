<?php

namespace App\Services;

use App\Models\CauHinhAi;

class AIProviderConfigService
{
    public const TYPE_PROMPT = 'prompt_mac_dinh';
    public const TYPE_GEMINI_API_KEY = 'gemini_api_key';
    public const TYPE_PEXELS_API_KEY = 'pexels_api_key';
    public const TYPE_GEMINI_MODEL_FALLBACKS = 'gemini_model_fallbacks';
    public const DEFAULT_GEMINI_MODELS = 'gemini-3.0-flash,gemini-3.1-pro,gemini-3.0-pro';

    public function getPromptTemplate(string $defaultPrompt): string
    {
        return $this->getConfigValue(self::TYPE_PROMPT, $defaultPrompt);
    }

    public function getGeminiApiKey(): string
    {
        return $this->getConfigValue(
            self::TYPE_GEMINI_API_KEY,
            trim((string) env('GEMINI_API_KEY', ''))
        );
    }

    public function getGeminiApiKeys(): array
    {
        $keys = [
            $this->getGeminiApiKey(),
            trim((string) env('GEMINI_API_KEY_2', '')),
        ];

        return array_values(array_filter(array_unique(array_map('trim', $keys))));
    }

    public function getPexelsApiKey(): string
    {
        return $this->getConfigValue(
            self::TYPE_PEXELS_API_KEY,
            trim((string) env('PEXELS_API_KEY', ''))
        );
    }

    public function getGeminiModels(): array
    {
        $rawModels = $this->getConfigValue(
            self::TYPE_GEMINI_MODEL_FALLBACKS,
            trim((string) env('GEMINI_MODEL_FALLBACKS', self::DEFAULT_GEMINI_MODELS))
        );

        $modelsFromEnv = array_filter(array_map('trim', explode(',', $rawModels)));
        $defaultModels = array_filter(array_map('trim', explode(',', self::DEFAULT_GEMINI_MODELS)));
        $models = array_values(array_unique(array_merge($modelsFromEnv, $defaultModels)));

        return $models === [] ? ['gemini-3.0-flash'] : $models;
    }

    public function getConfigValue(string $type, string $default = ''): string
    {
        $stored = CauHinhAi::query()->where('loai', $type)->value('noi_dung');
        if (is_string($stored) && trim($stored) !== '') {
            return trim($stored);
        }

        return trim($default);
    }
}
