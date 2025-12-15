<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;
use App\Models\SliderModel;
use App\Models\SliderVariantModel;

class Dashboard extends BaseController
{
    protected SliderModel $sliderModel;
    protected LanguageModel $languageModel;
    protected SliderVariantModel $sliderVariantModel;

    public function __construct()
    {
        // Auth kontrolü için helper yükle
        helper(['auth']);
        $this->sliderModel = new SliderModel();
        $this->languageModel = new LanguageModel();
        $this->sliderVariantModel = new SliderVariantModel();
    }

    public function index()
    {
        // Oturum kontrolü
        if (!session()->has('isLoggedIn')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Dashboard için gerekli verileri hazırla
        $data = [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'stats' => $this->getStats(),
            'recentActivities' => $this->getRecentActivities(),
            'sliders' => $this->getSliders(),
            'languages' => $this->languageModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/template/header', $data)
             . view('admin/dashboard', $data)
             . view('admin/template/footer');
    }

    private function getStats()
    {
        return [
            'slayt' => $this->sliderModel->countAllResults(),
            'hizmet' => 0,
            'blog' => 0,
            'yorum' => 0,
        ];
    }

    private function getRecentActivities()
    {
        // Son aktiviteleri getir
        // Örnek veriler:
        return [
            [
                'icon' => 'plus',
                'message' => 'Yeni blog yazısı eklendi: "Web Tasarım Trendleri"',
                'time' => '5 dakika önce'
            ],
            [
                'icon' => 'edit',
                'message' => 'Slayt güncellendi: "Ana Sayfa Banner"',
                'time' => '1 saat önce'
            ],
            [
                'icon' => 'comment',
                'message' => 'Yeni yorum onay bekliyor',
                'time' => '2 saat önce'
            ],
            [
                'icon' => 'image',
                'message' => '3 yeni fotoğraf eklendi',
                'time' => '3 saat önce'
            ]
        ];
    }

    private function getSliders(): array
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
            $slider['image_url'] = !empty($slider['image']) ? base_url($slider['image']) : null;
        }
        unset($slider);

        return $sliders;
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'))->with('success', 'Başarıyla çıkış yapıldı');
    }
}
