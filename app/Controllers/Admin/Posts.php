<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use App\Models\PostModel;
use App\Models\PostVariantModel;
use CodeIgniter\HTTP\ResponseInterface;

class Posts extends BaseController
{
    protected PostModel $postModel;
    protected PostVariantModel $postVariantModel;
    protected LanguageModel $languageModel;
    protected array $languages = [];
    protected array $languageMap = [];

    public function __construct()
    {
        helper(['form', 'url']);
        $this->postModel = new PostModel();
        $this->languageModel = new LanguageModel();
        $this->postVariantModel = new PostVariantModel();

        $this->languages = $this->languageModel->orderBy('name', 'ASC')->findAll();
        $this->languageMap = array_column($this->languages, null, 'id');
    }

    public function index()
    {
        $data = [
            'title' => 'Blog Yönetimi',
            'pageTitle' => 'Blog Yönetimi',
            'posts' => $this->getPosts(),
            'languages' => $this->languages,
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

        $post['variants'] = $this->getVariantsForPost($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->formatPost($post, true),
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

        [$variantsPayload, $variantErrors] = $this->prepareVariantsPayload();
        if (!empty($variantErrors)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $variantErrors,
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

        $payload = $this->buildPostPayload($imagePath);
        if (!array_key_exists('published_at', $payload)) {
            $payload['published_at'] = date('Y-m-d H:i:s');
        }
        if (!array_key_exists('post_order', $payload)) {
            $payload['post_order'] = $this->getNextOrder();
        }

        $id = $this->postModel->insert($payload, true);
        $this->syncVariants($id, $variantsPayload);

        $post = $this->postModel->find($id);
        $post['variants'] = $this->getVariantsForPost($id);

        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)
            ->setJSON([
                'success' => true,
                'message' => 'Blog yazısı oluşturuldu.',
                'data' => $this->formatPost($post, true),
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

        if (!$this->validate($this->validationRules())) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        [$variantsPayload, $variantErrors] = $this->prepareVariantsPayload();
        if (!empty($variantErrors)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $variantErrors,
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

        $payload = $this->buildPostPayload($imagePath, $removeImageRequest);

        $this->postModel->update($id, $payload);
        $this->syncVariants($id, $variantsPayload);

        $updated = $this->postModel->find($id);
        $updated['variants'] = $this->getVariantsForPost($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Blog yazısı güncellendi.',
            'data' => $this->formatPost($updated, true),
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

    private function validationRules(): array
    {
        $rules = [
            'active' => 'permit_empty|in_list[0,1]',
            'post_order' => 'permit_empty|integer',
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

    private function buildPostPayload(?string $imagePath = null, bool $removeImage = false): array
    {
        $activeInput = $this->request->getPost('active');

        $payload = [
            'active' => ($activeInput === '1' || $activeInput === 'on') ? 1 : 0,
        ];

        $postOrderInput = $this->request->getPost('post_order');
        if ($postOrderInput !== null && $postOrderInput !== '') {
            $payload['post_order'] = (int) $postOrderInput;
        }

        if ($removeImage) {
            $payload['image'] = null;
        } elseif ($imagePath !== null) {
            $payload['image'] = $imagePath;
        }

        return $payload;
    }

    private function getNextOrder(): int
    {
        $lastOrder = $this->postModel
            ->select('post_order')
            ->orderBy('post_order', 'DESC')
            ->first();

        return ($lastOrder['post_order'] ?? 0) + 1;
    }

    private function getPosts(): array
    {
        $posts = $this->postModel
            ->orderBy('posts.post_order', 'ASC')
            ->orderBy('posts.id', 'DESC')
            ->findAll();

        if (empty($posts)) {
            return [];
        }

        $postIds = array_column($posts, 'id');
        $variants = $this->postVariantModel
            ->select('post_variants.*, languages.name as language_name')
            ->join('languages', 'languages.id = post_variants.lang_id', 'left')
            ->whereIn('post_variants.post_id', $postIds)
            ->orderBy('languages.name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($variants as $variant) {
            $grouped[$variant['post_id']][] = $variant;
        }

        foreach ($posts as &$post) {
            $postVariants = $grouped[$post['id']] ?? [];
            $post['variants'] = $postVariants;
            $post['primary_variant'] = $postVariants[0] ?? null;
            $post = $this->formatPost($post);
        }
        unset($post);

        return $posts;
    }

    private function getVariantsForPost(int $postId): array
    {
        return $this->postVariantModel
            ->select('post_variants.*, languages.name as language_name')
            ->join('languages', 'languages.id = post_variants.lang_id', 'left')
            ->where('post_variants.post_id', $postId)
            ->orderBy('languages.name', 'ASC')
            ->findAll();
    }

    private function formatPost(?array $post, bool $includeVariants = false): ?array
    {
        if ($post === null) {
            return null;
        }

        if ($includeVariants) {
            $post['variants'] = $post['variants'] ?? $this->getVariantsForPost((int) $post['id']);
        } else {
            $post['variants'] = $post['variants'] ?? [];
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

    private function prepareVariantsPayload(): array
    {
        $rawVariants = $this->request->getPost('variants');
        $prepared = [];
        $errors = [];
        $hasActiveVariant = false;

        if (!is_array($rawVariants)) {
            return [[], ['variants' => 'En az bir dil için içerik girmeniz gerekiyor.']];
        }

        foreach ($rawVariants as $langId => $fields) {
            $langId = (int) $langId;
            $rawVariantId = $fields['id'] ?? null;
            $variantId = null;
            if ($rawVariantId !== null && $rawVariantId !== '' && (int) $rawVariantId > 0) {
                $variantId = (int) $rawVariantId;
            }
            $title = trim((string) ($fields['title'] ?? ''));
            $contentRaw = (string) ($fields['content'] ?? '');
            $contentIsEmpty = trim($contentRaw) === '';
            $seoTitle = trim((string) ($fields['seo_title'] ?? ''));
            $seoDesc = trim((string) ($fields['seo_desc'] ?? ''));
            $seoUrl = trim((string) ($fields['seo_url'] ?? ''));

            $isEmpty = $title === '' && $contentIsEmpty && $seoUrl === '';

            if ($variantId === null && $isEmpty) {
                continue;
            }

            if (!$this->languageExists($langId)) {
                $errors["variants.$langId.lang_id"] = 'Geçersiz dil seçildi.';
                continue;
            }

            if ($isEmpty && $variantId !== null) {
                $prepared[] = [
                    'id' => $variantId,
                    'lang_id' => $langId,
                    'delete' => true,
                ];
                continue;
            }

            $fieldErrors = [];
            if ($title === '') {
                $fieldErrors[] = 'Başlık zorunludur.';
            }
            if ($contentIsEmpty) {
                $fieldErrors[] = 'İçerik zorunludur.';
            }
            if ($seoUrl === '') {
                $fieldErrors[] = 'SEO URL zorunludur.';
            }

            if (!empty($fieldErrors)) {
                $errors["variants.$langId"] = implode(' ', $fieldErrors);
                continue;
            }

            if ($seoUrl !== '') {
                $conflictQuery = $this->postVariantModel->builder();
                $conflictQuery->select('id')
                    ->where('lang_id', $langId)
                    ->where('seo_url', $seoUrl);

                if ($variantId !== null) {
                    $conflictQuery->where('id !=', $variantId);
                }

                $conflict = $conflictQuery->get(1)->getFirstRow();
                if ($conflict !== null) {
                    $errors["variants.$langId.seo_url"] = 'Bu dil için SEO URL zaten kullanılıyor.';
                    continue;
                }
            }

            $hasActiveVariant = true;

            $prepared[] = [
                'id' => $variantId,
                'lang_id' => $langId,
                'title' => $title,
                'content' => $contentRaw,
                'seo_title' => $seoTitle !== '' ? $seoTitle : null,
                'seo_desc' => $seoDesc !== '' ? $seoDesc : null,
                'seo_url' => $seoUrl,
            ];
        }

        if (!$hasActiveVariant) {
            $errors['variants'] = 'En az bir dil için içerik girmeniz gerekiyor.';
        }

        return [$prepared, $errors];
    }

    private function languageExists(int $langId): bool
    {
        return isset($this->languageMap[$langId]);
    }

    private function syncVariants(int $postId, array $variants): void
    {
        foreach ($variants as $variant) {
            $variantId = $variant['id'] ?? null;
            $shouldDelete = !empty($variant['delete']);

            if ($shouldDelete && $variantId !== null) {
                $this->postVariantModel->delete($variantId);
                continue;
            }

            $data = [
                'lang_id' => $variant['lang_id'],
                'title' => $variant['title'],
                'content' => $variant['content'],
                'seo_title' => $variant['seo_title'],
                'seo_desc' => $variant['seo_desc'],
                'seo_url' => $variant['seo_url'],
            ];

            if ($variantId !== null) {
                $this->postVariantModel->update($variantId, $data);
                continue;
            }

            $data['post_id'] = $postId;
            $this->postVariantModel->insert($data);
        }
    }
}
