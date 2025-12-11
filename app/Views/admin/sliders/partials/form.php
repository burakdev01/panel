<input type="hidden" id="sliderId">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Left Column - Image Upload -->
  <div class="lg:col-span-1">
    <div class="bg-gray-50 rounded-xl p-6 h-full">
      <label class="block text-sm font-medium text-gray-700 mb-4">Resim Seç</label>

      <!-- Image Upload Area -->
      <div
        class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer bg-white">
        <input type="file" id="imageUpload" class="hidden" accept="image/*" onchange="previewImage(event)">
        <label for="imageUpload" class="cursor-pointer block">
          <div id="imagePreview" class="mb-4">
            <i class="fas fa-image text-gray-400 text-5xl mb-3"></i>
          </div>
          <p class="text-sm text-blue-500 font-medium mb-1">Resim Seç</p>
          <p class="text-xs text-gray-500">veya sürükleyip bırakın</p>
        </label>
        <button type="button" id="removeImageButton"
          class="mt-4 w-full px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition hidden"
          onclick="removeSelectedImage()">
          Resmi Kaldır
        </button>
      </div>

      <!-- Language Selector -->
      <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Dil</label>
        <select id="languageSelect"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
          <?php if(!empty($languages)): ?>
          <?php foreach($languages as $language): ?>
          <option value="<?= $language['id'] ?>"><?= esc($language['name']) ?></option>
          <?php endforeach; ?>
          <?php else: ?>
          <option value="">Dil bulunamadı</option>
          <?php endif; ?>
        </select>
      </div>

      <!-- Status Toggle -->
      <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Durum</label>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" id="statusToggle" class="sr-only peer" checked>
          <div
            class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500">
          </div>
          <span class="ml-3 text-sm font-medium text-gray-700">Aktif</span>
        </label>
      </div>
    </div>
  </div>

  <!-- Right Column - Form Fields -->
  <div class="lg:col-span-2">
    <div class="space-y-6">

      <!-- Title Field -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Başlık:</label>
        <input type="text" id="titleInput" placeholder="Başlık"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
      </div>

      <!-- Description Field -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama:</label>
        <textarea id="detailsInput" placeholder="Açıklama" rows="5"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
      </div>

      <!-- Redirect Link Field -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Yönlendirme Linki:</label>
        <input type="url" id="linkInput" placeholder="Yönlendirme Linki"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
      </div>



    </div>
  </div>

</div>