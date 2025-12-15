<div id="postModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl w-full max-w-5xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">

    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h2 id="postModalTitle" class="text-xl font-semibold text-gray-800">Blog Yazısı Ekle</h2>
      <div class="flex items-center space-x-2">
        <button onclick="closePostModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
          İptal
        </button>
        <button onclick="savePost()"
          class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition flex items-center space-x-2">
          <span>Kaydet</span>
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
      <?php echo view('admin/posts/form', ['languages' => $languages ?? []]); ?>
    </div>

  </div>
</div>