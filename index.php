<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lokka Investor Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" />
</head>
<body class="bg-base-200 min-h-screen">
  <div class="max-w-6xl mx-auto py-10">
    <h1 class="text-4xl font-bold text-center text-primary mb-10">Lokka Investor Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <a href="investor_add.php" class="card bg-base-100 shadow-lg hover:shadow-2xl transition-all">
        <div class="card-body items-center text-center">
          <div class="text-5xl text-primary">➕</div>
          <h2 class="card-title mt-2">Input Data Investor</h2>
          <p>Tambah investor baru beserta modal awalnya.</p>
        </div>
      </a>

      <a href="transaction_add.php" class="card bg-base-100 shadow-lg hover:shadow-2xl transition-all">
        <div class="card-body items-center text-center">
          <div class="text-5xl text-secondary">💰</div>
          <h2 class="card-title mt-2">Input Transaksi</h2>
          <p>Catat penjualan atau tambahan modal.</p>
        </div>
      </a>

      <a href="report_investor.php" class="card bg-base-100 shadow-lg hover:shadow-2xl transition-all">
        <div class="card-body items-center text-center">
          <div class="text-5xl text-info">📋</div>
          <h2 class="card-title mt-2">Laporan Investor</h2>
          <p>Lihat daftar investor dan status investasinya.</p>
        </div>
      </a>

      <a href="report_cashflow.php" class="card bg-base-100 shadow-lg hover:shadow-2xl transition-all">
        <div class="card-body items-center text-center">
          <div class="text-5xl text-success">📊</div>
          <h2 class="card-title mt-2">Cashflow & Bunga</h2>
          <p>Lihat pergerakan dana, bunga 3 bulanan, dan grafik.</p>
        </div>
      </a>
    </div>
  </div>
</body>
</html>