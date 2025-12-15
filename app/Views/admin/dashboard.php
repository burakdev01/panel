<!-- Dashboard Content -->
<div>
  <!-- Welcome Section -->
  <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white mb-6">
    <h1 class="text-2xl md:text-3xl font-bold mb-2">Hoş Geldiniz, <?= service('auth')->username ?? 'Admin' ?>!</h1>
    <p class="text-blue-100">Deniz Web Ajans Yönetim Paneline hoş geldiniz. İşte bugünün özeti.</p>
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
          <i class="fas fa-image text-blue-600 text-xl"></i>
        </div>
        <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded">+12%</span>
      </div>
      <h3 class="text-2xl font-bold text-gray-800 mb-1"><?= $stats['slayt'] ?? 0 ?></h3>
      <p class="text-gray-600 text-sm">Toplam Slayt</p>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
          <i class="fas fa-layer-group text-purple-600 text-xl"></i>
        </div>
        <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded">+8%</span>
      </div>
      <h3 class="text-2xl font-bold text-gray-800 mb-1"><?= $stats['hizmet'] ?? 0 ?></h3>
      <p class="text-gray-600 text-sm">Toplam Hizmet</p>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
          <i class="fas fa-blog text-orange-600 text-xl"></i>
        </div>
        <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded">+15%</span>
      </div>
      <h3 class="text-2xl font-bold text-gray-800 mb-1"><?= $stats['blog'] ?? 0 ?></h3>
      <p class="text-gray-600 text-sm">Blog Yazısı</p>
    </div>

    <!-- Stat Card 4 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-shadow">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
          <i class="fas fa-comment text-green-600 text-xl"></i>
        </div>
      </div>
      <h3 class="text-2xl font-bold text-gray-800 mb-1"><?= $stats['yorum'] ?? 0 ?></h3>
      <p class="text-gray-600 text-sm">Toplam Yorum</p>
    </div>
  </div>

  <div class="mb-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Hızlı İşlemler</h3>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-2 gap-4">
          <a href="<?= base_url('admin/slayt') ?>"
            class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition group">
            <i class="fas fa-plus-circle text-3xl text-gray-400 group-hover:text-blue-500 mb-2"></i>
            <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600">Yeni Slayt</span>
          </a>

          <a href="<?= base_url('admin/hizmet/create') ?>"
            class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition group">
            <i class="fas fa-plus-circle text-3xl text-gray-400 group-hover:text-purple-500 mb-2"></i>
            <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600">Yeni Hizmet</span>
          </a>

          <a href="<?= base_url('admin/blog/create') ?>"
            class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition group">
            <i class="fas fa-plus-circle text-3xl text-gray-400 group-hover:text-orange-500 mb-2"></i>
            <span class="text-sm font-medium text-gray-700 group-hover:text-orange-600">Yeni Blog</span>
          </a>

          <a href="<?= base_url('admin/fotograf/create') ?>"
            class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition group">
            <i class="fas fa-plus-circle text-3xl text-gray-400 group-hover:text-green-500 mb-2"></i>
            <span class="text-sm font-medium text-gray-700 group-hover:text-green-600">Yeni Fotoğraf</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Content Table -->
  <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800">Son Eklenen Slaytlar</h3>
      <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Tümünü Gör →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12"></th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İçerik</th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
              Dil</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200" id="sortable-table">
          <?php if(isset($sliders) && !empty($sliders)): ?>
          <?php foreach($sliders as $slider): ?>
          <?php $primaryVariant = $slider['primary_variant'] ?? null; ?>
          <tr class="hover:bg-gray-50 transition sortable-row" data-id="<?= $slider['id'] ?>">
            <td class="px-4 py-4">
              <div class="flex items-center justify-center cursor-move drag-handle text-gray-400 hover:text-gray-600">
                <i class="fas fa-grip-vertical"></i>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <img
                  src="<?= !empty($slider['image']) ? base_url($slider['image']) : 'https://via.placeholder.com/96?text=Slayt' ?>"
                  alt="<?= esc($primaryVariant['title'] ?? 'Dil içeriği bulunmuyor') ?>"
                  class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    <?= esc($primaryVariant['title'] ?? 'Dil içeriği bulunmuyor') ?>
                  </div>
                  <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                    <?= esc($primaryVariant['details'] ?? 'Bu slider için içerik eklenmemiş') ?>
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 hidden md:table-cell">
              <?php if(!empty($slider['variants'])): ?>
              <div class="flex flex-wrap gap-2">
                <?php foreach($slider['variants'] as $variant): ?>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                  <?= esc($variant['language_name'] ?? 'Dil') ?>
                </span>
                <?php endforeach; ?>
              </div>
              <?php else: ?>
              <span class="text-xs text-gray-500">Dil içeriği yok</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <span
                class="px-3 py-1 text-xs font-medium rounded-full <?= ($slider['active'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                <?= ($slider['active'] ?? 0) ? 'Aktif' : 'Pasif' ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center space-x-2">
                <button onclick="openEditModal(<?= $slider['id'] ?>)"
                  class="text-blue-600 hover:text-blue-800 transition" title="Düzenle">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteSlider(<?= $slider['id'] ?>)" class="text-red-600 hover:text-red-800 transition"
                  title="Sil">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="5" class="px-6 py-8 text-center">
              <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
              <p class="text-gray-500">Henüz slayt eklenmemiş</p>
            </td>
          </tr>
          <?php endif; ?>

    </div>

    <!-- Edit Modal -->
    <?php echo view('admin/modals/edit_modal', ['languages' => $languages ?? []]); ?>
    <?php echo view('admin/sliders/scripts'); ?>
