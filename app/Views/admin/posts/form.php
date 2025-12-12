<input type="hidden" id="postId">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Left Column - Image Upload -->
  <div class="lg:col-span-1">
    <div class="bg-gray-50 rounded-xl p-6 h-full">
      <label class="block text-sm font-medium text-gray-700 mb-4">Kapak Görseli</label>

      <div
        class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer bg-white">
        <input type="file" id="postImageUpload" class="hidden" accept="image/*" onchange="previewPostImage(event)">
        <label for="postImageUpload" class="cursor-pointer block">
          <div id="postImagePreview" class="mb-4">
            <i class="fas fa-image text-gray-400 text-5xl mb-3"></i>
          </div>
          <p class="text-sm text-blue-500 font-medium mb-1">Görsel Seç</p>
          <p class="text-xs text-gray-500">veya sürükleyip bırakın</p>
        </label>
        <button type="button" id="postRemoveImageButton"
          class="mt-4 w-full px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition hidden"
          onclick="removeSelectedPostImage()">
          Görseli Kaldır
        </button>
      </div>

      <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Durum</label>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" id="postStatusToggle" class="sr-only peer" checked>
          <div
            class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500">
          </div>
          <span class="ml-3 text-sm font-medium text-gray-700">Aktif</span>
        </label>
      </div>

    </div>
  </div>

  <!-- Right Column -->
  <div class="lg:col-span-2">
    <?php if(!empty($languages)): ?>
    <?php $seoBaseUrl = rtrim(base_url(), '/') . '/'; ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
      <div class="border-b border-gray-200 px-6 py-4">
        <div class="flex flex-wrap gap-2">
          <?php foreach($languages as $index => $language): ?>
          <button type="button" data-lang-tab="<?= esc($language['id']) ?>"
            class="lang-tab px-4 py-2 rounded-full text-sm font-medium transition border <?= $index === 0 ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-600 border-transparent hover:bg-gray-100' ?>">
            <?= esc($language['name']) ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="p-6 space-y-6">
        <?php foreach($languages as $index => $language): ?>
        <?php $langId = (int) $language['id']; ?>
        <div class="lang-pane <?= $index !== 0 ? 'hidden' : '' ?>" data-lang-pane="<?= $langId ?>">
          <input type="hidden" id="variantId_<?= $langId ?>" data-variant-field="id" data-lang-id="<?= $langId ?>">

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Başlık:</label>
              <input type="text" placeholder="Başlık" id="variantTitle_<?= $langId ?>"
                data-variant-field="title" data-lang-id="<?= $langId ?>"
                class="variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">İçerik:</label>
              <textarea rows="6" placeholder="Blog içeriğini yazın" id="variantContent_<?= $langId ?>"
                data-variant-field="content" data-lang-id="<?= $langId ?>"
                class="variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
            </div>
          </div>

          <div class="bg-gray-50 rounded-2xl p-6 mt-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">SEO Bilgileri</h3>

            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
              <p id="seoPreviewTitle_<?= $langId ?>" class="text-lg font-medium text-blue-700">Başlık</p>
              <p id="seoPreviewUrl_<?= $langId ?>" class="text-sm text-green-600 mt-1 break-all">
                <?= esc($seoBaseUrl) ?>
              </p>
              <p id="seoPreviewDescription_<?= $langId ?>" class="text-sm text-gray-600 mt-1">Açıklama</p>
            </div>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SEO Title:</label>
                <input type="text" placeholder="SEO başlığı" id="variantSeoTitle_<?= $langId ?>"
                  data-variant-field="seo_title" data-lang-id="<?= $langId ?>"
                  class="variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus-border-transparent outline-none transition">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SEO Description:</label>
                <textarea rows="3" placeholder="SEO açıklaması" id="variantSeoDesc_<?= $langId ?>"
                  data-variant-field="seo_desc" data-lang-id="<?= $langId ?>"
                  class="variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus-border-transparent outline-none transition resize-none"></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SEO URL:</label>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                  <input type="text" value="<?= esc($seoBaseUrl) ?>" readonly
                    class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none">
                  <div class="relative flex-1">
                    <input type="text" placeholder="seo-url" id="variantSeoUrl_<?= $langId ?>"
                      data-variant-field="seo_url" data-lang-id="<?= $langId ?>"
                      class="variant-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus-border-transparent outline-none transition">
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                      <i class="fas fa-exclamation-circle text-red-400 opacity-0"
                        id="seoUrlWarning_<?= $langId ?>"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="px-6 pb-6 text-xs text-gray-500 border-t border-gray-100">
        Bir dili boş bırakırsanız bu dil için içerik kaydedilmez. Mevcut bir dilin tüm alanlarını temizleyerek varyantı silebilirsiniz.
      </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-300 p-6 text-center text-gray-500">
      Dil tanımlı olmadığı için içerik girişi yapılamıyor. Lütfen önce dil ekleyin.
    </div>
    <?php endif; ?>
  </div>

</div>
