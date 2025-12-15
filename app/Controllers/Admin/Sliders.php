<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use App\Models\SliderModel;
use App\Models\SliderVariantModel;
use CodeIgniter\HTTP\ResponseInterface;

class Sliders extends BaseController
{
    protected SliderModel $sliderModel;
    protected LanguageModel $languageModel;
    protected SliderVariantModel $sliderVariantModel;
    protected array $languages = [];
    protected array $languageMap = [];

    public function __construct()
    {
        helper(['form', 'url']);
        $this->sliderModel = new SliderModel();
        $this->languageModel = new LanguageModel();
        $this->sliderVariantModel = new SliderVariantModel();

        $this->languages = $this->languageModel->orderBy('name', 'ASC')->findAll();
        $this->languageMap = array_column($this->languages, null, 'id');
    }

    public function editPage()
    {
        $data = [
            'title' => 'Slider Yönetimi',
            'pageTitle' => 'Slider Yönetimi',
            'sliders' => $this->getSlidersWithVariants(),
            'languages' => $this->languages,
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
        if (empty($this->languages)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => [
                        'languages' => 'Dil tanımı olmadan slider eklenemez.',
                    ],
                ]);
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
        if (!array_key_exists('slider_order', $payload)) {
            $payload['slider_order'] = $this->getNextOrder();
        }

        $id = $this->sliderModel->insert($payload, true);
        $this->syncVariants($id, $variantsPayload);

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

        [$variantsPayload, $variantErrors] = $this->prepareVariantsPayload();
        if (!empty($variantErrors)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $variantErrors,
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
        $this->syncVariants($id, $variantsPayload);

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
        $this->sliderVariantModel->where('slider_id', $id)->delete();

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
            'active' => 'permit_empty|in_list[0,1]',
            'slider_order' => 'permit_empty|integer',
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
            'active' => ($activeInput === '1' || $activeInput === 'on') ? 1 : 0,
        ];

        $orderInput = $this->request->getPost('slider_order');
        if ($orderInput !== null && $orderInput !== '') {
            $payload['slider_order'] = (int) $orderInput;
        }

        if ($removeImage) {
            $payload['image'] = null;
        } elseif ($imagePath !== null) {
            $payload['image'] = $imagePath;
        }

        return $payload;
    }

    private function findSlider(int $id): ?array
    {
        $slider = $this->sliderModel->find($id);

        return $this->formatSlider($slider);
    }

    private function getSlidersWithVariants(): array
    {
        $sliders = $this->sliderModel
            ->orderBy('slider_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();

        if (empty($sliders)) {
            return [];
        }

        $sliderIds = array_column($sliders, 'id');

        $variants = $this->sliderVariantModel
            ->select('slider_variants.*, languages.name as language_name')
            ->join('languages', 'languages.id = slider_variants.lang_id', 'left')
            ->whereIn('slider_variants.slider_id', $sliderIds)
            ->orderBy('languages.name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($variants as $variant) {
            $grouped[$variant['slider_id']][] = $variant;
        }

        foreach ($sliders as &$slider) {
            $slider['variants'] = $grouped[$slider['id']] ?? [];
            $slider['primary_variant'] = $slider['variants'][0] ?? null;
            $slider = $this->formatSlider($slider, false);
        }
        unset($slider);

        return $sliders;
    }

    private function getNextOrder(): int
    {
        $lastOrder = $this->sliderModel
            ->select('slider_order')
            ->orderBy('slider_order', 'DESC')
            ->first();

        return ($lastOrder['slider_order'] ?? 0) + 1;
    }

    private function formatSlider(?array $slider, bool $includeVariants = true): ?array
    {
        if ($slider === null) {
            return null;
        }

        if ($includeVariants) {
            $slider['variants'] = $this->getVariantsForSlider((int) $slider['id']);
        } else {
            $slider['variants'] = $slider['variants'] ?? [];
        }

        $slider['primary_variant'] = $slider['primary_variant'] ?? ($slider['variants'][0] ?? null);
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

    private function getVariantsForSlider(int $sliderId): array
    {
        return $this->sliderVariantModel
            ->select('slider_variants.*, languages.name as language_name')
            ->join('languages', 'languages.id = slider_variants.lang_id', 'left')
            ->where('slider_variants.slider_id', $sliderId)
            ->orderBy('languages.name', 'ASC')
            ->findAll();
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
            $details = (string) ($fields['details'] ?? '');
            $links = trim((string) ($fields['links'] ?? ''));

            $isEmpty = $title === '' && trim($details) === '' && $links === '';

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

            if ($title === '') {
                $errors["variants.$langId.title"] = 'Başlık zorunludur.';
                continue;
            }

            $hasActiveVariant = true;

            $prepared[] = [
                'id' => $variantId,
                'lang_id' => $langId,
                'title' => $title,
                'details' => $details,
                'links' => $links !== '' ? $links : null,
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

    private function syncVariants(int $sliderId, array $variants): void
    {
        foreach ($variants as $variant) {
            $variantId = $variant['id'] ?? null;
            $shouldDelete = !empty($variant['delete']);

            if ($shouldDelete && $variantId !== null) {
                $this->sliderVariantModel->delete($variantId);
                continue;
            }

            $data = [
                'lang_id' => $variant['lang_id'],
                'title' => $variant['title'],
                'details' => $variant['details'],
                'links' => $variant['links'],
            ];

            if ($variantId !== null) {
                $this->sliderVariantModel->update($variantId, $data);
                continue;
            }

            $data['slider_id'] = $sliderId;
            $this->sliderVariantModel->insert($data);
        }
    }
}
