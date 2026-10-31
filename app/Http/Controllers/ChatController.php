<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        // Only students and instructors can use the chatbot
        if (!in_array(auth()->user()->role, ['student', 'instructor'])) {
            return response()->json([
                'reply' => '👋 The AI chatbot is available for students and instructors only.'
            ]);
        }

        $message = trim($request->input('message'));

        $systemPrompt = "You are SensorsHub AI. Rules:
- For greetings like 'hello' or 'hi', respond with a friendly one-sentence greeting and ask what they want to learn.
- For real questions, answer directly in 1-3 sentences without greetings.
- Only provide code if explicitly asked for it.
- When providing code, ALWAYS put it in a code block with proper indentation and line breaks.
- If unrelated to electronics/microcontrollers/sensors, say 'I can only help with electronics topics.'";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nQuestion:\n" . $message]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 300,
            ],
        ];

        $data = $this->callGemini($payload);

        if ($data === null) {
            return response()->json([
                'reply' => '⚠️ AI service is busy right now. Please try again in a moment.'
            ]);
        }

        $reply = $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'Sorry, I could not generate a response.';

        return response()->json(['reply' => $reply]);
    }

    private function callGemini(array $payload): ?array
    {
        // Fallback order: try fastest/working models first
        $models = [
            'gemini-3.5-flash-lite',
            'gemini-flash-lite-latest',
            'gemini-3-flash-preview',
            'gemini-3.1-flash-lite-preview',
        ];

        $apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');

        foreach ($models as $model) {
            try {
                $res = Http::withOptions(['verify' => false])
                    ->timeout(10) // Wait max 10 seconds before giving up
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        $payload
                    );
            } catch (\Throwable $e) {
                \Log::warning("Gemini {$model} connection error: " . $e->getMessage());
                continue; // Timed out or connection failed -> try next model
            }

            if ($res->successful()) {
                return $res->json();
            }

            // Log the failure
            \Log::warning("Gemini {$model} failed", [
                'status' => $res->status(),
                'body' => $res->body(),
            ]);

            // If it's an auth error (403/401) or bad request (400), 
            // trying another model won't fix it. Stop immediately.
            if (in_array($res->status(), [400, 401, 403])) {
                break;
            }
            
            // If it's 404, 429, 500, 503... fall through and try the next model
        }

        return null; // All models failed
    }
}