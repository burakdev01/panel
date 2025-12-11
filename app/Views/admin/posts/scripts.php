<script>
const postsBaseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
let currentPostId = null;
let isPostImageRemoved = false;

const postModal = document.getElementById('postModal');
const postModalTitle = document.getElementById('postModalTitle');
const postIdField = document.getElementById('postId');
const postTitleInput = document.getElementById('postTitleInput');
const postContentInput = document.getElementById('postContentInput');
const postLanguageSelect = document.getElementById('postLanguageSelect');
const postStatusToggle = document.getElementById('postStatusToggle');
const postSeoTitleInput = document.getElementById('postSeoTitleInput');
const postSeoDescInput = document.getElementById('postSeoDescInput');
const postSeoUrlInput = document.getElementById('postSeoUrlInput');
const postImageUpload = document.getElementById('postImageUpload');
const postRemoveImageButton = document.getElementById('postRemoveImageButton');
const seoPreviewTitle = document.getElementById('seoPreviewTitle');
const seoPreviewUrl = document.getElementById('seoPreviewUrl');
const seoPreviewDescription = document.getElementById('seoPreviewDescription');
const seoUrlWarning = document.getElementById('seoUrlWarning');
const seoPreviewBaseUrl = '<?= rtrim(base_url(), '/') ?>/';

[postSeoTitleInput, postSeoDescInput, postSeoUrlInput].forEach(input => {
  input?.addEventListener('input', updateSeoPreview);
});

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
  if (postTitleInput) postTitleInput.value = '';
  if (postContentInput) postContentInput.value = '';
  if (postLanguageSelect && postLanguageSelect.options.length) {
    postLanguageSelect.selectedIndex = 0;
  }
  if (postStatusToggle) postStatusToggle.checked = true;
  if (postSeoTitleInput) postSeoTitleInput.value = '';
  if (postSeoDescInput) postSeoDescInput.value = '';
  if (postSeoUrlInput) postSeoUrlInput.value = '';
  if (postImageUpload) postImageUpload.value = '';
  isPostImageRemoved = false;
  setPostDefaultPreview();
  updateSeoPreview();
}

function setPostDefaultPreview() {
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
    setPostDefaultPreview();
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
  setPostDefaultPreview();
  isPostImageRemoved = currentPostId !== null;
}

function updateSeoPreview() {
  const title = (postSeoTitleInput?.value || '').trim();
  const description = (postSeoDescInput?.value || '').trim();
  const slug = (postSeoUrlInput?.value || '').trim();

  if (seoPreviewTitle) {
    seoPreviewTitle.textContent = title || 'Başlık';
  }

  if (seoPreviewDescription) {
    seoPreviewDescription.textContent = description || 'Açıklama';
  }

  if (seoPreviewUrl) {
    seoPreviewUrl.textContent = seoPreviewBaseUrl + (slug ? slug : '');
  }

  if (seoUrlWarning) {
    seoUrlWarning.classList.toggle('opacity-0', slug !== '');
  }

  if (postSeoUrlInput) {
    postSeoUrlInput.classList.toggle('border-red-300', slug === '');
    postSeoUrlInput.classList.toggle('border-gray-300', slug !== '');
  }
}

function savePost() {
  const formData = new FormData();
  formData.append('title', postTitleInput?.value ?? '');
  formData.append('content', postContentInput?.value ?? '');
  formData.append('lang_id', postLanguageSelect?.value ?? '');
  formData.append('seo_title', postSeoTitleInput?.value ?? '');
  formData.append('seo_desc', postSeoDescInput?.value ?? '');
  formData.append('seo_url', postSeoUrlInput?.value ?? '');
  formData.append('active', postStatusToggle?.checked ? '1' : '0');
  formData.append('remove_image', isPostImageRemoved ? '1' : '0');

  const file = postImageUpload?.files[0];
  if (file) {
    formData.append('image', file);
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
  if (postTitleInput) postTitleInput.value = post.title || '';
  if (postContentInput) postContentInput.value = post.content || '';
  if (postLanguageSelect) {
    const targetValue = post.lang_id !== undefined && post.lang_id !== null ? String(post.lang_id) : '';
    const exists = Array.from(postLanguageSelect.options).some(option => option.value === targetValue);
    if (!exists && targetValue) {
      const fallbackOption = new Option(`Dil ${targetValue}`, targetValue, true, true);
      postLanguageSelect.add(fallbackOption);
    }
    postLanguageSelect.value = targetValue;
  }
  if (postStatusToggle) postStatusToggle.checked = parseInt(post.active, 10) === 1;
  if (postSeoTitleInput) postSeoTitleInput.value = post.seo_title || '';
  if (postSeoDescInput) postSeoDescInput.value = post.seo_desc || '';
  if (postSeoUrlInput) postSeoUrlInput.value = post.seo_url || '';
  isPostImageRemoved = false;

  if (post.image_url) {
    const preview = document.getElementById('postImagePreview');
    if (!preview) return;
    preview.innerHTML = `<img src="${post.image_url}" class="w-full h-48 object-cover rounded-lg">`;
    togglePostRemoveButton(true);
  } else {
    setPostDefaultPreview();
  }
  updateSeoPreview();
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

updateSeoPreview();
</script>
