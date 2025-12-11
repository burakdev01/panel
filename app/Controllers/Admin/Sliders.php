<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use App\Models\SliderModel;
use CodeIgniter\HTTP\ResponseInterface;

class Sliders extends BaseController
{
    protected SliderModel $sliderModel;
    protected LanguageModel $languageModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->sliderModel = new SliderModel();
        $this->languageModel = new LanguageModel();
    }

    public function createForm()
    {
        $data = [
            'title' => 'Yeni Slider',
            'pageTitle' => 'Yeni Slider Ekle',
            'languages' => $this->languageModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/template/header', $data)
            . view('admin/sliders/create', $data)
            . view('admin/template/footer');
    }

    public function editPage()
    {
        $data = [
            'title' => 'Slider Düzenle',
            'pageTitle' => 'Slider Düzenle',
            'sliders' => $this->getSlidersWithLanguage(),
            'languages' => $this->languageModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/template/header', $data)
            . view('admin/sliders/edit', $data)
            . view('admin/template/footer');
    }

    public function show(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $slider = $this->findSlider($id);

        if (!$slider) {
            return $this->failNotFoundResponse();
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->formatSlider($slider),
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
        if (!$imageFile || !$imageFile->isValid()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON([
                    'success' => false,
                    'message' => 'Geçerli bir resim yükleyiniz.',
                ]);
        }

        $imagePath = $this->uploadImage($imageFile);

        if ($imagePath === null) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON([
                    'success' => false,
                    'message' => 'Resim yüklenirken bir hata oluştu.',
                ]);
        }

        $payload = $this->buildPayload($imagePath);

        $id = $this->sliderModel->insert($payload);

        $slider = $this->findSlider($id);

        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)
            ->setJSON([
                'success' => true,
                'message' => 'Slayt başarıyla oluşturuldu.',
                'data' => $this->formatSlider($slider),
            ]);
    }

    public function update(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $slider = $this->findSlider($id);

        if (!$slider) {
            return $this->failNotFoundResponse();
        }

        if (!$this->validate($this->validationRules(false))) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $imageFile = $this->request->getFile('image');
        $imagePath = $slider['image'];
        $removeImageRequest = $this->request->getPost('remove_image') === '1';

        if ($imageFile && $imageFile->isValid()) {
            $imagePath = $this->uploadImage($imageFile, $slider['image']);
            $removeImageRequest = false;
            if ($imagePath === null) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Resim yüklenirken bir hata oluştu.',
                    ]);
            }
        } elseif ($removeImageRequest && !empty($slider['image'])) {
            $this->deleteImageFile($slider['image']);
            $imagePath = null;
        }

        $payload = $this->buildPayload($imagePath, $removeImageRequest);

        $this->sliderModel->update($id, $payload);

        $updatedSlider = $this->findSlider($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Slayt güncellendi.',
            'data' => $this->formatSlider($updatedSlider),
        ]);
    }

    public function delete(?int $id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->failNotFoundResponse();
        }

        $slider = $this->findSlider($id);

        if (!$slider) {
            return $this->failNotFoundResponse();
        }

        $this->sliderModel->delete($id);

        if (!empty($slider['image'])) {
            $this->deleteImageFile($slider['image']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Slayt silindi.',
        ]);
    }

    private function validationRules(bool $isCreate = true): array
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'details' => 'permit_empty|string',
            'links' => 'permit_empty|max_length[255]',
            'lang_id' => 'required|is_not_unique[languages.id]',
            'active' => 'permit_empty|in_list[0,1]',
        ];

        if ($isCreate) {
            $rules['image'] = 'uploaded[image]|is_image[image]|ext_in[image,jpg,jpeg,png,webp]|max_size[image,2048]';
        } else {
            $rules['image'] = 'if_exist|is_image[image]|ext_in[image,jpg,jpeg,png,webp]|max_size[image,2048]';
        }

        return $rules;
    }

    private function uploadImage($imageFile, ?string $oldPath = null): ?string
    {
        $uploadDirectory = FCPATH . 'uploads/sliders';

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

        return 'uploads/sliders/' . $newName;
    }

    private function buildPayload(?string $imagePath = null, bool $removeImage = false): array
    {
        $activeInput = $this->request->getPost('active');

        $payload = [
            'title' => $this->request->getPost('title'),
            'details' => $this->request->getPost('details'),
            'links' => $this->request->getPost('links'),
            'lang_id' => (int) $this->request->getPost('lang_id'),
            'active' => ($activeInput === '1' || $activeInput === 'on') ? 1 : 0,
        ];

        if ($removeImage) {
            $payload['image'] = null;
        } elseif ($imagePath !== null) {
            $payload['image'] = $imagePath;
        }

        return $payload;
    }

    private function findSlider(int $id): ?array
    {
        return $this->sliderModel
            ->select('sliders.*, languages.name as language_name')
            ->join('languages', 'languages.id = sliders.lang_id', 'left')
            ->find($id);
    }

    private function getSlidersWithLanguage(): array
    {
        return $this->sliderModel
            ->select('sliders.*, languages.name as language_name')
            ->join('languages', 'languages.id = sliders.lang_id', 'left')
            ->orderBy('sliders.id', 'DESC')
            ->findAll();
    }

    private function formatSlider(?array $slider): ?array
    {
        if ($slider === null) {
            return null;
        }

        $slider['image_url'] = !empty($slider['image']) ? base_url($slider['image']) : null;

        return $slider;
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
                'message' => 'Slayt bulunamadı.',
            ]);
    }
}
