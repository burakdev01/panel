<div class="space-y-6">
  <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between px-6 py-5 border-b border-gray-200">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Yorum Yönetimi</h1>
        <p class="text-sm text-gray-500 mt-1">Blog yazılarına gelen yorumları görüntüleyin ve onaylayın.</p>
      </div>
      <button type="button" onclick="openCommentModal()"
        class="px-5 py-2 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus-circle"></i>
        <span>Yeni Yorum</span>
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blog Yazısı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yazan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
              İçerik</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
              Tarih</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">İşlemler</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if(isset($comments) && !empty($comments)): ?>
          <?php foreach($comments as $comment): ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">
              <?= esc($comment['post_title'] ?? ('Blog #' . $comment['post_id'])) ?>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">
              <div class="font-medium"><?= esc($comment['author_name']) ?></div>
              <div class="text-xs text-gray-500"><?= esc($comment['author_email']) ?></div>
            </td>
            <td class="px-6 py-4 hidden lg:table-cell">
              <p class="text-sm text-gray-600 truncate"><?= esc($comment['content']) ?></p>
            </td>
            <td class="px-6 py-4">
              <span
                class="px-3 py-1 text-xs font-medium rounded-full <?= ($comment['is_approved'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                <?= ($comment['is_approved'] ?? 0) ? 'Onaylı' : 'Beklemede' ?>
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
              <?= $comment['created_at'] ? date('d.m.Y H:i', strtotime($comment['created_at'])) : '-' ?>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center space-x-2">
                <button onclick="openCommentModal(<?= $comment['id'] ?>)"
                  class="text-blue-600 hover:text-blue-800 transition" title="Düzenle">
                  <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteComment(<?= $comment['id'] ?>)"
                  class="text-red-600 hover:text-red-800 transition" title="Sil">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="6" class="px-6 py-8 text-center">
              <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
              <p class="text-gray-500">Henüz yorum bulunmuyor</p>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php echo view('admin/comments/modal', ['posts' => $posts ?? []]); ?>
<?php echo view('admin/comments/scripts', ['posts' => $posts ?? []]); ?>
