<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\HTTP\ResponseInterface;

class ContentOrder extends BaseController
{
    public function update(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        $entity = $payload['entity'] ?? '';
        $items = $payload['items'] ?? [];

        if ($entity === '' || !is_array($items) || empty($items)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON([
                    'success' => false,
                    'message' => 'Geçersiz veri gönderildi.',
                ]);
        }

        if ($entity !== 'posts') {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON([
                    'success' => false,
                    'message' => 'Desteklenmeyen içerik tipi.',
                ]);
        }

        $postModel = new PostModel();

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($items as $item) {
                $id = (int) ($item['id'] ?? 0);
                $order = (int) ($item['order'] ?? 0);

                if ($id <= 0) {
                    continue;
                }

                $postModel->update($id, ['post_order' => $order]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed');
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON([
                    'success' => false,
                    'message' => 'Sıralama güncellenemedi.',
                ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sıralama güncellendi.',
        ]);
    }
}
