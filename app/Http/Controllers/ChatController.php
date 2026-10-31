<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

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

        // Ignore empty messages and cap the length
        if ($message === '') {
            return response()->json(['reply' => 'Please type a question first.']);
        }
        $message = mb_substr($message, 0, 500);

        // Per-user limits: 5 messages per minute, 20 per day
        $userId = auth()->id();

        if (RateLimiter::tooManyAttempts("chat-min:$userId", 5)) {
            return response()->json([
                'reply' => '⏳ Slow down! Please wait a minute before asking again.'
            ]);
        }

        if (RateLimiter::tooManyAttempts("chat-day:$userId", 20)) {
            return response()->json([
                'reply' => '⚠️ You reached your daily AI chat limit. Please try again tomorrow.'
            ]);
        }

        RateLimiter::hit("chat-min:$userId", 60);
        RateLimiter::hit("chat-day:$userId", 86400);

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
                'maxOutputTokens' => 1024,
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
        // Tries each model in order until one works
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
                    ->timeout(8)
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        $payload
                    );
            } catch (\Throwable $e) {
                \Log::warning("Gemini {$model} connection error: " . $e->getMessage());
                continue; // Timed out or connection failed -> try next model
            }

            if ($res->successful()) {
                $json = $res->json();
                
                // Log which model actually answered
                \Log::warning("AI answered using: " . ($json['modelVersion'] ?? $model));
                
                return $json;
            }

            \Log::warning("Gemini {$model} failed", [
                'status' => $res->status(),
                'body' => $res->body(),
            ]);

            // Auth error or bad request: another model won't fix it, so stop
            if (in_array($res->status(), [400, 401, 403])) {
                break;
            }

            // 404, 429, 500, 503... try the next model
        }

        return null; // All models failed
    }
}