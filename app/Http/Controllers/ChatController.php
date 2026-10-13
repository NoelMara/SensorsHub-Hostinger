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

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key=' . env('GEMINI_API_KEY'),
            [
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
            ]
        );

        $data = $response->json();

        if (!$response->successful()) {
            return response()->json([
                'reply' => '⚠️ AI service is temporarily unavailable. Please try again later.'
            ]);
        }

        $reply = $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'Sorry, I could not generate a response.';

        return response()->json(['reply' => $reply]);
    }
}