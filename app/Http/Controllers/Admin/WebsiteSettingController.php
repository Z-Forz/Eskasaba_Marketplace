<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteSettingRequest;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    /**
     * Tampilkan form edit website settings.
     */
    public function edit()
    {
        $settings = WebsiteSetting::allSettings();

        return view('admin.settings.edit', compact('settings'));
    }

    /**
     * Update website settings.
     */
    public function update(WebsiteSettingRequest $request)
    {
        $data = $request->validated();

        // Upload logo baru kalau ada file yang dikirim
        if ($request->hasFile('logo')) {
            $this->deleteOldFile(WebsiteSetting::get('logo'));
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        } else {
            unset($data['logo']);
        }

        // Upload hero_image baru kalau ada file yang dikirim
        if ($request->hasFile('hero_image')) {
            $this->deleteOldFile(WebsiteSetting::get('hero_image'));
            $data['hero_image'] = $request->file('hero_image')->store('settings', 'public');
        } else {
            unset($data['hero_image']);
        }

        foreach ($data as $key => $value) {
            WebsiteSetting::set($key, $value);
        }

        return redirect()
            ->back()
            ->with('success', 'Pengaturan website berhasil diperbarui.');
    }

    /**
     * Hapus file lama dari storage sebelum diganti file baru.
     */
    private function deleteOldFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}