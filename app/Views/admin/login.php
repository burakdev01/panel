<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <title><?= $this->renderSection('title') ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <style>
  body {
    font-family: "Inter", sans-serif;
  }
  </style>
</head>

<body class="min-h-screen bg-[#eef0f7] flex flex-col items-center pt-10">

  <!-- Logo -->
  <div class="flex flex-col items-center mb-10">
    <div class="flex items-center gap-2">
      <span class="text-5xl font-semibold text-[#6bb3e9] tracking-wide">DW</span>
      <span class="text-xl font-light">| Deniz Web Ajans</span>
    </div>
  </div>

  <!-- Login Card -->
  <div class="bg-white w-[90%] max-w-[480px] rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] px-10 py-10">

    <h1 class="text-2xl font-semibold text-center mb-8">Giriş Yap</h1>

    <!-- ERROR MESSAGE -->
    <?php if(session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
      <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- FORM -->
    <form action="<?= site_url('admin/login') ?>" method="post">

      <!-- Kullanıcı Adı -->
      <div class="mb-6">
        <label class="block font-medium mb-2">Kullanıcı Adı:</label>
        <input type="text" name="username" placeholder="Kullanıcı adı"
          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-800 focus:ring-2 focus:ring-blue-300 outline-none"
          required />
      </div>

      <!-- Şifre -->
      <div class="mb-4">
        <label class="block font-medium mb-2">Şifre:</label>

        <div class="relative">
          <input id="password" name="password" type="password" placeholder="Şifre"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-12 focus:ring-2 focus:ring-blue-300 outline-none"
            required />

          <button id="togglePass" type="button" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
            👁️
          </button>
        </div>
      </div>

      <!-- Beni Hatırla -->
      <div class="flex items-center gap-2 mb-6">
        <input type="checkbox" id="remember" class="w-4 h-4 rounded border-gray-300" />
        <label for="remember" class="text-sm text-gray-700">Beni Hatırla</label>
      </div>

      <!-- Submit Button -->
      <button type="submit"
        class="w-full bg-[#55aee6] hover:bg-[#4aa3da] transition text-white font-medium py-3 rounded-lg text-lg">
        Giriş Yap
      </button>

    </form>
  </div>

  <!-- Password Toggle JS -->
  <script>
  const toggle = document.getElementById("togglePass");
  const input = document.getElementById("password");

  toggle.addEventListener("click", () => {
    input.type = input.type === "password" ? "text" : "password";
  });
  </script>
</body>

</html>