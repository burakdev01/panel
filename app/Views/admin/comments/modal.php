<div id="commentModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">

    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h2 id="commentModalTitle" class="text-xl font-semibold text-gray-800">Yorum Ekle</h2>
      <div class="flex items-center space-x-2">
        <button onclick="closeCommentModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
          İptal
        </button>
        <button onclick="saveComment()"
          class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition flex items-center space-x-2">
          <span>Kaydet</span>
          <i class="fas fa-save"></i>
        </button>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-6">
      <input type="hidden" id="commentId">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Blog Yazısı</label>
        <select id="commentPostSelect"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
          <option value="">Seçiniz</option>
          <?php if(!empty($posts)): ?>
          <?php foreach($posts as $post): ?>
          <option value="<?= $post['id'] ?>"><?= esc($post['title']) ?></option>
          <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad</label>
          <input type="text" id="commentAuthorName" placeholder="Yorum yapan kişi"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">E-Posta</label>
          <input type="email" id="commentAuthorEmail" placeholder="ornek@mail.com"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus-border-transparent outline-none transition">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Yorum</label>
        <textarea id="commentContent" rows="5" placeholder="Yorum içeriğini yazın"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus-border-transparent outline-none transition resize-none"></textarea>
      </div>

      <div class="flex items-center justify-between">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3">Durum</label>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="commentStatusToggle" class="sr-only peer">
            <div
              class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500">
            </div>
            <span class="ml-3 text-sm font-medium text-gray-700">Onaylı</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</div>
