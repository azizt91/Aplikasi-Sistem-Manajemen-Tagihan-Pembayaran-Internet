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
                'favicon' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'logo_admin' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'logo_pelanggan' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'sidebar_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'receipt_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'app_name_admin' => 'string|max:255',
                'app_name_pelanggan' => 'string|max:255',
                'sidebar_text' => 'string|max:255',
                'company_address' => 'string|max:255',
                'whatsapp_number' => 'string|max:255',
                'pwa_name' => 'string|max:255',
                'pwa_short_name' => 'string|max:255',
                'pwa_description' => 'string|max:255',
                'pwa_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('public/icons');
                Setting::updateOrCreate(['key' => 'favicon'], ['value' => $faviconPath]);
            }

            if ($request->hasFile('logo_admin')) {
                $logoAdminPath = $request->file('logo_admin')->store('public/logos');
                Setting::updateOrCreate(['key' => 'logo_admin'], ['value' => $logoAdminPath]);
            }

            if ($request->hasFile('logo_pelanggan')) {
                $logoPelangganPath = $request->file('logo_pelanggan')->store('public/logos');
                Setting::updateOrCreate(['key' => 'logo_pelanggan'], ['value' => $logoPelangganPath]);
            }

            if ($request->hasFile('sidebar_logo')) {
                $sidebarLogoPath = $request->file('sidebar_logo')->store('public/logos');
                Setting::updateOrCreate(['key' => 'sidebar_logo'], ['value' => $sidebarLogoPath]);
            }

            if ($request->hasFile('receipt_logo')) {
                $receiptLogoPath = $request->file('receipt_logo')->store('public/logos');
                Setting::updateOrCreate(['key' => 'receipt_logo'], ['value' => $receiptLogoPath]);
            }

            Setting::updateOrCreate(['key' => 'app_name_admin'], ['value' => $request->app_name_admin]);
            Setting::updateOrCreate(['key' => 'app_name_pelanggan'], ['value' => $request->app_name_pelanggan]);
            Setting::updateOrCreate(['key' => 'sidebar_text'], ['value' => $request->sidebar_text]);
            Setting::updateOrCreate(['key' => 'company_address'], ['value' => $request->company_address]);
            Setting::updateOrCreate(['key' => 'whatsapp_number'], ['value' => $request->whatsapp_number]);

            // Tambahkan kode ini untuk menyimpan pengaturan PWA
            Setting::updateOrCreate(['key' => 'pwa_name'], ['value' => $request->pwa_name]);
            Setting::updateOrCreate(['key' => 'pwa_short_name'], ['value' => $request->pwa_short_name]);
            Setting::updateOrCreate(['key' => 'pwa_description'], ['value' => $request->pwa_description]);

            if ($request->hasFile('pwa_logo')) {
                $pwaLogoPath = $request->file('pwa_logo')->store('public/logos');
                Setting::updateOrCreate(['key' => 'pwa_logo'], ['value' => $pwaLogoPath]);
            }

            // Update manifest.json
            $manifest = json_decode(File::get(public_path('manifest.json')), true);
            $manifest['name'] = $request->pwa_name;
            $manifest['short_name'] = $request->pwa_short_name;
            $manifest['description'] = $request->pwa_description;

            if (isset($pwaLogoPath)) {
                $manifest['icons'][0]['src'] = Storage::url($pwaLogoPath);
            }
            File::put(public_path('manifest.json'), json_encode($manifest, JSON_PRETTY_PRINT));

            Alert::toast('Pengaturan berhasil diperbarui', 'success');
            return redirect()->route('settings.edit');
        }
    }



