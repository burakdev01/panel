<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use CodeIgniter\HTTP\ResponseInterface;

class Languages extends BaseController
{
    protected LanguageModel $languageModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->languageModel = new LanguageModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dil Yönetimi',
            'pageTitle' => 'Dil Yönetimi',
            'languages' => $this->languageModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/template/header', $data)
            . view('admin/languages/index', $data)
            . view('admin/template/footer');
    }

    public function show(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $language = $this->languageModel->find($id);
        if (!$language) {
            return $this->failNotFoundResponse();
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $language,
        ]);
    }

    public function store(): ResponseInterface
    {
        if (!$this->validate(['name' => 'required|min_length[2]|max_length[100]|is_unique[languages.name]'])) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $id = $this->languageModel->insert([
            'name' => $this->request->getPost('name'),
        ]);

        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)
            ->setJSON([
                'success' => true,
                'message' => 'Dil eklendi.',
                'data' => $this->languageModel->find($id),
            ]);
    }

    public function update(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        if (!$this->languageModel->find($id)) {
            return $this->failNotFoundResponse();
        }

        $rules = ['name' => 'required|min_length[2]|max_length[100]|is_unique[languages.name,id,' . $id . ']'];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $this->languageModel->update($id, ['name' => $this->request->getPost('name')]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Dil güncellendi.',
            'data' => $this->languageModel->find($id),
        ]);
    }

    public function delete(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        if (!$this->languageModel->find($id)) {
            return $this->failNotFoundResponse();
        }

        $this->languageModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Dil silindi.',
        ]);
    }

    private function failNotFoundResponse(): ResponseInterface
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON([
                'success' => false,
                'message' => 'Dil bulunamadı.',
            ]);
    }
}
