<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'instagram',
        'youtube',
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
        static::saved(fn () => cache()->forget('site.contacts'));
        static::deleted(fn () => cache()->forget('site.contacts'));
    }
}
