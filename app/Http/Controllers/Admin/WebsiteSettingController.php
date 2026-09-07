<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteSettingRequest;
use App\Models\WebsiteSetting;
use App\Services\ImageCompressor;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    /**
     * Tampilkan form edit website settings.
     */
    public function index()
    {
        $allSettings = WebsiteSetting::allSettings();

        // Convert key-value array to object with default fallbacks
        $settings = (object) array_merge([
            'website_name'     => 'Eskasaba Marketplace',
            'logo'             => null,
            'hero_title'       => 'Pasar Digital Produk Kreatif Sekolah Eskasaba',
            'hero_description' => 'Platform marketplace khusus warga SMK Kasuari Bangsa untuk mempromosikan dan menjual produk buatan siswa dan guru.',
            'hero_image'       => null,
            'about'            => 'Eskasaba Marketplace adalah wadah wirausaha berbasis sekolah.',
            'vision'           => 'Menciptakan ekosistem wirausaha mandiri bagi siswa.',
            'mission'          => 'Memberikan wadah pemasaran produk berkualitas buatan sekolah.',
            'address'          => 'Jl. Kasuari No. 1, Kota Kasuari',
            'email'            => 'info@eskasaba.sch.id',
            'phone'            => '081234567890',
            'instagram'        => 'https://instagram.com/eskasaba',
            'facebook'         => 'https://facebook.com/eskasaba',
            'tiktok'           => 'https://tiktok.com/@eskasaba',
            'copyright'        => '© ' . date('Y') . ' Eskasaba Marketplace. All Rights Reserved.',
        ], $allSettings);

        return view('admin.website-settings.index', compact('settings'));
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
            $data['logo'] = ImageCompressor::compressAndStore($request->file('logo'), 'settings');
        } else {
            unset($data['logo']);
        }

        // Upload hero_image baru kalau ada file yang dikirim
        if ($request->hasFile('hero_image')) {
            $this->deleteOldFile(WebsiteSetting::get('hero_image'));
            $data['hero_image'] = ImageCompressor::compressAndStore($request->file('hero_image'), 'settings');
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