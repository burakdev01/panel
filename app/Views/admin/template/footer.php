</main>
</div>
</div>

<!-- Sortable.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
function toggleMobileSidebar() {
  const sidebar = document.getElementById('mobile-sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  sidebar.classList.toggle('-translate-x-full');
  overlay.classList.toggle('hidden');
}

function closeMobileSidebar() {
  const sidebar = document.getElementById('mobile-sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  sidebar.classList.add('-translate-x-full');
  overlay.classList.add('hidden');
}

function toggleDropdown(id) {
  const menu = document.getElementById(id + '-menu');
  const icon = document.getElementById(id + '-icon');

  menu.classList.toggle('hidden');
  icon.classList.toggle('rotate-90');
}

// Close mobile sidebar when clicking outside
document.addEventListener('click', function(event) {
  const sidebar = document.getElementById('mobile-sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const menuButton = event.target.closest('button[onclick="toggleMobileSidebar()"]');

  if (!sidebar.contains(event.target) && !menuButton && !sidebar.classList.contains('-translate-x-full')) {
    closeMobileSidebar();
  }
});

// Auto-hide flash messages after 5 seconds
setTimeout(function() {
  const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
  alerts.forEach(function(alert) {
    // Sadece flash mesajlarını kapat (parent elementi div olan)
    if (alert.tagName === 'DIV' && (alert.classList.contains('border-green-200') || alert.classList.contains(
        'border-red-200'))) {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(function() {
        alert.remove();
      }, 500);
    }
  });
}, 5000);

// Sortable Table - Drag & Drop
const sortableTable = document.getElementById('sortable-table');
if (sortableTable) {
  new Sortable(sortableTable, {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'bg-blue-50',
    dragClass: 'opacity-50',
    onEnd: function(evt) {
      // Sıralama değiştiğinde çalışır
      const itemId = evt.item.getAttribute('data-id');
      const oldIndex = evt.oldIndex;
      const newIndex = evt.newIndex;

      console.log('Item ID:', itemId);
      console.log('Eski Sıra:', oldIndex);
      console.log('Yeni Sıra:', newIndex);

      // AJAX ile sıralamayı kaydet
      updateOrder(itemId, newIndex);
    }
  });
}

// Sıralama güncellemesi için AJAX fonksiyonu
function updateOrder(itemId, newOrder) {
  // Şu an için sadece console'a yazdır
  // Route tanımlandığında aktif hale gelecek
  console.log('Sıralama güncellendi:');
  console.log('- ID:', itemId);
  console.log('- Yeni Sıra:', newOrder);

  // AJAX kullanmak istersen, önce route tanımla:
  /*
  fetch('<?= base_url('admin/content/update-order') ?>', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
          id: itemId,
          order: newOrder
      })
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          console.log('✅ Sıralama veritabanına kaydedildi');
      } else {
          console.error('❌ Sıralama güncellenemedi:', data.message);
      }
  })
  .catch(error => {
      console.error('❌ AJAX Hatası:', error);
  });
  */
}

// Delete confirmation
function deleteContent(id) {
  if (confirm('Bu içeriği silmek istediğinizden emin misiniz?')) {
    window.location.href = '<?= base_url('admin/content/delete/') ?>' + id;
  }
}
</script>
</body>

</html>