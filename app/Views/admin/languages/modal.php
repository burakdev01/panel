<div id="languageModal"
  class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h2 id="languageModalTitle" class="text-xl font-semibold text-gray-800">Dil Ekle</h2>
      <button onclick="closeLanguageModal()" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <div class="p-6 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Dil Adı</label>
        <input type="text" id="languageNameInput" placeholder="Örnek: Türkçe"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
      <button onclick="closeLanguageModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">İptal</button>
      <button onclick="saveLanguage()"
        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition flex items-center space-x-2">
        <span>Kaydet</span>
        <i class="fas fa-save"></i>
      </button>
    </div>
  </div>
</div>
<input type="hidden" id="languageIdField">
