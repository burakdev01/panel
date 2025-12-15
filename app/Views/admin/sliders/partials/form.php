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

      <?php if(empty($languages)): ?>
      <div class="mt-6 bg-orange-50 border border-orange-200 text-orange-700 text-sm rounded-lg px-4 py-3">
        Dil tanımlı olmadığı için slider içeriği eklenemez. Lütfen önce Dil Yönetimi bölümünden dil ekleyin.
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right Column - Form Fields -->
  <div class="lg:col-span-2">
    <?php if(!empty($languages)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
      <div class="border-b border-gray-200 px-6 py-4">
        <div class="flex flex-wrap gap-2">
          <?php foreach($languages as $index => $language): ?>
          <button type="button" data-slider-lang-tab="<?= esc($language['id']) ?>"
            class="slider-lang-tab px-4 py-2 rounded-full text-sm font-medium transition border border-transparent hover:bg-gray-100 <?= $index === 0 ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-600' ?>">
            <?= esc($language['name']) ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="p-6 flex flex-col gap-6">
        <?php foreach($languages as $index => $language): ?>
        <?php $langId = (int) $language['id']; ?>
        <div class="slider-lang-pane <?= $index !== 0 ? 'hidden' : '' ?>" data-slider-lang-pane="<?= $langId ?>">
          <input type="hidden" id="sliderVariantId_<?= $langId ?>" data-slider-variant-field="id"
            data-lang-id="<?= $langId ?>">

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Başlık:</label>
              <input type="text" placeholder="Başlık" id="sliderVariantTitle_<?= $langId ?>"
                data-slider-variant-field="title" data-lang-id="<?= $langId ?>"
                class="slider-variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama:</label>
              <textarea rows="5" placeholder="Kısa açıklama" id="sliderVariantDetails_<?= $langId ?>"
                data-slider-variant-field="details" data-lang-id="<?= $langId ?>"
                class="slider-variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Yönlendirme Linki:</label>
              <input type="url" placeholder="https://..." id="sliderVariantLink_<?= $langId ?>"
                data-slider-variant-field="links" data-lang-id="<?= $langId ?>"
                class="slider-variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus-border-transparent outline-none transition">
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="px-6 pb-6 text-xs text-gray-500 border-t border-gray-100">
        Bir dili boş bırakırsanız bu dil için içerik kaydedilmez. Mevcut bir dilin tüm alanlarını temizleyerek varyantı
        silebilirsiniz.
      </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-300 p-6 text-center text-gray-500">
      Dil tanımlı olmadığı için içerik alanları pasif durumda.
    </div>
    <?php endif; ?>
  </div>

</div>
