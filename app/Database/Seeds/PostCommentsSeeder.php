<?php

namespace App\Database\Seeds;

use App\Models\PostCommentModel;
use App\Models\PostModel;
use App\Models\PostVariantModel;
use CodeIgniter\Database\Seeder;

class PostCommentsSeeder extends Seeder
{
    public function run(): void
    {
        $postModel = new PostModel();
        $variantModel = new PostVariantModel();

        $posts = $postModel->findAll();
        if (empty($posts)) {
            echo "Post kaydı bulunamadığı için PostCommentsSeeder atlanıyor.\n";
            return;
        }

        $variants = $variantModel
            ->whereIn('post_id', array_column($posts, 'id'))
            ->orderBy('post_id', 'ASC')
            ->findAll();

        $variantMap = [];
        foreach ($variants as $variant) {
            $variantMap[$variant['post_id']][] = $variant;
        }

        $comments = [
            [
                'post_id' => $posts[0]['id'],
                'author_name' => 'Ayşe Yılmaz',
                'author_email' => 'ayse@example.com',
                'content' => 'Harika bir yazı olmuş, emeğinize sağlık.',
                'is_approved' => 1,
            ],
            [
                'post_id' => $posts[0]['id'],
                'author_name' => 'John Doe',
                'author_email' => 'john@example.com',
                'content' => 'Great insights, thank you for sharing.',
                'is_approved' => 0,
            ],
            [
                'post_id' => $posts[1]['id'] ?? $posts[0]['id'],
                'author_name' => 'Mehmet Kaya',
                'author_email' => 'mehmet@example.com',
                'content' => 'Yeni hizmet paketleri hakkında bilgi alabilir miyiz?',
                'is_approved' => 0,
            ],
        ];

        $commentModel = new PostCommentModel();
        foreach ($comments as $comment) {
            $commentModel->insert($comment);
        }
    }
}
