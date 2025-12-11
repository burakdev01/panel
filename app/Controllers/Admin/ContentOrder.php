<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostModel;
use App\Models\SliderModel;
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

        $map = [
            'posts' => ['model' => new PostModel(), 'column' => 'post_order'],
            'sliders' => ['model' => new SliderModel(), 'column' => 'slider_order'],
        ];

        if (!array_key_exists($entity, $map)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON([
                    'success' => false,
                    'message' => 'Desteklenmeyen içerik tipi.',
                ]);
        }

        $model = $map[$entity]['model'];
        $column = $map[$entity]['column'];

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($items as $item) {
                $id = (int) ($item['id'] ?? 0);
                $order = (int) ($item['order'] ?? 0);

                if ($id <= 0) {
                    continue;
                }

                $model->update($id, [$column => $order]);
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
