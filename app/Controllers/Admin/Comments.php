<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostCommentModel;
use App\Models\PostModel;
use App\Models\PostVariantModel;
use CodeIgniter\HTTP\ResponseInterface;

class Comments extends BaseController
{
    protected PostCommentModel $commentModel;
    protected PostModel $postModel;
    protected PostVariantModel $postVariantModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->commentModel = new PostCommentModel();
        $this->postModel = new PostModel();
        $this->postVariantModel = new PostVariantModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Yorum Yönetimi',
            'pageTitle' => 'Yorum Yönetimi',
            'comments' => $this->getComments(),
            'posts' => $this->getPostOptions(),
        ];

        return view('admin/template/header', $data)
            . view('admin/comments/index', $data)
            . view('admin/template/footer');
    }

    public function show(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $comment = $this->findComment($id);
        if (!$comment) {
            return $this->failNotFoundResponse();
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $comment,
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

        $payload = $this->buildPayload();
        $payload['is_approved'] = $this->request->getPost('is_approved') === '1' ? 1 : 0;

        $id = $this->commentModel->insert($payload, true);
        $comment = $this->findComment($id);

        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)
            ->setJSON([
                'success' => true,
                'message' => 'Yorum eklendi.',
                'data' => $comment,
            ]);
    }

    public function update(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        if (!$this->commentModel->find($id)) {
            return $this->failNotFoundResponse();
        }

        if (!$this->validate($this->validationRules(false))) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $payload = $this->buildPayload();
        if ($this->request->getPost('is_approved') !== null) {
            $payload['is_approved'] = $this->request->getPost('is_approved') === '1' ? 1 : 0;
        }

        $this->commentModel->update($id, $payload);
        $comment = $this->findComment($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Yorum güncellendi.',
            'data' => $comment,
        ]);
    }

    public function delete(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        if (!$this->commentModel->find($id)) {
            return $this->failNotFoundResponse();
        }

        $this->commentModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Yorum silindi.',
        ]);
    }

    private function validationRules(bool $isCreate = true): array
    {
        $rules = [
            'post_id' => 'required|is_not_unique[posts.id]',
            'author_name' => 'required|min_length[2]|max_length[150]',
            'author_email' => 'required|valid_email|max_length[150]',
            'content' => 'required|min_length[5]',
            'is_approved' => 'permit_empty|in_list[0,1]',
        ];

        if (!$isCreate) {
            $rules['post_id'] = 'permit_empty|is_not_unique[posts.id]';
            $rules['author_name'] = 'permit_empty|min_length[2]|max_length[150]';
            $rules['author_email'] = 'permit_empty|valid_email|max_length[150]';
            $rules['content'] = 'permit_empty|min_length[5]';
        }

        return $rules;
    }

    private function buildPayload(): array
    {
        $payload = [];

        $postId = $this->request->getPost('post_id');
        if ($postId !== null && $postId !== '') {
            $payload['post_id'] = (int) $postId;
        }

        $authorName = $this->request->getPost('author_name');
        if ($authorName !== null) {
            $payload['author_name'] = $authorName;
        }

        $authorEmail = $this->request->getPost('author_email');
        if ($authorEmail !== null) {
            $payload['author_email'] = $authorEmail;
        }

        $content = $this->request->getPost('content');
        if ($content !== null) {
            $payload['content'] = $content;
        }

        return $payload;
    }

    private function getComments(): array
    {
        $comments = $this->commentModel
            ->select('post_comments.*, posts.image as post_image')
            ->join('posts', 'posts.id = post_comments.post_id', 'left')
            ->orderBy('post_comments.created_at', 'DESC')
            ->findAll();

        if (empty($comments)) {
            return [];
        }

        $postIds = array_column($comments, 'post_id');
        $variants = $this->postVariantModel
            ->select('post_variants.*')
            ->whereIn('post_id', $postIds)
            ->orderBy('post_id', 'ASC')
            ->findAll();

        $variantsByPost = [];
        foreach ($variants as $variant) {
            $variantsByPost[$variant['post_id']][] = $variant;
        }

        foreach ($comments as &$comment) {
            $comment['post_title'] = $this->resolvePostTitle($variantsByPost[$comment['post_id']] ?? []);
        }
        unset($comment);

        return $comments;
    }

    private function getPostOptions(): array
    {
        $posts = $this->postModel
            ->orderBy('post_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();

        if (empty($posts)) {
            return [];
        }

        $postIds = array_column($posts, 'id');
        $variants = $this->postVariantModel
            ->select('post_variants.*')
            ->whereIn('post_id', $postIds)
            ->orderBy('post_id', 'ASC')
            ->orderBy('lang_id', 'ASC')
            ->findAll();

        $variantsByPost = [];
        foreach ($variants as $variant) {
            $variantsByPost[$variant['post_id']][] = $variant;
        }

        $options = [];
        foreach ($posts as $post) {
            $options[] = [
                'id' => $post['id'],
                'title' => $this->resolvePostTitle($variantsByPost[$post['id']] ?? []) ?? ('Blog #' . $post['id']),
            ];
        }

        return $options;
    }

    private function resolvePostTitle(array $variants): ?string
    {
        return $variants[0]['title'] ?? null;
    }

    private function findComment(int $id): ?array
    {
        $comment = $this->commentModel->find($id);
        if (!$comment) {
            return null;
        }

        $comment['post_title'] = $this->getPostTitle($comment['post_id']);

        return $comment;
    }

    private function getPostTitle(int $postId): ?string
    {
        $variant = $this->postVariantModel
            ->where('post_id', $postId)
            ->orderBy('lang_id', 'ASC')
            ->first();

        return $variant['title'] ?? null;
    }

    private function failNotFoundResponse(): ResponseInterface
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON([
                'success' => false,
                'message' => 'Yorum bulunamadı.',
            ]);
    }
}
