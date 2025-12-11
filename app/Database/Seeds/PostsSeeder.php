<?php

namespace App\Database\Seeds;

use App\Models\PostModel;
use CodeIgniter\Database\Seeder;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        $languageModel = model('App\Models\LanguageModel');
        $languages = $languageModel->findAll();

        if (empty($languages)) {
            echo "Languages tablosunda kayıt bulunmadığı için Post seed atlanıyor.\n";
            return;
        }

        $posts = [
            [
                'title' => 'Hoş Geldiniz: Blog Açılışı',
                'content' => 'Deniz Web Ajans bloguna hoş geldiniz! Burada dijital çözümlerimizi paylaşacağız.',
                'image' => null,
                'lang_id' => $languages[0]['id'],
                'seo_title' => 'Deniz Web Ajans Blog Açılışı',
                'seo_desc' => 'Ajansımızın blogunda en yeni projeler ve ipuçları.',
                'seo_url' => 'hos-geldiniz',
                'active' => 1,
                'post_order' => 1,
            ],
            [
                'title' => 'Yeni Hizmetlerimiz',
                'content' => 'Müşterilerimize sunduğumuz yeni hizmet paketleri hakkında bilgi alın.',
                'image' => null,
                'lang_id' => $languages[0]['id'] ?? null,
                'seo_title' => 'Yeni Hizmet Paketleri',
                'seo_desc' => 'Web tasarım ve yazılımda sunduğumuz güncel hizmetler.',
                'seo_url' => 'yeni-hizmetler',
                'active' => 1,
                'post_order' => 2,
            ],
            [
                'title' => 'Our Agency News',
                'content' => 'Latest updates from Deniz Web Agency for our English-speaking audience.',
                'image' => null,
                'lang_id' => $languages[1]['id'] ?? $languages[0]['id'],
                'seo_title' => 'Agency Updates',
                'seo_desc' => 'Find out what is new at Deniz Web.',
                'seo_url' => 'agency-updates',
                'active' => 1,
                'post_order' => 3,
            ],
        ];

        $postModel = new PostModel();
        foreach ($posts as $post) {
            $postModel->insert($post);
        }
    }
}
