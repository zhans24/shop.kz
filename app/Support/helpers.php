<?php

use App\Models\ContactSetting;
use App\Services\ContactNormalizer;

if (! function_exists('site_contacts')) {
    function site_contacts(): array {
        return cache()->remember('site.contacts', 86400, function () {
            $s = ContactSetting::singleton();

            $phones = collect($s->phones ?? [])
                ->map(function ($p) {
                    $raw = is_array($p) ? ($p['raw'] ?? $p['number'] ?? '') : (string) $p;
                    return [
                        'label' => is_array($p) ? ($p['label'] ?? null) : null,
                        'raw'   => $raw ?: null,
                        'tel'   => ContactNormalizer::telHrefFromRaw($raw),
                    ];
                })
                ->filter(fn ($p) => $p['raw'] && $p['tel'])
                ->take(2)
                ->values()
                ->all();

            return [
                'company_name' => $s->company_name,
                'company_text' => $s->company_text,
                'phones'       => $phones,
                'email'        => $s->email,
                'whatsapp'     => $s->whatsapp,
                'facebook'     => $s->facebook,
                'tiktok'       => $s->tiktok,
                'youtube'      => $s->youtube,
                'telegram'     => $s->telegram,
                'address'      => $s->address,
                'map_embed'    => $s->map_embed,
            ];
        });
    }
}
