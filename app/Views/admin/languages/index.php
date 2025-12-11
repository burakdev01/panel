<div class="space-y-6">
  <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between px-6 py-5 border-b border-gray-200">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Dil Yönetimi</h1>
        <p class="text-sm text-gray-500 mt-1">Panelde kullanılacak dilleri buradan yönetebilirsiniz.</p>
      </div>
      <button type="button" onclick="openLanguageModal()"
        class="px-5 py-2 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus-circle"></i>
        <span>Yeni Dil</span>
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dil Adı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if(isset($languages) && !empty($languages)): ?>
          <?php foreach($languages as $language): ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">
              <?= esc($language['name']) ?>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center space-x-2">
                <button onclick="openLanguageModal(<?= $language['id'] ?>)" class="text-blue-600 hover:text-blue-800"
                  title="Düzenle">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteLanguage(<?= $language['id'] ?>)" class="text-red-600 hover:text-red-800"
                  title="Sil">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="2" class="px-6 py-8 text-center">
              <i class="fas fa-language text-gray-300 text-4xl mb-3"></i>
              <p class="text-gray-500">Henüz dil eklenmemiş</p>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php echo view('admin/languages/modal'); ?>
<?php echo view('admin/languages/scripts'); ?>
