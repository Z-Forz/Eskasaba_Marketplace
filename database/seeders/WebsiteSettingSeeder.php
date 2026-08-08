<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'website_name'     => 'Eskasaba Marketplace',
            'logo'             => null,
            'hero_image'       => null,
            'hero_title'       => 'Selamat Datang di Website Kami',
            'hero_description' => 'Deskripsi singkat di bagian hero.',
            'about'            => 'Tulis tentang perusahaan/organisasi di sini.',
            'vision'           => 'Tulis visi di sini.',
            'mission'          => 'Tulis misi di sini.',
            'address'          => 'Alamat lengkap perusahaan.',
            'email'            => 'info@example.com',
            'phone'            => '+62 800 0000 0000',
            'instagram'        => null,
            'facebook'         => null,
            'tiktok'           => null,
            'youtube'          => null,
            'copyright'        => '© ' . date('Y') . ' Nama Perusahaan. All rights reserved.',
        ];

        foreach ($defaults as $key => $value) {
            WebsiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}