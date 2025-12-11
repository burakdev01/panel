<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <title><?= esc($title ?? "Yönetim Paneli") ?></title>

  <script src="https://cdn.tailwindcss.com"></script>

  <style>
  body {
    font-family: "Inter", sans-serif;
  }
  </style>
</head>

<body class="bg-[#f4f5f9] h-screen flex overflow-hidden">

  <!-- MOBILE OVERLAY -->
  <div id="overlay" class="fixed inset-0 bg-black/40 z-20 hidden md:hidden" onclick="toggleSidebar()"></div>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="w-64 bg-white shadow-lg border-r border-gray-200 flex flex-col z-30
                transform transition-transform duration-300 md:translate-x-0 -translate-x-full">

    <!-- Logo -->
    <div class="px-6 py-6 border-b flex items-center gap-2">
      <span class="text-4xl font-extrabold text-[#6bb3e9] leading-none">DW</span>
      <span class="text-lg text-gray-700">Deniz Web Ajans</span>
    </div>

    <!-- MENU -->
    <nav class="px-4 py-5 flex-1 space-y-2 text-gray-700">

      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eef4ff] text-[#4f6bff] font-medium">
        <span class="text-xl">🏠</span> Ana Sayfa
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">🖼️</span> Slayt Yönetimi
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">📦</span> Hizmet Yönetimi
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">💬</span> Yorum Yönetimi
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">🎬</span> Video Yönetimi
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">📸</span> Fotoğraf Yönetimi
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">📝</span> Blog Yönetimi
      </a>

      <a href="#" class="menu-item">
        <span class="text-xl">❓</span> SSS Yönetimi
      </a>

    </nav>

    <style>
    .menu-item {
      @apply flex items-center gap-3 px-4 py-3 rounded-xl hover: bg-gray-100 transition;
    }
    </style>

  </aside>

  <!-- MAIN CONTENT AREA -->
  <div class="flex-1 flex flex-col overflow-hidden">

    <!-- TOP NAVBAR -->
    <header class="bg-white h-16 flex items-center justify-between px-6 shadow-sm border-b">

      <!-- MOBILE MENU BUTTON -->
      <button class="text-3xl md:hidden text-gray-700" onclick="toggleSidebar()">☰</button>

      <!-- RIGHT PROFILE AREA -->
      <div class="flex items-center gap-3 relative">

        <img src="https://i.pravatar.cc/44" class="w-11 h-11 rounded-full border shadow-sm cursor-pointer"
          onclick="toggleUserMenu()">

        <div class="text-right">
          <div class="font-semibold"><?= esc($username ?? "burak burak") ?></div>
          <div class="text-sm text-gray-500">Yönetici</div>
        </div>

        <!-- DROPDOWN -->
        <div id="dropdown" class="absolute top-14 right-0 w-40 bg-white shadow-xl border rounded-lg py-2 hidden">
          <a href="#" class="dropdown-item">Profil</a>
          <a href="/admin/logout" class="dropdown-item">Çıkış Yap</a>
        </div>

        <style>
        .dropdown-item {
          @apply block px-4 py-2 hover: bg-gray-100 text-gray-700;
        }
        </style>

      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="flex-1 overflow-auto p-6">
      <?= $this->renderSection("content") ?>
    </main>
  </div>

  <!-- JS -->
  <script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    const isOpen = !sidebar.classList.contains("-translate-x-full");

    if (isOpen) {
      sidebar.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
    } else {
      sidebar.classList.remove("-translate-x-full");
      overlay.classList.remove("hidden");
    }
  }

  function toggleUserMenu() {
    const menu = document.getElementById("dropdown");
    menu.classList.toggle("hidden");
  }

  document.addEventListener("click", (e) => {
    if (!e.target.closest("#dropdown") && !e.target.closest(".rounded-full")) {
      document.getElementById("dropdown").classList.add("hidden");
    }
  });
  </script>

</body>

</html>