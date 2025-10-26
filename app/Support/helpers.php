<?php

use App\Models\ContactSetting;

if (! function_exists('site_contacts')) {
    function site_contacts(): ContactSetting
    {
        return cache()->remember('site.contacts', 900, fn () => ContactSetting::singleton());
    }
}

if (! function_exists('tel_href')) {
    function tel_href(string $phone): string
    {
        $digits = preg_replace('~\D+~', '', $phone) ?? '';
        if ($digits && $digits[0] !== '+') {
            if (str_starts_with($digits, '8')) {
                $digits = '7' . substr($digits, 1);
            }
            $digits = '+' . $digits;
        }
        return 'tel:' . $digits;
    }
}

