<div class="space-y-6">
  <div
    class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <p class="text-sm uppercase tracking-wider text-blue-100 mb-1">Deniz Web - Yönetim</p>
      <h1 class="text-3xl font-semibold">Genel Site Ayarları</h1>
      <p class="text-blue-100 mt-2 max-w-2xl">Dilinizi ve global parametreleri tek ekrandan yöneterek SEO değerlerinizi
        her zaman güncel tutun.</p>
    </div>
    <div class="grid grid-cols-2 gap-3 text-center text-sm">
      <div class="bg-white/20 rounded-xl p-4">
        <p class="text-blue-100">Desteklenen Dil</p>
        <p class="text-2xl font-semibold"><?= count($languages) ?></p>
      </div>
      <div class="bg-white/20 rounded-xl p-4">
        <p class="text-blue-100">SMTP Durumu</p>
        <p class="text-2xl font-semibold"><?= !empty($setting['smtp_host']) ? 'Aktif' : 'Pasif' ?></p>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Dil Bazlı İçerik</h2>
            <p class="text-sm text-gray-500">SEO ve footer tanıtım metinleri</p>
          </div>
          <select id="settingsLanguageSelect"
            class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
            <?php foreach($languages as $language): ?>
            <option value="<?= $language['id'] ?>"><?= esc($language['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div id="languageSettingsForm" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Site Adı (Title)</label>
            <input type="text" id="siteTitleInput"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
            <input type="text" id="metaTitleInput"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
            <textarea id="metaKeywordsInput" rows="2"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
            <textarea id="metaDescriptionInput" rows="2"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Author</label>
            <input type="text" id="metaAuthorInput"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Footer Tanıtım</label>
            <textarea id="footerDescriptionInput" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
          </div>
          <button onclick="saveLanguageSettings()"
            class="w-full px-5 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition">Seçili
            Dili Kaydet</button>
        </div>
      </div>
    </div>
    <div class="xl:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Global Ayarlar</h2>
            <p class="text-sm text-gray-500">URL, analiz ve SMTP bilgileri</p>
          </div>
          <button onclick="saveGeneralSettings()"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition">Kaydet</button>
        </div>
        <div class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Site Base URL</label>
            <input type="url" id="siteBaseUrl" value="<?= esc($setting['site_base_url'] ?? '') ?>"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Dil</label>
            <select id="defaultLanguageSelect"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
              <option value="">Dil seçiniz</option>
              <?php foreach($languages as $language): ?>
              <option value="<?= $language['id'] ?>"
                <?= ($setting['default_language_id'] ?? null) == $language['id'] ? 'selected' : '' ?>>
                <?= esc($language['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics</label>
              <textarea id="googleAnalytics" rows="5"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500"><?= esc($setting['google_analytics'] ?? '') ?></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Google Search Console</label>
              <textarea id="googleSearchConsole" rows="5"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500"><?= esc($setting['google_search_console'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">SMTP Ayarları</h2>
            <p class="text-sm text-gray-500">Mail gönderimleri için gerekli parametreler</p>
          </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
            <input type="text" id="smtpHost" value="<?= esc($setting['smtp_host'] ?? '') ?>"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Kullanıcı</label>
            <input type="text" id="smtpUser" value="<?= esc($setting['smtp_user'] ?? '') ?>"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
            <input type="text" id="smtpPassword" value="<?= esc($setting['smtp_password'] ?? '') ?>"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
            <input type="number" id="smtpPort" value="<?= esc($setting['smtp_port'] ?? '') ?>"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
        <div class="px-6 pb-6">
          <button onclick="saveGeneralSettings()"
            class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition w-full md:w-auto">SMTP
            Bilgilerini Kaydet</button>
        </div>
      </div>
    </div>


  </div>
</div>

<?php echo view('admin/settings/scripts'); ?>
