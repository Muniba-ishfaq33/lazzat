<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'mode' => ['nullable', 'string', 'max:40'],
            'context' => ['nullable', 'array'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*.mimeType' => ['required_with:images', 'string', 'in:image/jpeg,image/png,image/webp'],
            'images.*.data' => ['required_with:images', 'string', 'max:6000000'],
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'Gemini API key is not configured yet. Add GEMINI_API_KEY to your Laravel .env file, then reload the app.',
                'needs_key' => true,
            ]);
        }

        $parts = $this->buildParts(
            mode: $validated['mode'] ?? 'ask',
            message: $validated['message'],
            context: $validated['context'] ?? [],
            images: $validated['images'] ?? [],
        );

        $response = Http::timeout(30)
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'contents' => [[
                    'role' => 'user',
                    'parts' => $parts,
                ]],
                'generationConfig' => [
                    'temperature' => 0.75,
                    'maxOutputTokens' => 1200,
                ],
            ]);

        if (!$response->successful()) {
            return response()->json([
                'reply' => 'I could not reach Gemini right now. Please check your API key, model name, or internet connection.',
                'error' => $response->json('error.message') ?? $response->body(),
            ]);
        }

        return response()->json([
            'reply' => $response->json('candidates.0.content.parts.0.text')
                ?? 'I did not receive a clear response. Please try again.',
        ]);
    }

    private function buildParts(string $mode, string $message, array $context, array $images): array
    {
        $systemPrompt = <<<PROMPT
You are Lazzat AI, a warm Pakistani food expert inside a Laravel recipe planner.
Reply in the same language as the user when possible, including Urdu.
Be practical, concise, and food-safe. Prefer Pakistani recipes and familiar ingredients.

Modes:
- ask: answer Pakistani recipe, cooking, food culture, and nutrition questions.
- meal-plan: generate a personalized 7-day Pakistani breakfast/lunch/dinner meal plan using the user's goal, diet, and cuisine preferences.
- ingredients: suggest dishes from ingredients the user has, plus missing ingredients.
- nutrition: give general nutrition guidance for Pakistani food. Do not diagnose disease; recommend a doctor for medical conditions.

User app context may include saved recipes, favorites, planned meals, and grocery items. Use it when relevant.
PROMPT;

        $contextText = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $parts = [[
            'text' => "{$systemPrompt}\n\nMode: {$mode}\nUser app context JSON: {$contextText}\n\nUser message:\n{$message}",
        ]];

        foreach ($images as $image) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $image['mimeType'],
                    'data' => $image['data'],
                ],
            ];
        }

        return $parts;
    }
}
