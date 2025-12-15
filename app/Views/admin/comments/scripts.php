<script>
const commentsBaseUrl = '<?= rtrim(base_url(), '/') ?>' + '/';
let currentCommentId = null;
const commentModal = document.getElementById('commentModal');
const commentModalTitle = document.getElementById('commentModalTitle');

const commentIdField = document.getElementById('commentId');
const commentPostSelect = document.getElementById('commentPostSelect');
const commentAuthorName = document.getElementById('commentAuthorName');
const commentAuthorEmail = document.getElementById('commentAuthorEmail');
const commentContent = document.getElementById('commentContent');
const commentStatusToggle = document.getElementById('commentStatusToggle');

function openCommentModal(id = null) {
  currentCommentId = id;
  resetCommentForm();
  if (commentModalTitle) {
    commentModalTitle.textContent = id ? 'Yorumu Düzenle' : 'Yorum Ekle';
  }

  if (commentModal) {
    commentModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  if (id) {
    loadCommentData(id);
  }
}

function closeCommentModal() {
  if (commentModal) {
    commentModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
  }
  currentCommentId = null;
}

function resetCommentForm() {
  if (commentIdField) commentIdField.value = '';
  if (commentPostSelect) commentPostSelect.value = '';
  if (commentAuthorName) commentAuthorName.value = '';
  if (commentAuthorEmail) commentAuthorEmail.value = '';
  if (commentContent) commentContent.value = '';
  if (commentStatusToggle) commentStatusToggle.checked = false;
}

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape' && !commentModal?.classList.contains('hidden')) {
    closeCommentModal();
  }
});

commentModal?.addEventListener('click', function(event) {
  if (event.target === this) {
    closeCommentModal();
  }
});

function saveComment() {
  const formData = new FormData();
  formData.append('post_id', commentPostSelect?.value ?? '');
  formData.append('author_name', commentAuthorName?.value ?? '');
  formData.append('author_email', commentAuthorEmail?.value ?? '');
  formData.append('content', commentContent?.value ?? '');
  formData.append('is_approved', commentStatusToggle?.checked ? '1' : '0');

  const url = currentCommentId ? `${commentsBaseUrl}admin/comments/${currentCommentId}` : `${commentsBaseUrl}admin/comments`;

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
        closeCommentModal();
        window.location.reload();
      } else {
        handleCommentErrors(data);
      }
    })
    .catch(() => alert('İşlem sırasında bir hata oluştu.'));
}

function loadCommentData(id) {
  fetch(`${commentsBaseUrl}admin/comments/${id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        fillCommentForm(data.data);
      } else {
        alert(data.message || 'Kayıt yüklenemedi.');
      }
    })
    .catch(() => alert('Kayıt yüklenemedi.'));
}

function fillCommentForm(comment) {
  if (commentIdField) commentIdField.value = comment.id;
  if (commentPostSelect) commentPostSelect.value = comment.post_id ?? '';
  if (commentAuthorName) commentAuthorName.value = comment.author_name ?? '';
  if (commentAuthorEmail) commentAuthorEmail.value = comment.author_email ?? '';
  if (commentContent) commentContent.value = comment.content ?? '';
  if (commentStatusToggle) commentStatusToggle.checked = parseInt(comment.is_approved, 10) === 1;
}

function deleteComment(id) {
  if (!confirm('Bu yorumu silmek istediğinizden emin misiniz?')) {
    return;
  }

  fetch(`${commentsBaseUrl}admin/comments/${id}`, {
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
        alert(data.message || 'Yorum silinemedi.');
      }
    })
    .catch(() => alert('Yorum silinemedi.'));
}

function handleCommentErrors(data) {
  if (data.errors) {
    const messages = Object.values(data.errors).join('\n');
    alert(messages);
  } else if (data.message) {
    alert(data.message);
  } else {
    alert('Form verileri doğrulanamadı.');
  }
}
</script>
