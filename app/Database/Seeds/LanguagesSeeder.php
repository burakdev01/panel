<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\LanguageModel;

class LanguagesSeeder extends Seeder
{
    public function run(): void
    {
        $languageModel = new LanguageModel();

        $languages = [
            ['name' => 'Türkçe'],
            ['name' => 'English'],
        ];

        foreach ($languages as $language) {
            if ($languageModel->where('name', $language['name'])->first()) {
                continue;
            }

            $languageModel->insert($language);
        }
    }
}
