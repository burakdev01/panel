<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use App\Models\SiteSettingModel;
use App\Models\SiteSettingTranslationModel;
use CodeIgniter\HTTP\ResponseInterface;

class SiteSettings extends BaseController
{
    protected SiteSettingModel $settingModel;
    protected SiteSettingTranslationModel $translationModel;
    protected LanguageModel $languageModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->settingModel = new SiteSettingModel();
        $this->translationModel = new SiteSettingTranslationModel();
        $this->languageModel = new LanguageModel();
    }

    public function index()
    {
        $setting = $this->settingModel->first();
        if (!$setting) {
            $settingId = $this->settingModel->insert([
                'site_base_url' => null,
                'default_language_id' => null,
                'google_analytics' => null,
                'google_search_console' => null,
                'smtp_host' => null,
                'smtp_user' => null,
                'smtp_password' => null,
                'smtp_port' => null,
            ]);
            $setting = $this->settingModel->find($settingId);
        }

        $languages = $this->languageModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Genel Site Ayarları',
            'pageTitle' => 'Genel Site Ayarları',
            'setting' => $setting,
            'languages' => $languages,
        ];

        return view('admin/template/header', $data)
            . view('admin/settings/index', $data)
            . view('admin/template/footer');
    }

    public function showTranslation(?int $langId = null): ResponseInterface
    {
        if ($langId === null) {
            return $this->failNotFoundResponse();
        }

        $setting = $this->settingModel->first();
        if (!$setting) {
            return $this->failNotFoundResponse();
        }

        $translation = $this->translationModel
            ->where('lang_id', $langId)
            ->first();

        return $this->response->setJSON([
            'success' => true,
            'data' => $translation,
        ]);
    }

    public function updateSettings(): ResponseInterface
    {
        $setting = $this->settingModel->first();
        if (!$setting) {
            $settingId = $this->settingModel->insert([]);
            $setting = $this->settingModel->find($settingId);
        }

        $rules = [
            'site_base_url' => 'permit_empty|valid_url',
            'smtp_port' => 'permit_empty|numeric',
            'default_language_id' => 'permit_empty|is_not_unique[languages.id]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator?->getErrors(),
                ]);
        }

        $defaultLanguageId = $this->request->getPost('default_language_id');

        $this->settingModel->update($setting['id'], [
            'site_base_url' => $this->request->getPost('site_base_url'),
            'default_language_id' => $defaultLanguageId !== null && $defaultLanguageId !== '' ? (int) $defaultLanguageId : null,
            'google_analytics' => $this->request->getPost('google_analytics'),
            'google_search_console' => $this->request->getPost('google_search_console'),
            'smtp_host' => $this->request->getPost('smtp_host'),
            'smtp_user' => $this->request->getPost('smtp_user'),
            'smtp_password' => $this->request->getPost('smtp_password'),
            'smtp_port' => $this->request->getPost('smtp_port'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Genel ayarlar güncellendi.',
        ]);
    }

    public function updateTranslation(?int $langId = null): ResponseInterface
    {
        if ($langId === null) {
            return $this->failNotFoundResponse();
        }

        $setting = $this->settingModel->first();
        if (!$setting) {
            return $this->failNotFoundResponse();
        }

        $payload = [
            'site_title' => $this->request->getPost('site_title'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_author' => $this->request->getPost('meta_author'),
            'footer_description' => $this->request->getPost('footer_description'),
        ];

        $translation = $this->translationModel
            ->where('lang_id', $langId)
            ->first();

        if ($translation) {
            $this->translationModel->update($translation['id'], $payload);
        } else {
            $payload['lang_id'] = $langId;
            $this->translationModel->insert($payload);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Dil ayarları güncellendi.',
        ]);
    }

    private function failNotFoundResponse(): ResponseInterface
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON([
                'success' => false,
                'message' => 'Kayıt bulunamadı.',
            ]);
    }
}
