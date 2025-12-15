<?php

namespace App\Database\Seeds;

use App\Models\LanguageModel;
use App\Models\SliderModel;
use App\Models\SliderVariantModel;
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
                'slider' => [
                    'image' => 'https://via.placeholder.com/960x480?text=Slider+1',
                    'active' => 1,
                    'slider_order' => 1,
                ],
                'variants' => [
                    [
                        'lang_id' => $defaultLangId,
                        'title' => 'Hoş Geldiniz',
                        'details' => 'Deniz Web Ajans ile projelerinize güç katın.',
                        'links' => null,
                    ],
                    [
                        'lang_id' => $languages[1]['id'] ?? $defaultLangId,
                        'title' => 'Welcome',
                        'details' => 'Empower your projects with Deniz Web Agency.',
                        'links' => null,
                    ],
                ],
            ],
            [
                'slider' => [
                    'image' => 'https://via.placeholder.com/960x480?text=Slider+2',
                    'active' => 1,
                    'slider_order' => 2,
                ],
                'variants' => [
                    [
                        'lang_id' => $defaultLangId,
                        'title' => 'Profesyonel Tasarım',
                        'details' => 'Modern ve kullanıcı dostu arayüzler tasarlıyoruz.',
                        'links' => null,
                    ],
                ],
            ],
            [
                'slider' => [
                    'image' => 'https://via.placeholder.com/960x480?text=Slider+3',
                    'active' => 1,
                    'slider_order' => 3,
                ],
                'variants' => [
                    [
                        'lang_id' => $defaultLangId,
                        'title' => 'Teknik Destek',
                        'details' => '7/24 destek ile yanınızdayız.',
                        'links' => null,
                    ],
                    [
                        'lang_id' => $languages[1]['id'] ?? $defaultLangId,
                        'title' => 'Technical Support',
                        'details' => 'We stand by you with around-the-clock support.',
                        'links' => null,
                    ],
                ],
            ],
        ];

        $sliderModel = new SliderModel();
        $variantModel = new SliderVariantModel();

        foreach ($sliders as $sliderData) {
            $sliderId = $sliderModel->insert($sliderData['slider'], true);

            foreach ($sliderData['variants'] as $variant) {
                $variantModel->insert([
                    'slider_id' => $sliderId,
                    'lang_id' => $variant['lang_id'],
                    'title' => $variant['title'],
                    'details' => $variant['details'],
                    'links' => $variant['links'],
                ]);
            }
        }
    }
}
