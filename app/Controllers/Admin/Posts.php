<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use App\Models\PostModel;
use CodeIgniter\HTTP\ResponseInterface;

class Posts extends BaseController
{
    protected PostModel $postModel;
    protected LanguageModel $languageModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->postModel = new PostModel();
        $this->languageModel = new LanguageModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Blog Yönetimi',
            'pageTitle' => 'Blog Yönetimi',
            'posts' => $this->getPosts(),
            'languages' => $this->languageModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/template/header', $data)
            . view('admin/posts/index', $data)
            . view('admin/template/footer');
    }

    public function show(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $post = $this->postModel->find($id);

        if (!$post) {
            return $this->failNotFoundResponse();
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->formatPost($post),
        ]);
    }

    public function store(): ResponseInterface
    {
        if (!$this->validate($this->validationRules())) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $imageFile = $this->request->getFile('image');
        $imagePath = null;

        if ($imageFile && $imageFile->isValid()) {
            $imagePath = $this->uploadImage($imageFile);
            if ($imagePath === null) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Görsel yüklenirken bir hata oluştu.',
                    ]);
            }
        }

        $payload = $this->buildPayload($imagePath);

        $id = $this->postModel->insert($payload);
        $post = $this->postModel->find($id);

        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)
            ->setJSON([
                'success' => true,
                'message' => 'Blog yazısı oluşturuldu.',
                'data' => $this->formatPost($post),
            ]);
    }

    public function update(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $post = $this->postModel->find($id);

        if (!$post) {
            return $this->failNotFoundResponse();
        }

        if (!$this->validate($this->validationRules(false, $id))) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $imageFile = $this->request->getFile('image');
        $imagePath = $post['image'];
        $removeImageRequest = $this->request->getPost('remove_image') === '1';

        if ($imageFile && $imageFile->isValid()) {
            $imagePath = $this->uploadImage($imageFile, $post['image']);
            $removeImageRequest = false;
            if ($imagePath === null) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Görsel yüklenirken bir hata oluştu.',
                    ]);
            }
        } elseif ($removeImageRequest && !empty($post['image'])) {
            $this->deleteImageFile($post['image']);
            $imagePath = null;
        }

        $payload = $this->buildPayload($imagePath, $removeImageRequest);

        $this->postModel->update($id, $payload);

        $updated = $this->postModel->find($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Blog yazısı güncellendi.',
            'data' => $this->formatPost($updated),
        ]);
    }

    public function delete(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $post = $this->postModel->find($id);

        if (!$post) {
            return $this->failNotFoundResponse();
        }

        $this->postModel->delete($id);

        if (!empty($post['image'])) {
            $this->deleteImageFile($post['image']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Blog yazısı silindi.',
        ]);
    }

    private function validationRules(bool $isCreate = true, ?int $id = null): array
    {
        $seoUrlRule = 'permit_empty|max_length[255]';
        $seoUrlRule .= $isCreate ? '|is_unique[posts.seo_url]' : '|is_unique[posts.seo_url,id,' . $id . ']';

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|string',
            'lang' => 'required|max_length[100]',
            'seo_title' => 'permit_empty|max_length[255]',
            'seo_desc' => 'permit_empty|max_length[255]',
            'seo_url' => $seoUrlRule,
            'active' => 'permit_empty|in_list[0,1]'
        ];

        $rules['image'] = 'if_exist|is_image[image]|ext_in[image,jpg,jpeg,png,webp]|max_size[image,2048]';

        return $rules;
    }

    private function uploadImage($imageFile, ?string $oldPath = null): ?string
    {
        $uploadDirectory = FCPATH . 'uploads/posts';

        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
            return null;
        }

        $newName = $imageFile->getRandomName();

        try {
            $imageFile->move($uploadDirectory, $newName);
        } catch (\Throwable $e) {
            return null;
        }

        if ($oldPath) {
            $absoluteOldPath = FCPATH . $oldPath;
            if (is_file($absoluteOldPath)) {
                @unlink($absoluteOldPath);
            }
        }

        return 'uploads/posts/' . $newName;
    }

    private function buildPayload(?string $imagePath = null, bool $removeImage = false): array
    {
        $activeInput = $this->request->getPost('active');

        $payload = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'lang' => $this->request->getPost('lang'),
            'seo_title' => $this->request->getPost('seo_title'),
            'seo_desc' => $this->request->getPost('seo_desc'),
            'seo_url' => $this->request->getPost('seo_url'),
            'active' => ($activeInput === '1' || $activeInput === 'on') ? 1 : 0,
        ];

        if ($removeImage) {
            $payload['image'] = null;
        } elseif ($imagePath !== null) {
            $payload['image'] = $imagePath;
        }

        return $payload;
    }

    private function getPosts(): array
    {
        return $this->postModel->orderBy('id', 'DESC')->findAll();
    }

    private function formatPost(?array $post): ?array
    {
        if ($post === null) {
            return null;
        }

        $post['image_url'] = !empty($post['image']) ? base_url($post['image']) : null;

        return $post;
    }

    private function deleteImageFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        $absolutePath = FCPATH . $relativePath;
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function failNotFoundResponse(): ResponseInterface
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON([
                'success' => false,
                'message' => 'Blog yazısı bulunamadı.',
            ]);
    }
}
