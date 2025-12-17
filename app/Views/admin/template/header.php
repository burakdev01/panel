<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Admin Panel' ?> - Deniz Web Ajans</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

  body {
    font-family: 'Inter', sans-serif;
  }
  </style>
</head>

<body class="bg-gray-50">
  <?php
    $uri = service('uri');
    $currentPath = trim($uri->getPath(), '/');
    $isPath = static function(string $path, bool $exact = false) use ($currentPath): bool {
        $target = trim($path, '/');
        if ($target === '') {
            return $currentPath === '';
        }
        if ($exact) {
            return $currentPath === $target;
        }
        return strpos($currentPath, $target) === 0;
    };
    $navBaseClass = 'flex items-center space-x-3 px-4 py-3 mb-1 rounded-lg transition';
    $activeClass = 'text-blue-600 bg-blue-50 hover:bg-blue-100';
    $inactiveClass = 'text-gray-700 hover:bg-gray-100';
    $mobileBaseClass = 'flex items-center space-x-3 px-4 py-3 mb-1 rounded-lg';
    $mobileActiveClass = 'text-blue-600 bg-blue-50';
    $mobileInactiveClass = 'text-gray-700 hover:bg-gray-100';
    $slugify = static function(string $label): string {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($label));
        $slug = trim($slug, '-');
        return $slug ?: 'menu-item';
    };
    $menuItems = [
        [
            'path' => 'admin/dashboard',
            'icon' => 'fas fa-home',
            'label' => 'Ana Sayfa',
        ],
        [
            'path' => 'admin/slider',
            'icon' => 'fas fa-image',
            'label' => 'Slider Yönetimi',
        ],
        // [
        //     'path' => 'admin/service',
        //     'icon' => 'fas fa-layer-group',
        //     'label' => 'Hizmet Yönetimi',
        //     'dropdownId' => 'hizmet',
        //     'mobileDropdownId' => 'hizmet-mobile',
        //     'children' => [
        //         [
        //             'path' => 'admin/service/category',
        //             'label' => 'Kategoriler',
        //         ],
        //         [
        //             'path' => 'admin/service',
        //             'label' => 'Hizmetler',
        //             'exact' => true,
        //         ],
        //     ],
        // ],
        // [
        //     'path' => 'admin/video',
        //     'icon' => 'fas fa-video',
        //     'label' => 'Video Yönetimi',
        // ],
        // [
        //     'path' => 'admin/photo',
        //     'icon' => 'fas fa-camera',
        //     'label' => 'Fotoğraf Yönetimi',
        // ],
        [
            'path' => 'admin/blog',
            'icon' => 'fas fa-blog',
            'label' => 'Blog Yönetimi',
        ],
        [
            'path' => 'admin/comment',
            'icon' => 'fas fa-comments',
            'label' => 'Yorum Yönetimi',
        ],
        [
            'path' => 'admin/language',
            'icon' => 'fas fa-language',
            'label' => 'Dil Yönetimi',
        ],
        [
            'path' => 'admin/settings',
            'icon' => 'fas fa-sliders-h',
            'label' => 'Site Ayarları',
        ],
        // [
        //     'path' => 'admin/faq',
        //     'icon' => 'fas fa-question-circle',
        //     'label' => 'SSS Yönetimi',
        // ],
    ];
  ?>
  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar - Desktop -->
    <aside id="sidebar"
      class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-gray-200 transition-all duration-300">
      <!-- Logo -->
      <div class="h-16 flex items-center justify-center border-b border-gray-200 px-6">
        <div class="flex items-center space-x-3">
          <div
            class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-xl">DW</span>
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-800">Deniz Web</h1>
            <p class="text-xs text-gray-500">Ajans</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto py-4 px-3">
        <?php foreach ($menuItems as $item): ?>
        <?php
          $hasChildren = isset($item['children']) && is_array($item['children']);
          $itemActive = $isPath($item['path'], $item['exact'] ?? false);
          $childActive = false;
          if ($hasChildren) {
              foreach ($item['children'] as $child) {
                  if ($isPath($child['path'], $child['exact'] ?? false)) {
                      $childActive = true;
                      break;
                  }
              }
          }
          $dropdownId = $item['dropdownId'] ?? $slugify($item['label']);
          $menuActive = $itemActive || $childActive;
        ?>
        <?php if ($hasChildren): ?>
        <div class="mb-1">
          <button onclick="toggleDropdown('<?= $dropdownId ?>')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition <?= $menuActive ? 'text-blue-600 bg-blue-50 hover:bg-blue-100' : 'text-gray-700 hover:bg-gray-100' ?>">
            <div class="flex items-center space-x-3">
              <i class="<?= esc($item['icon']) ?> w-5"></i>
              <span class="font-medium"><?= esc($item['label']) ?></span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform <?= $menuActive ? 'rotate-90 text-blue-600' : '' ?>"
              id="<?= $dropdownId ?>-icon"></i>
          </button>
          <div id="<?= $dropdownId ?>-menu" class="<?= $menuActive ? '' : 'hidden ' ?>pl-12 mt-1 space-y-1">
            <?php foreach ($item['children'] as $child): ?>
            <?php $isChildActive = $isPath($child['path'], $child['exact'] ?? false); ?>
            <a href="<?= base_url($child['path']) ?>"
              class="block px-4 py-2 text-sm rounded <?= $isChildActive ? 'text-blue-600 bg-blue-50 hover:bg-blue-100' : 'text-gray-600 hover:bg-gray-100' ?>"><?= esc($child['label']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php else: ?>
        <a href="<?= base_url($item['path']) ?>"
          class="<?= $navBaseClass . ' ' . ($itemActive ? $activeClass : $inactiveClass) ?>">
          <i class="<?= esc($item['icon']) ?> w-5"></i>
          <span class="font-medium"><?= esc($item['label']) ?></span>
        </a>
        <?php endif; ?>
        <?php endforeach; ?>
      </nav>

    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"
      onclick="closeMobileSidebar()"></div>

    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar"
      class="fixed inset-y-0 left-0 w-64 bg-white z-50 transform -translate-x-full transition-transform duration-300 lg:hidden">
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
          <div
            class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-xl">DW</span>
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-800">Deniz Web</h1>
            <p class="text-xs text-gray-500">Ajans</p>
          </div>
        </div>
        <button onclick="closeMobileSidebar()" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <!-- Mobile Navigation (same as desktop) -->
      <nav class="flex-1 overflow-y-auto py-4 px-3">
        <?php foreach ($menuItems as $item): ?>
        <?php
          $hasChildren = isset($item['children']) && is_array($item['children']);
          $itemActive = $isPath($item['path'], $item['exact'] ?? false);
          $childActive = false;
          if ($hasChildren) {
              foreach ($item['children'] as $child) {
                  if ($isPath($child['path'], $child['exact'] ?? false)) {
                      $childActive = true;
                      break;
                  }
              }
          }
          $dropdownId = $item['mobileDropdownId'] ?? (($item['dropdownId'] ?? $slugify($item['label'])) . '-mobile');
          $menuActive = $itemActive || $childActive;
        ?>
        <?php if ($hasChildren): ?>
        <div class="mb-1">
          <button onclick="toggleDropdown('<?= $dropdownId ?>')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-lg <?= $menuActive ? $mobileActiveClass : $mobileInactiveClass ?>">
            <div class="flex items-center space-x-3">
              <i class="<?= esc($item['icon']) ?> w-5"></i>
              <span class="font-medium"><?= esc($item['label']) ?></span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform <?= $menuActive ? 'rotate-90 text-blue-600' : '' ?>"
              id="<?= $dropdownId ?>-icon"></i>
          </button>
          <div id="<?= $dropdownId ?>-menu" class="<?= $menuActive ? '' : 'hidden ' ?>pl-12 mt-1 space-y-1">
            <?php foreach ($item['children'] as $child): ?>
            <?php $isChildActive = $isPath($child['path'], $child['exact'] ?? false); ?>
            <a href="<?= base_url($child['path']) ?>"
              class="block px-4 py-2 text-sm rounded <?= $isChildActive ? 'text-blue-600 bg-blue-50 hover:bg-blue-100' : 'text-gray-600 hover:bg-gray-100' ?>"><?= esc($child['label']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php else: ?>
        <a href="<?= base_url($item['path']) ?>"
          class="<?= $mobileBaseClass . ' ' . ($itemActive ? $mobileActiveClass : $mobileInactiveClass) ?>">
          <i class="<?= esc($item['icon']) ?> w-5"></i>
          <span class="font-medium"><?= esc($item['label']) ?></span>
        </a>
        <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Navbar -->
      <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6">
        <div class="flex items-center space-x-4">
          <button onclick="toggleMobileSidebar()" class="lg:hidden text-gray-600 hover:text-gray-800">
            <i class="fas fa-bars text-xl"></i>
          </button>
          <h2 class="text-xl font-semibold text-gray-800 hidden sm:block"><?= $pageTitle ?? 'Dashboard' ?></h2>
        </div>

        <div class="flex items-center space-x-2 sm:space-x-4">
          <button class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-bell"></i>
          </button>
          <button class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-cog"></i>
          </button>
          <a href="<?= base_url('admin/logout') ?>"
            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-6">
        <?php if (session()->getFlashdata('success')): ?>
        <div
          class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
          <span><?= session()->getFlashdata('success') ?></span>
          <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div
          class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
          <span><?= session()->getFlashdata('error') ?></span>
          <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <?php endif; ?>