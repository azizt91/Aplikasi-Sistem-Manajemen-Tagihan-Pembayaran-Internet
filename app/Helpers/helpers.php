<?php

if (!function_exists('rupiah')) {
    function rupiah($nilai)
    {
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
}

if (!function_exists('settings')) {
    function settings($key, $default = null)
    {
        $setting = \App\Models\Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}

