<div class="space-y-6">
  <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between px-6 py-5 border-b border-gray-200">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Blog Yönetimi</h1>
        <p class="text-sm text-gray-500 mt-1">Mevcut blog yazılarını düzenleyebilir veya yeni yazılar ekleyebilirsiniz.
        </p>
      </div>
      <button type="button" onclick="openPostModal()"
        class="px-5 py-2 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus-circle"></i>
        <span>Yeni Blog Yazısı</span>
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12"></th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
              Dil</th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
              SEO URL</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200" id="sortable-table" data-entity="posts">
          <?php if(isset($posts) && !empty($posts)): ?>
          <?php foreach($posts as $post): ?>
          <tr class="hover:bg-gray-50 transition sortable-row" data-id="<?= $post['id'] ?>">
            <td class="px-4 py-4">
              <div class="flex items-center justify-center cursor-move drag-handle text-gray-400 hover:text-gray-600">
                <i class="fas fa-grip-vertical"></i>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-gray-900 flex items-center space-x-3">
                <img
                  src="<?= !empty($post['image']) ? base_url($post['image']) : 'https://via.placeholder.com/96?text=Blog' ?>"
                  alt="<?= esc($post['title']) ?>" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                <span><?= esc($post['title']) ?></span>
              </div>
            </td>
            <td class="px-6 py-4 hidden md:table-cell">
              <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                <?= esc($post['language_name'] ?? 'Bilinmiyor') ?>
              </span>
            </td>
            <td class="px-6 py-4 hidden lg:table-cell">
              <span class="text-xs text-gray-600"><?= esc($post['seo_url'] ?? '-') ?></span>
            </td>
            <td class="px-6 py-4">
              <span
                class="px-3 py-1 text-xs font-medium rounded-full <?= ($post['active'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                <?= ($post['active'] ?? 0) ? 'Aktif' : 'Pasif' ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center space-x-2">
                <button onclick="openPostModal(<?= $post['id'] ?>)" class="text-blue-600 hover:text-blue-800 transition"
                  title="Düzenle">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="deletePost(<?= $post['id'] ?>)" class="text-red-600 hover:text-red-800 transition"
                  title="Sil">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="6" class="px-6 py-8 text-center">
              <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
              <p class="text-gray-500">Henüz blog yazısı eklenmemiş</p>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php echo view('admin/posts/modal', ['languages' => $languages ?? []]); ?>
<?php echo view('admin/posts/scripts'); ?>
