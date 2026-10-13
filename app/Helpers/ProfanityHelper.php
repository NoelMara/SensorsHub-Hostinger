<?php

namespace App\Helpers;

class ProfanityHelper
{
    public static function check(string $text): ?string
    {
        $words = config('blacklist.profanity', []);

        if (empty($words)) {
            return null;
        }

        $text = strtolower($text);

        foreach ($words as $word) {
            $word = strtolower(trim($word));

            if ($word === '') {
                continue;
            }

            if (self::matches($text, $word)) {
                return $word;
            }
        }

        return null;
    }

    public static function has(string $text): bool
    {
        return self::check($text) !== null;
    }

    private static function matches(string $text, string $word): bool
    {
        $pattern = preg_quote($word, '/');

        if (preg_match('/(?<![a-z0-9])' . $pattern . '(?![a-z0-9])/i', $text)) {
            return true;
        }

        $normalizedText = self::normalize($text);
        $normalizedWord = self::normalize($word);

        if ($normalizedWord === '') {
            return false;
        }

        $normalizedText = self::collapseRepeats($normalizedText);

        return str_contains($normalizedText, $normalizedWord);
    }

    private static function normalize(string $text): string
    {
        $text = strtolower($text);

        $text = strtr($text, [
            '0' => 'o',
            '1' => 'i',
            '2' => 'z',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '6' => 'g',
            '7' => 't',
            '8' => 'b',
            '9' => 'g',
            '@' => 'a',
            '$' => 's',
        ]);

        $text = preg_replace('/[^a-z0-9]/', '', $text);

        return $text ?? '';
    }

    private static function collapseRepeats(string $text): string
    {
        return preg_replace('/(.)\1+/', '$1', $text) ?? $text;
    }
}