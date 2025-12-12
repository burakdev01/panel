<script>
const postsBaseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
const postLanguages = <?= json_encode($languages ?? []) ?>;
const variantValueKeys = ['title', 'content', 'seo_title', 'seo_desc', 'seo_url'];
let currentPostId = null;
let isPostImageRemoved = false;
let activeLangId = postLanguages.length ? String(postLanguages[0].id) : null;

const postModal = document.getElementById('postModal');
const postModalTitle = document.getElementById('postModalTitle');
const postIdField = document.getElementById('postId');
const postStatusToggle = document.getElementById('postStatusToggle');
const postImageUpload = document.getElementById('postImageUpload');
const postRemoveImageButton = document.getElementById('postRemoveImageButton');
const seoPreviewBaseUrl = '<?= rtrim(base_url(), '/') ?>/';

const langTabs = document.querySelectorAll('[data-lang-tab]');
const langPanes = document.querySelectorAll('[data-lang-pane]');
const variantFields = {};

postLanguages.forEach(language => {
  const langId = String(language.id);
  variantFields[langId] = {
    id: document.getElementById(`variantId_${langId}`),
    title: document.getElementById(`variantTitle_${langId}`),
    content: document.getElementById(`variantContent_${langId}`),
    seo_title: document.getElementById(`variantSeoTitle_${langId}`),
    seo_desc: document.getElementById(`variantSeoDesc_${langId}`),
    seo_url: document.getElementById(`variantSeoUrl_${langId}`),
    previewTitle: document.getElementById(`seoPreviewTitle_${langId}`),
    previewUrl: document.getElementById(`seoPreviewUrl_${langId}`),
    previewDesc: document.getElementById(`seoPreviewDescription_${langId}`),
    seoUrlWarning: document.getElementById(`seoUrlWarning_${langId}`),
  };

  ['seo_title', 'seo_desc', 'seo_url'].forEach(field => {
    const input = variantFields[langId][field];
    input?.addEventListener('input', () => updateSeoPreview(langId));
  });
});

langTabs.forEach(tab => {
  tab.addEventListener('click', () => {
    const langId = tab.getAttribute('data-lang-tab');
    setActiveLang(langId);
  });
});

function setActiveLang(langId) {
  if (!langId) {
    return;
  }
  activeLangId = langId;
  langTabs.forEach(tab => {
    const tabLang = tab.getAttribute('data-lang-tab');
    if (tabLang === langId) {
      tab.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-200');
      tab.classList.remove('text-gray-600', 'border-transparent');
    } else {
      tab.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-200');
      tab.classList.add('text-gray-600', 'border-transparent');
    }
  });

  langPanes.forEach(pane => {
    pane.classList.toggle('hidden', pane.getAttribute('data-lang-pane') !== langId);
  });
}

function openPostModal(id = null) {
  currentPostId = id;
  resetPostForm();
  if (postModalTitle) {
    postModalTitle.textContent = id ? 'Blog Yazısı Düzenle' : 'Blog Yazısı Ekle';
  }

  if (postModal) {
    postModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  if (postLanguages.length && activeLangId === null) {
    activeLangId = String(postLanguages[0].id);
  }
  if (activeLangId) {
    setActiveLang(activeLangId);
  }

  if (id) {
    loadPostData(id);
  }
}

function closePostModal() {
  if (postModal) {
    postModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
  }
  currentPostId = null;
}

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape' && !postModal?.classList.contains('hidden')) {
    closePostModal();
  }
});

postModal?.addEventListener('click', function(event) {
  if (event.target === this) {
    closePostModal();
  }
});

function resetPostForm() {
  if (postIdField) postIdField.value = '';
  if (postStatusToggle) postStatusToggle.checked = true;
  if (postImageUpload) postImageUpload.value = '';
  isPostImageRemoved = false;
  resetPostImagePreview();
  resetVariantFields();
  if (postLanguages.length) {
    setActiveLang(String(postLanguages[0].id));
  }
}

function resetVariantFields() {
  Object.keys(variantFields).forEach(langId => {
    const fields = variantFields[langId];
    if (!fields) {
      return;
    }
    if (fields.id) fields.id.value = '';
    variantValueKeys.forEach(key => {
      if (fields[key]) {
        fields[key].value = '';
      }
    });
    updateSeoPreview(langId);
  });
}

function resetPostImagePreview() {
  const preview = document.getElementById('postImagePreview');
  if (!preview) return;
  preview.innerHTML = '<i class="fas fa-image text-gray-400 text-5xl mb-3"></i>';
  togglePostRemoveButton(false);
}

function previewPostImage(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('postImagePreview');
      if (!preview) return;
      preview.innerHTML = `<img src="${e.target.result}" class="w-full h-48 object-cover rounded-lg">`;
    };
    reader.readAsDataURL(file);
    isPostImageRemoved = false;
    togglePostRemoveButton(true);
  } else {
    resetPostImagePreview();
  }
}

function togglePostRemoveButton(show) {
  if (!postRemoveImageButton) return;
  if (show) {
    postRemoveImageButton.classList.remove('hidden');
  } else {
    postRemoveImageButton.classList.add('hidden');
  }
}

function removeSelectedPostImage() {
  if (postImageUpload) {
    postImageUpload.value = '';
  }
  resetPostImagePreview();
  isPostImageRemoved = currentPostId !== null;
}

