<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenAiService
{
    public function generateText(string $instructions, string $input): string
    {
        $apiKey = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-5.4-mini');

        if (!$apiKey) {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => $model,
                'instructions' => $instructions,
                'input' => $input,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException(
                'OpenAI request failed: ' . $response->status() . ' ' . $response->body()
            );
        }

        $data = $response->json();

        $text = $this->extractText($data);

        if ($text === '') {
            Log::warning('OpenAI returned empty response', [
                'data' => $data,
            ]);

            throw new RuntimeException('OpenAI returned an empty response.');
        }

        return $text;
    }

    protected function extractText(array $data): string
    {
        if (!empty($data['output_text'])) {
            return trim((string) $data['output_text']);
        }

        if (!empty($data['output'][0]['content'])) {
            foreach ($data['output'][0]['content'] as $contentItem) {
                if (($contentItem['type'] ?? null) === 'output_text' && !empty($contentItem['text'])) {
                    return trim((string) $contentItem['text']);
                }

                if (!empty($contentItem['text'])) {
                    return trim((string) $contentItem['text']);
                }
            }
        }

        return '';
    }
}
