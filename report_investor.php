Baik
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
    <h1 class="text-3xl font-bold text-info mb-6">📊 Laporan Investor</h1>

    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr>
            <th>Nama Investor</th>
            <th>Tanggal Mulai</th>
            <th>Modal Awal</th>
            <th>Total Masuk</th>
            <th>Total Keluar</th>
            <th>Saldo Bersih</th>
            <th>Total Akhir + Bunga (3 Bulan)</th>
          </tr>
        </thead>
        <tbody>
        <?php
        // Ambil semua investor dan hitung total masuk / keluar
        $sql = "
          SELECT 
            i.id,
            i.investor_name,
            i.start_date,
            i.principal,
            COALESCE(SUM(CASE WHEN t.trans_type='masuk' THEN t.amount ELSE 0 END),0) AS total_masuk,
            COALESCE(SUM(CASE WHEN t.trans_type='keluar' THEN t.amount ELSE 0 END),0) AS total_keluar
          FROM investments i
          LEFT JOIN transactions t ON i.id = t.investment_id
          GROUP BY i.id
        ";
        $res = $conn->query($sql);

        while ($r = $res->fetch_assoc()) {
          $saldo = $r['principal'] + $r['total_masuk'] - $r['total_keluar'];
          $bunga = $saldo * 0.15 / 4; // bunga 15% per tahun dibagi 4 (3 bulan)
          $totalAkhir = $saldo + $bunga;

          echo "
          <tr>
            <td>{$r['investor_name']}</td>
            <td>{$r['start_date']}</td>
            <td>Rp".number_format($r['principal'],0,',','.')."</td>
            <td class='text-success'>Rp".number_format($r['total_masuk'],0,',','.')."</td>
            <td class='text-error'>Rp".number_format($r['total_keluar'],0,',','.')."</td>
            <td class='font-semibold'>Rp".number_format($saldo,0,',','.')."</td>
            <td class='text-primary font-bold'>Rp".number_format($totalAkhir,0,',','.')."</td>
          </tr>
          ";
        }
        ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-6">
      <a href="index.php" class="btn btn-outline">⬅️ Kembali</a>
    </div>
  </div>
</body>
</html>