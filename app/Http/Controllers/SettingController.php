<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function edit()
    {
        return view('settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'whatsapp_number' => 'nullable|string|max:20',
            'app_link' => 'nullable|url|max:255',
            'pwa_short_name' => 'nullable|string|max:20',
            'pwa_description' => 'nullable|string|max:255',
            'pwa_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // PENGATURAN UMUM
        if ($request->app_name) {
            Setting::updateOrCreate(['key' => 'app_name'], ['value' => $request->app_name]);
            // Also update legacy keys for backward compatibility
            Setting::updateOrCreate(['key' => 'app_name_admin'], ['value' => $request->app_name]);
            Setting::updateOrCreate(['key' => 'app_name_pelanggan'], ['value' => $request->app_name]);
            Setting::updateOrCreate(['key' => 'sidebar_text'], ['value' => $request->app_name]);
            Setting::updateOrCreate(['key' => 'pwa_name'], ['value' => $request->app_name]);
        }

        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('public/logos');
            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $logoPath]);
            // Also update legacy keys for backward compatibility
            Setting::updateOrCreate(['key' => 'logo_admin'], ['value' => $logoPath]);
            Setting::updateOrCreate(['key' => 'logo_pelanggan'], ['value' => $logoPath]);
            Setting::updateOrCreate(['key' => 'sidebar_logo'], ['value' => $logoPath]);
        }

        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('public/icons');
            Setting::updateOrCreate(['key' => 'favicon'], ['value' => $faviconPath]);
        }

        if ($request->whatsapp_number) {
            Setting::updateOrCreate(['key' => 'whatsapp_number'], ['value' => $request->whatsapp_number]);
        }

        if ($request->app_link) {
            Setting::updateOrCreate(['key' => 'app_link'], ['value' => $request->app_link]);
        }

        // PENGATURAN PELANGGAN
        if ($request->customer_id_prefix) {
            Setting::updateOrCreate(['key' => 'customer_id_prefix'], ['value' => strtoupper($request->customer_id_prefix)]);
        }

        if ($request->customer_email_prefix) {
            Setting::updateOrCreate(['key' => 'customer_email_prefix'], ['value' => strtolower($request->customer_email_prefix)]);
        }

        if ($request->customer_email_domain) {
            Setting::updateOrCreate(['key' => 'customer_email_domain'], ['value' => strtolower($request->customer_email_domain)]);
        }

        if ($request->customer_default_password) {
            Setting::updateOrCreate(['key' => 'customer_default_password'], ['value' => $request->customer_default_password]);
        }

        // PENGATURAN PWA
        if ($request->pwa_short_name) {
            Setting::updateOrCreate(['key' => 'pwa_short_name'], ['value' => $request->pwa_short_name]);
        }

        if ($request->pwa_description) {
            Setting::updateOrCreate(['key' => 'pwa_description'], ['value' => $request->pwa_description]);
        }

        if ($request->hasFile('pwa_logo')) {
            $pwaLogoPath = $request->file('pwa_logo')->store('public/logos');
            Setting::updateOrCreate(['key' => 'pwa_logo'], ['value' => $pwaLogoPath]);
        }

        // Update manifest.json for PWA
        $manifestPath = public_path('manifest.json');
        if (File::exists($manifestPath)) {
            $manifest = json_decode(File::get($manifestPath), true);
            $manifest['name'] = $request->app_name ?? ($manifest['name'] ?? 'App');
            $manifest['short_name'] = $request->pwa_short_name ?? ($manifest['short_name'] ?? 'App');
            $manifest['description'] = $request->pwa_description ?? ($manifest['description'] ?? '');

            if (isset($pwaLogoPath)) {
                $manifest['icons'][0]['src'] = Storage::url($pwaLogoPath);
            }
            File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        }

        Alert::success('Sukses', 'Pengaturan berhasil diperbarui');
        return redirect()->route('settings.edit');
    }
}
