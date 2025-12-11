<script>
const languagesBaseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
let currentLanguageId = null;

const languageModal = document.getElementById('languageModal');
const languageModalTitle = document.getElementById('languageModalTitle');
const languageNameInput = document.getElementById('languageNameInput');
const languageIdField = document.getElementById('languageIdField');

function openLanguageModal(id = null) {
  currentLanguageId = id;
  resetLanguageForm();
  if (languageModalTitle) {
    languageModalTitle.textContent = id ? 'Dil Düzenle' : 'Dil Ekle';
  }

  if (languageModal) {
    languageModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  if (id) {
    loadLanguageData(id);
  }
}

function closeLanguageModal() {
  if (languageModal) {
    languageModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
  }
  currentLanguageId = null;
}

languageModal?.addEventListener('click', function(event) {
  if (event.target === this) {
    closeLanguageModal();
  }
});

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape' && !languageModal?.classList.contains('hidden')) {
    closeLanguageModal();
  }
});

function resetLanguageForm() {
  if (languageIdField) languageIdField.value = '';
  if (languageNameInput) languageNameInput.value = '';
}

function saveLanguage() {
  const formData = new FormData();
  formData.append('name', languageNameInput?.value ?? '');

  const url = currentLanguageId ? `${languagesBaseUrl}admin/languages/${currentLanguageId}` : `${languagesBaseUrl}admin/languages`;

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
        closeLanguageModal();
        window.location.reload();
      } else {
        handleLanguageErrors(data);
      }
    })
    .catch(() => alert('Dil kaydedilemedi.'));
}

function loadLanguageData(id) {
  fetch(`${languagesBaseUrl}admin/languages/${id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        fillLanguageForm(data.data);
      } else {
        alert(data.message || 'Dil yüklenemedi.');
      }
    })
    .catch(() => alert('Dil yüklenemedi.'));
}

function fillLanguageForm(language) {
  if (languageIdField) languageIdField.value = language.id;
  if (languageNameInput) languageNameInput.value = language.name || '';
}

function deleteLanguage(id) {
  if (!confirm('Bu dili silmek istediğinizden emin misiniz?\nİlişkili kayıtlar etkilenebilir.')) {
    return;
  }

  fetch(`${languagesBaseUrl}admin/languages/${id}`, {
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
        alert(data.message || 'Dil silinemedi.');
      }
    })
    .catch(() => alert('Dil silinemedi.'));
}

function handleLanguageErrors(data) {
  if (data.errors) {
    const messages = Object.values(data.errors).join('\n');
    alert(messages);
  } else if (data.message) {
    alert(data.message);
  } else {
    alert('Form doğrulanamadı.');
  }
}
</script>
