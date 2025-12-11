<?php

namespace App\Database\Seeds;

use App\Models\LanguageModel;
use App\Models\SiteSettingModel;
use App\Models\SiteSettingTranslationModel;
use CodeIgniter\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settingModel = new SiteSettingModel();
        $translationModel = new SiteSettingTranslationModel();
        $languageModel = new LanguageModel();

        $setting = $settingModel->first();
        if (!$setting) {
            $settingId = $settingModel->insert([
                'site_base_url' => 'https://example.com',
                'google_analytics' => null,
                'google_search_console' => null,
                'smtp_host' => 'smtp.example.com',
                'smtp_user' => 'info@example.com',
                'smtp_password' => 'secret',
                'smtp_port' => '587',
            ]);
            $setting = $settingModel->find($settingId);
        }

        $languages = $languageModel->findAll();
        foreach ($languages as $language) {
            $translation = $translationModel
                ->where('setting_id', $setting['id'])
                ->where('lang_id', $language['id'])
                ->first();

            if ($translation) {
                continue;
            }

            $translationModel->insert([
                'setting_id' => $setting['id'],
                'lang_id' => $language['id'],
                'site_title' => $language['name'] . ' Site Başlığı',
                'meta_title' => $language['name'] . ' Meta Title',
                'meta_keywords' => 'ajans, web, ' . strtolower($language['name']),
                'meta_description' => $language['name'] . ' dilinde meta açıklaması',
                'meta_author' => 'Deniz Web Ajans',
                'footer_description' => 'Deniz Web Ajans ' . $language['name'] . ' açıklaması',
            ]);
        }
    }
}
