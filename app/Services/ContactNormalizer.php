<?php

namespace App\Services;

final class ContactNormalizer
{
    public static function onlyDigits(?string $v): ?string
    {
        if (!is_string($v)) return null;
        $d = preg_replace('~\D+~', '', $v);
        return $d !== '' ? $d : null;
    }

    public static function whatsappLink(?string $input): ?string
    {
        if (!$input) return null;

        if (preg_match('~^https?://~i', $input)) {
            $digits = self::onlyDigits($input);
            return $digits ? "https://wa.me/{$digits}" : $input;
        }

        $digits = self::onlyDigits($input);
        return $digits ? "https://wa.me/{$digits}" : null;
    }

    public static function normalizeHandle(string $v): string
    {
        $v = trim($v);
        $v = preg_replace('~^https?://~i', '', $v);
        return ltrim($v, '@/');
    }

    public static function facebook(?string $v): ?string
    {
        if (!$v) return null;
        if (preg_match('~^https?://~i', $v)) return $v;
        return 'https://facebook.com/' . self::normalizeHandle($v);
    }

    public static function tiktok(?string $v): ?string
    {
        if (!$v) return null;
        if (preg_match('~^https?://~i', $v)) return $v;
        return 'https://www.tiktok.com/@' . self::normalizeHandle($v);
    }

    public static function youtube(?string $v): ?string
    {
        if (!$v) return null;
        if (preg_match('~^https?://~i', $v)) return $v;
        return 'https://youtube.com/@' . self::normalizeHandle($v);
    }

    public static function telegram(?string $v): ?string
    {
        if (!$v) return null;
        if (preg_match('~^https?://~i', $v)) return $v;
        return 'https://t.me/' . self::normalizeHandle($v);
    }



    public static function telHrefFromRaw(?string $raw): ?string
    {
        $digits = self::onlyDigits($raw);
        return $digits ? "tel:+{$digits}" : null;
    }

    public static function limitPhones(?array $phones): array
    {
        return collect($phones ?? [])
            ->take(2)
            ->map(function ($row) {
                $raw = is_array($row) ? ($row['raw'] ?? null) : (string) $row;
                $raw = is_string($raw) ? trim($raw) : null;
                if ($raw === null || $raw === '') return null;

                return [
                    'raw' => $raw,
                    'tel' => self::telHrefFromRaw($raw),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function extractWhatsappDigits(?string $link): ?string
    {
        return self::onlyDigits($link);
    }
}
