<?php

namespace App\Database\Seeds;

use App\Models\PostModel;
use App\Models\PostVariantModel;
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

        $defaultLang = $languages[0]['id'];
        $secondLang = $languages[1]['id'] ?? $defaultLang;

        $posts = [
            [
                'post' => [
                    'image' => null,
                    'active' => 1,
                    'post_order' => 1,
                ],
                'variants' => [
                    [
                        'lang_id' => $defaultLang,
                        'title' => 'Hoş Geldiniz: Blog Açılışı',
                        'content' => 'Deniz Web Ajans bloguna hoş geldiniz! Burada dijital çözümlerimizi paylaşacağız.',
                        'seo_title' => 'Deniz Web Ajans Blog Açılışı',
                        'seo_desc' => 'Ajansımızın blogunda en yeni projeler ve ipuçları.',
                        'seo_url' => 'hos-geldiniz',
                    ],
                    [
                        'lang_id' => $secondLang,
                        'title' => 'Welcome: Blog Launch',
                        'content' => 'Welcome to the Deniz Web Agency blog. We will share our latest digital solutions here.',
                        'seo_title' => 'Deniz Web Agency Blog Launch',
                        'seo_desc' => 'Discover the latest projects and tips from our agency.',
                        'seo_url' => 'welcome-blog-launch',
                    ],
                ],
            ],
            [
                'post' => [
                    'image' => null,
                    'active' => 1,
                    'post_order' => 2,
                ],
                'variants' => [
                    [
                        'lang_id' => $defaultLang,
                        'title' => 'Yeni Hizmetlerimiz',
                        'content' => 'Müşterilerimize sunduğumuz yeni hizmet paketleri hakkında bilgi alın.',
                        'seo_title' => 'Yeni Hizmet Paketleri',
                        'seo_desc' => 'Web tasarım ve yazılımda sunduğumuz güncel hizmetler.',
                        'seo_url' => 'yeni-hizmetler',
                    ],
                ],
            ],
            [
                'post' => [
                    'image' => null,
                    'active' => 1,
                    'post_order' => 3,
                ],
                'variants' => [
                    [
                        'lang_id' => $secondLang,
                        'title' => 'Our Agency News',
                        'content' => 'Latest updates from Deniz Web Agency for our English-speaking audience.',
                        'seo_title' => 'Agency Updates',
                        'seo_desc' => 'Find out what is new at Deniz Web.',
                        'seo_url' => 'agency-updates',
                    ],
                ],
            ],
        ];

        $postModel = new PostModel();
        $variantModel = new PostVariantModel();

        foreach ($posts as $postData) {
            $postId = $postModel->insert($postData['post'], true);

            foreach ($postData['variants'] as $variant) {
                $variantModel->insert([
                    'post_id'  => $postId,
                    'lang_id'  => $variant['lang_id'],
                    'title'    => $variant['title'],
                    'content'  => $variant['content'],
                    'seo_title'=> $variant['seo_title'],
                    'seo_desc' => $variant['seo_desc'],
                    'seo_url'  => $variant['seo_url'],
                ]);
            }
        }
    }
}
