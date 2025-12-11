<?php

namespace App\Database\Seeds;

use App\Models\SliderModel;
use App\Models\LanguageModel;
use CodeIgniter\Database\Seeder;

class SlidersSeeder extends Seeder
{
    public function run(): void
    {
        $languageModel = new LanguageModel();
        $languages = $languageModel->findAll();

        if (empty($languages)) {
            echo "Language kaydı bulunamadı, slider seed atlanıyor.\n";
            return;
        }

        $defaultLangId = $languages[0]['id'];

        $sliders = [
            [
                'title' => 'Hoş Geldiniz',
                'details' => 'Deniz Web Ajans ile projelerinize güç katın.',
                'links' => null,
                'image' => 'image',
                'lang_id' => $defaultLangId,
                'active' => 1,
                'slider_order' => 1,
            ],
            [
                'title' => 'Profesyonel Tasarım',
                'details' => 'Modern ve kullanıcı dostu arayüzler tasarlıyoruz.',
                'links' => null,
                'image' => 'image',
                'lang_id' => $defaultLangId,
                'active' => 1,
                'slider_order' => 2,
            ],
            [
                'title' => 'Teknik Destek',
                'details' => '7/24 destek ile yanınızdayız.',
                'links' => null,
                'image' => 'image',
                'lang_id' => $languages[1]['id'] ?? $defaultLangId,
                'active' => 1,
                'slider_order' => 3,
            ],
        ];

        $sliderModel = new SliderModel();
        foreach ($sliders as $slider) {
            $sliderModel->insert($slider);
        }
    }
}