function updateSeoPreview(langId) {
  const fields = variantFields[langId];
  if (!fields) return;

  const title = (fields.seo_title?.value || '').trim();
  const description = (fields.seo_desc?.value || '').trim();
  const slug = (fields.seo_url?.value || '').trim();

  if (fields.previewTitle) {
    fields.previewTitle.textContent = title || 'Başlık';
  }

  if (fields.previewDesc) {
    fields.previewDesc.textContent = description || 'Açıklama';
  }

  if (fields.previewUrl) {
    fields.previewUrl.textContent = seoPreviewBaseUrl + (slug ? slug : '');
  }

  if (fields.seoUrlWarning) {
    fields.seoUrlWarning.classList.toggle('opacity-0', slug !== '');
  }

  if (fields.seo_url) {
    fields.seo_url.classList.toggle('border-red-300', slug === '');
    fields.seo_url.classList.toggle('border-gray-300', slug !== '');
  }
}

function variantHasContent(fields) {
  if (!fields) {
    return false;
  }

  return variantValueKeys.some(key => {
    const value = fields[key]?.value ?? '';
    return value.trim() !== '';
  });
}

function savePost() {
  if (!postLanguages.length) {
    alert('Dil tanımı olmadığı için blog yazısı eklenemez. Lütfen önce dil ekleyin.');
    return;
  }

  const formData = new FormData();
  formData.append('active', postStatusToggle?.checked ? '1' : '0');
  formData.append('remove_image', isPostImageRemoved ? '1' : '0');

  const file = postImageUpload?.files[0];
  if (file) {
    formData.append('image', file);
  }

  let hasAnyVariant = false;

  postLanguages.forEach(language => {
    const langId = String(language.id);
    const fields = variantFields[langId];
    if (!fields) {
      return;
    }

    const variantId = (fields.id?.value || '').trim();
    const hasContent = variantHasContent(fields);

    if (!variantId && !hasContent) {
      return;
    }

    hasAnyVariant = true;
    const prefix = `variants[${langId}]`;
    formData.append(`${prefix}[id]`, variantId);

    if (!hasContent && variantId) {
      variantValueKeys.forEach(key => {
        formData.append(`${prefix}[${key}]`, '');
      });
      return;
    }

    formData.append(`${prefix}[title]`, fields.title?.value ?? '');
    formData.append(`${prefix}[content]`, fields.content?.value ?? '');
    formData.append(`${prefix}[seo_title]`, fields.seo_title?.value ?? '');
    formData.append(`${prefix}[seo_desc]`, fields.seo_desc?.value ?? '');
    formData.append(`${prefix}[seo_url]`, fields.seo_url?.value ?? '');
  });

  if (!hasAnyVariant) {
    alert('En az bir dil için içerik girmelisiniz.');
    return;
  }

  const url = currentPostId ? `${postsBaseUrl}admin/posts/${currentPostId}` : `${postsBaseUrl}admin/posts`;

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
        closePostModal();
        window.location.reload();
      } else {
        handlePostErrors(data);
      }
    })
    .catch(() => alert('Beklenmeyen bir hata oluştu.'));
}

function loadPostData(id) {
  fetch(`${postsBaseUrl}admin/posts/${id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        fillPostForm(data.data);
      } else {
        alert(data.message || 'Kayıt yüklenemedi.');
      }
    })
    .catch(() => alert('Kayıt yüklenemedi.'));
}

function fillPostForm(post) {
  if (postIdField) postIdField.value = post.id;
  if (postStatusToggle) postStatusToggle.checked = parseInt(post.active, 10) === 1;
  isPostImageRemoved = false;

  const variantMap = {};
  (post.variants || []).forEach(variant => {
    variantMap[String(variant.lang_id)] = variant;
  });

  postLanguages.forEach(language => {
    const langId = String(language.id);
    const fields = variantFields[langId];
    const variant = variantMap[langId] || null;

    if (!fields) {
      return;
    }

    if (fields.id) fields.id.value = variant?.id ?? '';
    if (fields.title) fields.title.value = variant?.title ?? '';
    if (fields.content) fields.content.value = variant?.content ?? '';
    if (fields.seo_title) fields.seo_title.value = variant?.seo_title ?? '';
    if (fields.seo_desc) fields.seo_desc.value = variant?.seo_desc ?? '';
    if (fields.seo_url) fields.seo_url.value = variant?.seo_url ?? '';
    updateSeoPreview(langId);
  });

  const preview = document.getElementById('postImagePreview');
  if (preview && post.image_url) {
    preview.innerHTML = `<img src="${post.image_url}" class="w-full h-48 object-cover rounded-lg">`;
    togglePostRemoveButton(true);
  } else {
    resetPostImagePreview();
  }

  const firstWithContent = findFirstLangWithContent();
  setActiveLang(firstWithContent || (postLanguages.length ? String(postLanguages[0].id) : null));
}

function findFirstLangWithContent() {
  for (const language of postLanguages) {
    const langId = String(language.id);
    if (variantHasContent(variantFields[langId])) {
      return langId;
    }
  }
  return null;
}

function handlePostErrors(data) {
  if (data.errors) {
    const messages = Object.values(data.errors).join('\n');
    alert(messages);
  } else if (data.message) {
    alert(data.message);
  } else {
    alert('Form verileri doğrulanamadı.');
  }
}

function deletePost(id) {
  if (!confirm('Bu blog yazısını silmek istediğinizden emin misiniz?')) {
    return;
  }

  fetch(`${postsBaseUrl}admin/posts/${id}`, {
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
        alert(data.message || 'Blog yazısı silinemedi.');
      }
    })
    .catch(() => alert('Blog yazısı silinemedi.'));
}

Object.keys(variantFields).forEach(langId => updateSeoPreview(langId));
if (activeLangId) {
  setActiveLang(activeLangId);
}
</script>
