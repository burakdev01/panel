<script>
const settingsBaseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
const siteBaseUrlInput = document.getElementById('siteBaseUrl');
const googleAnalyticsInput = document.getElementById('googleAnalytics');
const googleSearchConsoleInput = document.getElementById('googleSearchConsole');
const smtpHostInput = document.getElementById('smtpHost');
const smtpUserInput = document.getElementById('smtpUser');
const smtpPasswordInput = document.getElementById('smtpPassword');
const smtpPortInput = document.getElementById('smtpPort');
const languageSelect = document.getElementById('settingsLanguageSelect');
const siteTitleInput = document.getElementById('siteTitleInput');
const metaTitleInput = document.getElementById('metaTitleInput');
const metaKeywordsInput = document.getElementById('metaKeywordsInput');
const metaDescriptionInput = document.getElementById('metaDescriptionInput');
const metaAuthorInput = document.getElementById('metaAuthorInput');
const footerDescriptionInput = document.getElementById('footerDescriptionInput');

languageSelect?.addEventListener('change', function() {
  loadLanguageSettings(this.value);
});

if (languageSelect?.value) {
  loadLanguageSettings(languageSelect.value);
}

function saveGeneralSettings() {
  const formData = new FormData();
  formData.append('site_base_url', siteBaseUrlInput?.value ?? '');
  formData.append('google_analytics', googleAnalyticsInput?.value ?? '');
  formData.append('google_search_console', googleSearchConsoleInput?.value ?? '');
  formData.append('smtp_host', smtpHostInput?.value ?? '');
  formData.append('smtp_user', smtpUserInput?.value ?? '');
  formData.append('smtp_password', smtpPasswordInput?.value ?? '');
  formData.append('smtp_port', smtpPortInput?.value ?? '');

  fetch(`${settingsBaseUrl}admin/settings/general`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Genel ayarlar kaydedildi.');
      } else {
        handleSettingsErrors(data);
      }
    })
    .catch(() => alert('Genel ayarlar kaydedilemedi.'));
}

function saveLanguageSettings() {
  const langId = languageSelect?.value;
  if (!langId) {
    alert('Lütfen bir dil seçin.');
    return;
  }

  const formData = new FormData();
  formData.append('site_title', siteTitleInput?.value ?? '');
  formData.append('meta_title', metaTitleInput?.value ?? '');
  formData.append('meta_keywords', metaKeywordsInput?.value ?? '');
  formData.append('meta_description', metaDescriptionInput?.value ?? '');
  formData.append('meta_author', metaAuthorInput?.value ?? '');
  formData.append('footer_description', footerDescriptionInput?.value ?? '');

  fetch(`${settingsBaseUrl}admin/settings/translation/${langId}`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Dil ayarları kaydedildi.');
      } else {
        handleSettingsErrors(data);
      }
    })
    .catch(() => alert('Dil ayarları kaydedilemedi.'));
}

function loadLanguageSettings(langId) {
  fetch(`${settingsBaseUrl}admin/settings/translation/${langId}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const translation = data.data || {};
        siteTitleInput.value = translation.site_title || '';
        metaTitleInput.value = translation.meta_title || '';
        metaKeywordsInput.value = translation.meta_keywords || '';
        metaDescriptionInput.value = translation.meta_description || '';
        metaAuthorInput.value = translation.meta_author || '';
        footerDescriptionInput.value = translation.footer_description || '';
      } else {
        handleSettingsErrors(data);
      }
    })
    .catch(() => alert('Dil ayarları yüklenemedi.'));
}

function handleSettingsErrors(data) {
  if (data.errors) {
    const messages = Object.values(data.errors).join('\n');
    alert(messages);
  } else if (data.message) {
    alert(data.message);
  } else {
    alert('İşlem tamamlanamadı.');
  }
}
</script>
