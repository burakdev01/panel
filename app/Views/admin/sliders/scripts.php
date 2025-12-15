<script>
const baseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
let currentSliderId = null;
const modalTitle = document.getElementById('modalTitle');

const sliderIdField = document.getElementById('sliderId');
const statusToggle = document.getElementById('statusToggle');
const imageUpload = document.getElementById('imageUpload');
const removeImageButton = document.getElementById('removeImageButton');
let isImageRemoved = false;

const sliderLanguages = <?= json_encode($languages ?? []) ?>;
const sliderVariantKeys = ['title', 'details', 'links'];
const sliderVariantFields = {};
let sliderActiveLangId = sliderLanguages.length ? String(sliderLanguages[0].id) : null;

sliderLanguages.forEach(language => {
  const langId = String(language.id);
  sliderVariantFields[langId] = {
    id: document.getElementById(`sliderVariantId_${langId}`),
    title: document.getElementById(`sliderVariantTitle_${langId}`),
    details: document.getElementById(`sliderVariantDetails_${langId}`),
    links: document.getElementById(`sliderVariantLink_${langId}`),
  };
});

const sliderLangTabs = document.querySelectorAll('[data-slider-lang-tab]');
const sliderLangPanes = document.querySelectorAll('[data-slider-lang-pane]');

sliderLangTabs.forEach(tab => {
  tab.addEventListener('click', () => {
    const langId = tab.getAttribute('data-slider-lang-tab');
    setActiveSliderLang(langId);
  });
});

function setActiveSliderLang(langId) {
  if (!langId) {
    return;
  }
  sliderActiveLangId = langId;

  sliderLangTabs.forEach(tab => {
    const tabLang = tab.getAttribute('data-slider-lang-tab');
    if (tabLang === langId) {
      tab.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-200');
      tab.classList.remove('text-gray-600', 'border-transparent');
    } else {
      tab.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-200');
      tab.classList.add('text-gray-600', 'border-transparent');
    }
  });

  sliderLangPanes.forEach(pane => {
    pane.classList.toggle('hidden', pane.getAttribute('data-slider-lang-pane') !== langId);
  });
}

function openEditModal(id = null) {
  if (!sliderLanguages.length) {
    alert('Dil tanımı olmadan slider eklenemez. Lütfen önce dil ekleyin.');
    return;
  }

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

  if (sliderActiveLangId) {
    setActiveSliderLang(sliderActiveLangId);
  }

  if (id) {
    loadContentData(id);
  }
}

function resetForm() {
  if (sliderIdField) sliderIdField.value = '';
  if (statusToggle) statusToggle.checked = true;
  if (imageUpload) imageUpload.value = '';
  isImageRemoved = false;
  setDefaultPreview();
  resetSliderVariantFields();
  if (sliderLanguages.length) {
    setActiveSliderLang(String(sliderLanguages[0].id));
  }
}

function resetSliderVariantFields() {
  Object.keys(sliderVariantFields).forEach(langId => {
    const fields = sliderVariantFields[langId];
    if (!fields) {
      return;
    }
    if (fields.id) fields.id.value = '';
    sliderVariantKeys.forEach(key => {
      if (fields[key]) {
        fields[key].value = '';
      }
    });
  });
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

function sliderVariantHasContent(fields) {
  if (!fields) {
    return false;
  }

  return sliderVariantKeys.some(key => {
    const value = fields[key]?.value ?? '';
    return value.trim() !== '';
  });
}

function saveContent() {
  if (!sliderLanguages.length) {
    alert('Dil tanımı olmadığı için slider eklenemez.');
    return;
  }

  const formData = new FormData();
  formData.append('active', statusToggle?.checked ? '1' : '0');
  formData.append('remove_image', isImageRemoved ? '1' : '0');

  const file = imageUpload?.files[0];
  if (file) {
    formData.append('image', file);
  }

  let hasAnyVariant = false;

  sliderLanguages.forEach(language => {
    const langId = String(language.id);
    const fields = sliderVariantFields[langId];
    if (!fields) {
      return;
    }

    const variantId = (fields.id?.value || '').trim();
    const hasContent = sliderVariantHasContent(fields);

    if (!variantId && !hasContent) {
      return;
    }

    hasAnyVariant = true;
    const prefix = `variants[${langId}]`;
    formData.append(`${prefix}[id]`, variantId);

    if (!hasContent && variantId) {
      sliderVariantKeys.forEach(key => {
        formData.append(`${prefix}[${key}]`, '');
      });
      return;
    }

    formData.append(`${prefix}[title]`, fields.title?.value ?? '');
    formData.append(`${prefix}[details]`, fields.details?.value ?? '');
    formData.append(`${prefix}[links]`, fields.links?.value ?? '');
  });

  if (!hasAnyVariant) {
    alert('En az bir dil için içerik girmelisiniz.');
    return;
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
  if (statusToggle) statusToggle.checked = parseInt(slider.active, 10) === 1;
  isImageRemoved = false;

  const variantMap = {};
  (slider.variants || []).forEach(variant => {
    variantMap[String(variant.lang_id)] = variant;
  });

  sliderLanguages.forEach(language => {
    const langId = String(language.id);
    const fields = sliderVariantFields[langId];
    const variant = variantMap[langId] || null;

    if (!fields) {
      return;
    }

    if (fields.id) fields.id.value = variant?.id ?? '';
    if (fields.title) fields.title.value = variant?.title ?? '';
    if (fields.details) fields.details.value = variant?.details ?? '';
    if (fields.links) fields.links.value = variant?.links ?? '';
  });

  const firstWithContent = sliderLanguages.find(language => {
    const langId = String(language.id);
    return sliderVariantHasContent(sliderVariantFields[langId]);
  });

  setActiveSliderLang(firstWithContent ? String(firstWithContent.id) : String(sliderLanguages[0].id));

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

if (sliderActiveLangId) {
  setActiveSliderLang(sliderActiveLangId);
}
</script>
