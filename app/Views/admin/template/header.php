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
        <a href="<?= base_url('admin/dashboard') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
          <i class="fas fa-home w-5"></i>
          <span class="font-medium">Ana Sayfa</span>
        </a>

        <a href="<?= base_url('admin/slayt') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100 transition">
          <i class="fas fa-image w-5"></i>
          <span class="font-medium">Slider Yönetimi</span>
        </a>

        <!-- Hizmet Yönetimi - Dropdown -->
        <div class="mb-1">
          <button onclick="toggleDropdown('hizmet')"
            class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center space-x-3">
              <i class="fas fa-layer-group w-5"></i>
              <span class="font-medium">Hizmet Yönetimi</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform" id="hizmet-icon"></i>
          </button>
          <div id="hizmet-menu" class="hidden pl-12 mt-1 space-y-1">
            <a href="<?= base_url('admin/hizmet/kategori') ?>"
              class="block px-4 py-2 text-sm text-gray-600 rounded hover:bg-gray-100">Kategoriler</a>
            <a href="<?= base_url('admin/hizmet') ?>"
              class="block px-4 py-2 text-sm text-gray-600 rounded hover:bg-gray-100">Hizmetler</a>
          </div>
        </div>

        <a href="<?= base_url('admin/video') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100 transition">
          <i class="fas fa-video w-5"></i>
          <span class="font-medium">Video Yönetimi</span>
        </a>

        <a href="<?= base_url('admin/fotograf') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100 transition">
          <i class="fas fa-camera w-5"></i>
          <span class="font-medium">Fotoğraf Yönetimi</span>
        </a>

        <a href="<?= base_url('admin/blog') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100 transition">
          <i class="fas fa-blog w-5"></i>
          <span class="font-medium">Blog Yönetimi</span>
        </a>

        <a href="<?= base_url('admin/sss') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100 transition">
          <i class="fas fa-question-circle w-5"></i>
          <span class="font-medium">SSS Yönetimi</span>
        </a>
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
        <a href="<?= base_url('admin/dashboard') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-blue-600 bg-blue-50 rounded-lg">
          <i class="fas fa-home w-5"></i>
          <span class="font-medium">Ana Sayfa</span>
        </a>
        <a href="<?= base_url('admin/slayt') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100">
          <i class="fas fa-image w-5"></i>
          <span class="font-medium">Slider Yönetimi</span>
        </a>
        <div class="mb-1">
          <button onclick="toggleDropdown('hizmet-mobile')"
            class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
            <div class="flex items-center space-x-3">
              <i class="fas fa-layer-group w-5"></i>
              <span class="font-medium">Hizmet Yönetimi</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform" id="hizmet-mobile-icon"></i>
          </button>
          <div id="hizmet-mobile-menu" class="hidden pl-12 mt-1 space-y-1">
            <a href="<?= base_url('admin/hizmet/kategori') ?>"
              class="block px-4 py-2 text-sm text-gray-600 rounded hover:bg-gray-100">Kategoriler</a>
            <a href="<?= base_url('admin/hizmet') ?>"
              class="block px-4 py-2 text-sm text-gray-600 rounded hover:bg-gray-100">Hizmetler</a>
          </div>
        </div>
        <a href="<?= base_url('admin/yorum') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100">
          <i class="fas fa-comment w-5"></i>
          <span class="font-medium">Yorum Yönetimi</span>
        </a>
        <a href="<?= base_url('admin/video') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100">
          <i class="fas fa-video w-5"></i>
          <span class="font-medium">Video Yönetimi</span>
        </a>
        <a href="<?= base_url('admin/fotograf') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100">
          <i class="fas fa-camera w-5"></i>
          <span class="font-medium">Fotoğraf Yönetimi</span>
        </a>
        <a href="<?= base_url('admin/blog') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100">
          <i class="fas fa-blog w-5"></i>
          <span class="font-medium">Blog Yönetimi</span>
        </a>
        <a href="<?= base_url('admin/sss') ?>"
          class="flex items-center space-x-3 px-4 py-3 mb-1 text-gray-700 rounded-lg hover:bg-gray-100">
          <i class="fas fa-question-circle w-5"></i>
          <span class="font-medium">SSS Yönetimi</span>
        </a>
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
