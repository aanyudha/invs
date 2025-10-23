<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Investor</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" />
</head>
<body class="bg-base-200 p-6 min-h-screen">
  <div class="max-w-6xl mx-auto card bg-base-100 shadow-xl p-6">
    <h1 class="text-3xl font-bold text-info mb-6">Laporan Investor</h1>
    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Tanggal Mulai</th>
            <th>Modal Awal</th>
            <th>Total Transaksi</th>
            <th>Total Akhir + Bunga (3 Bulan)</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("
          SELECT i.*, 
            IFNULL(SUM(t.amount),0) as total_trans
          FROM investments i
          LEFT JOIN transactions t ON i.id = t.investment_id
          GROUP BY i.id
        ");
        while ($r = $res->fetch_assoc()) {
          $bunga = $r['principal'] * 0.15 / 4;
          $total = $r['principal'] + $r['total_trans'] + $bunga;
          echo "
          <tr>
            <td>{$r['investor_name']}</td>
            <td>{$r['start_date']}</td>
            <td>Rp".number_format($r['principal'],0,',','.')."</td>
            <td>Rp".number_format($r['total_trans'],0,',','.')."</td>
            <td class='text-success font-bold'>Rp".number_format($total,0,',','.')."</td>
          </tr>";
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
    <div class="text-center mt-6">
      <a href="index.php" class="btn btn-outline">⬅️ Kembali</a>
    </div>
</html>