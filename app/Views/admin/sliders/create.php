<div class="space-y-6">
  <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between px-6 py-5 border-b border-gray-200">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Yeni Slider Ekle</h1>
        <p class="text-sm text-gray-500 mt-1">Edit modalındaki formun birebir aynısı ile yeni slayt oluşturun.</p>
      </div>
      <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
        <button type="button" onclick="resetForm()"
          class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
          Formu Temizle
        </button>
        <button type="button" onclick="saveContent()"
          class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm transition flex items-center justify-center gap-2">
          <span>Kaydet</span>
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <div class="p-6">
      <?php echo view('admin/sliders/partials/form', ['languages' => $languages ?? []]); ?>
    </div>
  </div>
</div>

<?php echo view('admin/sliders/scripts'); ?>
