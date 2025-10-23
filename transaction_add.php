<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Transaksi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" />
</head>
<body class="bg-base-200 min-h-screen p-6">
  <div class="max-w-4xl mx-auto card bg-base-100 shadow-xl p-6">
    <h1 class="text-3xl font-bold text-secondary mb-6">Tambah Transaksi</h1>

    <!-- Notifikasi -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
      <div class="alert alert-success mb-4 shadow-lg">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2l4-4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
          </svg>
          <span>✅ Transaksi berhasil disimpan.</span>
        </div>
      </div>
    <?php endif; ?>

    <form action="process.php" method="POST" class="space-y-4">
      <input type="hidden" name="type" value="transaction">

      <div>
        <label class="label"><span class="label-text">Pilih Investor</span></label>
        <select name="investment_id" class="select select-bordered w-full" required>
          <option value="">-- Pilih Investor --</option>
          <?php
          $result = $conn->query("SELECT id, investor_name FROM investments");
          while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['investor_name']}</option>";
          }
          ?>
        </select>
      </div>

      <div id="transactions" class="space-y-2 mt-4">
        <h3 class="font-semibold text-lg">Daftar Transaksi</h3>
        <div class="grid grid-cols-5 gap-2 items-center">
          <input type="date" name="trans_date[]" class="input input-bordered">
          <input type="text" name="description[]" placeholder="Keterangan" class="input input-bordered">
          <select name="trans_type[]" class="select select-bordered">
            <option value="masuk">Masuk</option>
            <option value="keluar">Keluar</option>
          </select>
          <input type="number" name="amount[]" placeholder="Jumlah (Rp)" class="input input-bordered">
          <button type="button" onclick="addRow()" class="btn btn-outline btn-primary">+</button>
        </div>
      </div>

      <button type="submit" class="btn btn-secondary w-full mt-6">💾 Simpan Transaksi</button>
    </form>

    <div class="text-center mt-6">
      <a href="index.php" class="btn btn-outline">⬅️ Kembali</a>
    </div>
  </div>

  <script>
    function addRow() {
      const div = document.createElement('div');
      div.classList = 'grid grid-cols-5 gap-2 items-center';
      div.innerHTML = `
        <input type="date" name="trans_date[]" class="input input-bordered">
        <input type="text" name="description[]" placeholder="Keterangan" class="input input-bordered">
        <select name="trans_type[]" class="select select-bordered">
          <option value="masuk">Masuk</option>
          <option value="keluar">Keluar</option>
        </select>
        <input type="number" name="amount[]" placeholder="Jumlah (Rp)" class="input input-bordered">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-outline btn-error">-</button>
      `;
      document.getElementById('transactions').appendChild(div);
    }
  </script>
</body>
</html>