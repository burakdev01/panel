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
const removeImageButton = document.getElementById('removeImageButton');
let isImageRemoved = false;

function openEditModal(id = null) {
  currentSliderId = id;
  resetForm();
  if (modalTitle) {
    modalTitle.textContent = id ? 'Slayt Düzenle' : 'Slayt Ekle';
  }

  const modal = document.getElementById('editModal');
  if (modal) {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

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
  isImageRemoved = false;
  setDefaultPreview();
}

function closeEditModal() {
  const modal = document.getElementById('editModal');
  if (modal) {
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
  }
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
  toggleRemoveImageButton(false);
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
    isImageRemoved = false;
    toggleRemoveImageButton(true);
  } else {
    setDefaultPreview();
  }
}

function toggleRemoveImageButton(show) {
  if (!removeImageButton) return;
  if (show) {
    removeImageButton.classList.remove('hidden');
  } else {
    removeImageButton.classList.add('hidden');
  }
}

function removeSelectedImage() {
  if (imageUpload) {
    imageUpload.value = '';
  }
  setDefaultPreview();
  isImageRemoved = currentSliderId !== null;
}

function saveContent() {
  const formData = new FormData();
  formData.append('title', titleInput?.value ?? '');
  formData.append('details', detailsInput?.value ?? '');
  formData.append('links', linkInput?.value ?? '');
  formData.append('lang_id', languageSelect?.value ?? '');
  formData.append('active', statusToggle?.checked ? '1' : '0');
  formData.append('remove_image', isImageRemoved ? '1' : '0');

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
  isImageRemoved = false;

  if (slider.image_url) {
    const preview = document.getElementById('imagePreview');
    if (!preview) return;
    preview.innerHTML = `<img src="${slider.image_url}" class="w-full h-48 object-cover rounded-lg">`;
    toggleRemoveImageButton(true);
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
