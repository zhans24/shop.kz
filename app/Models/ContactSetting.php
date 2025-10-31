<?php

namespace App\Models;

use App\Services\ContactNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_text',
        'phones',
        'email',
        'whatsapp',
        'tiktok',
        'facebook',
        'youtube',
        'telegram',
        'address',
        'map_embed',
    ];

    protected $casts = [
        'phones' => 'array',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'company_name' => null,
            'company_text' => null,
            'phones'       => [],
        ]);
    }

    protected static function booted(): void
    {
        static::saving(function (self $m) {
            $m->phones   = ContactNormalizer::limitPhones($m->phones ?? []);
            $m->facebook = ContactNormalizer::facebook($m->facebook ?? null);
            $m->tiktok   = ContactNormalizer::tiktok($m->tiktok ?? null);
            $m->youtube  = ContactNormalizer::youtube($m->youtube ?? null);
            $m->telegram = ContactNormalizer::telegram($m->telegram ?? null);
            $m->whatsapp = ContactNormalizer::whatsappLink($m->whatsapp ?? null);
        });

        static::saved(fn () => cache()->forget('site.contacts'));
        static::deleted(fn () => cache()->forget('site.contacts'));
    }

    public function getWhatsappDigitsAttribute(): ?string
    {
        return ContactNormalizer::extractWhatsappDigits($this->whatsapp);
    }
}
