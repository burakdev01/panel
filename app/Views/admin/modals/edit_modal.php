<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl w-full max-w-5xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">

    <!-- Modal Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h2 id="modalTitle" class="text-xl font-semibold text-gray-800">Slayt Ekle</h2>
      <div class="flex items-center space-x-2">
        <button onclick="closeEditModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
          İptal
        </button>
        <button onclick="saveContent()"
          class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition flex items-center space-x-2">
          <span>Kaydet</span>
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <!-- Modal Body -->
    <div class="flex-1 overflow-y-auto">
      <input type="hidden" id="sliderId">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

        <!-- Left Column - Image Upload -->
        <div class="lg:col-span-1">
          <div class="bg-gray-50 rounded-xl p-6 h-full">
            <label class="block text-sm font-medium text-gray-700 mb-4">Resim Seç</label>

            <!-- Image Upload Area -->
            <div
              class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer bg-white">
              <input type="file" id="imageUpload" class="hidden" accept="image/*" onchange="previewImage(event)">
              <label for="imageUpload" class="cursor-pointer">
                <div id="imagePreview" class="mb-4">
                  <i class="fas fa-image text-gray-400 text-5xl mb-3"></i>
                </div>
                <p class="text-sm text-blue-500 font-medium mb-1">Resim Seç</p>
                <p class="text-xs text-gray-500">veya sürükleyip bırakın</p>
              </label>
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

            <!-- Additional Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div>
                  <p class="text-sm text-blue-800 font-medium">Bilgi</p>
                  <p class="text-xs text-blue-600 mt-1">Tüm alanları eksiksiz doldurunuz. Resim boyutu en fazla 2MB
                    olmalıdır.</p>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
const baseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
let currentSliderId = null;
const modalTitle = document.getElementById('modalTitle');

const sliderIdField = document.getElementById('sliderId');
const titleInput = document.getElementById('titleInput');
const detailsInput = document.getElementById('detailsInput');
const linkInput = document.getElementById('linkInput');
const languageSelect = document.getElementById('languageSelect');
const statusToggle = document.getElementById('statusToggle');
const imageUpload = document.getElementById('imageUpload');

function openEditModal(id = null) {
  currentSliderId = id;
  resetForm();
  modalTitle.textContent = id ? 'Slayt Düzenle' : 'Slayt Ekle';

  const modal = document.getElementById('editModal');
  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';

  if (id) {
    loadContentData(id);
  }
}

function resetForm() {
  if (sliderIdField) sliderIdField.value = '';
  if (titleInput) titleInput.value = '';
  if (detailsInput) detailsInput.value = '';
  if (linkInput) linkInput.value = '';
  if (languageSelect && languageSelect.options.length) {
    languageSelect.selectedIndex = 0;
  }
  if (statusToggle) statusToggle.checked = true;
  if (imageUpload) imageUpload.value = '';
  setDefaultPreview();
}

function closeEditModal() {
  const modal = document.getElementById('editModal');
  modal.classList.add('hidden');
  document.body.style.overflow = 'auto';
  currentSliderId = null;
}

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeEditModal();
  }
});

document.getElementById('editModal')?.addEventListener('click', function(event) {
  if (event.target === this) {
    closeEditModal();
  }
});

function setDefaultPreview() {
  const preview = document.getElementById('imagePreview');
  if (!preview) return;
  preview.innerHTML = '<i class="fas fa-image text-gray-400 text-5xl mb-3"></i>';
}

function previewImage(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('imagePreview');
      if (!preview) return;
      preview.innerHTML = `<img src="${e.target.result}" class="w-full h-48 object-cover rounded-lg">`;
    };
    reader.readAsDataURL(file);
  } else {
    setDefaultPreview();
  }
}

function saveContent() {

  const formData = new FormData();
  formData.append('title', titleInput?.value ?? '');
  formData.append('details', detailsInput?.value ?? '');
  formData.append('links', linkInput?.value ?? '');
  formData.append('lang_id', languageSelect?.value ?? '');
  formData.append('active', statusToggle?.checked ? '1' : '0');

  const file = imageUpload?.files[0];
  if (file) {
    formData.append('image', file);
  }

  const url = currentSliderId ? `${baseUrl}admin/sliders/${currentSliderId}` : `${baseUrl}admin/sliders`;

  fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closeEditModal();
        window.location.reload();
      } else {
        handleErrors(data);
      }
    })
    .catch(() => alert('Beklenmeyen bir hata oluştu.'));
}

function loadContentData(id) {
  fetch(`${baseUrl}admin/sliders/${id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        fillForm(data.data);
      } else {
        alert(data.message || 'Kayıt yüklenemedi.');
      }
    })
    .catch(() => alert('Kayıt yüklenemedi.'));
}

function fillForm(slider) {
  if (sliderIdField) sliderIdField.value = slider.id;
  if (titleInput) titleInput.value = slider.title || '';
  if (detailsInput) detailsInput.value = slider.details || '';
  if (linkInput) linkInput.value = slider.links || '';
  if (languageSelect) languageSelect.value = slider.lang_id || '';
  if (statusToggle) statusToggle.checked = parseInt(slider.active, 10) === 1;

  if (slider.image_url) {
    const preview = document.getElementById('imagePreview');
    if (!preview) return;
    preview.innerHTML = `<img src="${slider.image_url}" class="w-full h-48 object-cover rounded-lg">`;
  } else {
    setDefaultPreview();
  }
}

function handleErrors(data) {
  if (data.errors) {
    const messages = Object.values(data.errors).join('\n');
    alert(messages);
  } else if (data.message) {
    alert(data.message);
  } else {
    alert('Form verileri doğrulanamadı.');
  }
}

function deleteSlider(id) {
  if (!confirm('Bu slaytı silmek istediğinizden emin misiniz?')) {
    return;
  }

  fetch(`${baseUrl}admin/sliders/${id}`, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Slayt silinemedi.');
      }
    })
    .catch(() => alert('Slayt silinemedi.'));
}
</script>