<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Investor - Lokka Investor Tool</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" />
</head>
<body class="bg-gradient-to-br from-blue-50 to-gray-100 min-h-screen p-6">
  <div class="max-w-5xl mx-auto card bg-base-100 shadow-xl p-8">
    <h1 class="text-3xl font-bold text-primary mb-6">Input Data Investor</h1>

    <!-- Notifikasi -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
      <div class="alert alert-success mb-6 shadow-lg">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2l4-4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
          </svg>
          <span>✅ Data investor berhasil disimpan / dihapus.</span>
        </div>
      </div>
    <?php endif; ?>

    <!-- Form Input -->
    <form action="process.php" method="POST" class="space-y-4">
      <input type="hidden" name="type" value="investor">

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="label"><span class="label-text">Nama Investor</span></label>
          <input type="text" name="investor_name" class="input input-bordered w-full" required autofocus>
        </div>
        <div>
          <label class="label"><span class="label-text">Tanggal Mulai</span></label>
          <input type="date" name="start_date" class="input input-bordered w-full" required>
        </div>
        <div>
          <label class="label"><span class="label-text">Modal Awal (Rp)</span></label>
          <input type="number" name="principal" class="input input-bordered w-full" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full mt-6">💾 Simpan Data</button>
    </form>

    <!-- Daftar Investor -->
    <h2 class="text-2xl font-semibold mt-10 mb-4">📋 Daftar Investor</h2>
    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr class="bg-base-200">
            <th>No</th>
            <th>Nama Investor</th>
            <th>Tanggal Mulai</th>
            <th>Modal Awal (Rp)</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM investments ORDER BY id DESC");
          if ($result->num_rows > 0):
              $no = 1;
              while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['investor_name']) ?></td>
              <td><?= htmlspecialchars($row['start_date']) ?></td>
              <td><?= number_format($row['principal'], 0, ',', '.') ?></td>
              <td>
                <form action="process.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus investor ini?')" class="inline">
                  <input type="hidden" name="type" value="delete_investor">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <button type="submit" class="btn btn-error btn-sm">🗑️ Hapus</button>
                </form>
              </td>
            </tr>
          <?php
              endwhile;
          else:
          ?>
            <tr>
              <td colspan="5" class="text-center text-gray-500">Belum ada data investor.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-6">
      <a href="index.php" class="btn btn-outline">⬅️ Kembali</a>
    </div>
  </div>
</body>
</html>