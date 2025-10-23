<?php
// report.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Report Investor - Lokka</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" />
</head>
<body class="bg-base-200 min-h-screen p-6">
  <div class="max-w-6xl mx-auto">
    <div class="card bg-base-100 shadow-xl p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Laporan Investor</h1>
        <div>
          <a href="index.php" class="btn btn-ghost btn-sm">← Dashboard</a>
          <a href="investor_form.php" class="btn btn-primary btn-sm ml-2">Tambah Investor</a>
          <a href="transaction_add.php" class="btn btn-secondary btn-sm ml-2">Tambah Transaksi</a>
        </div>
      </div>

      <div class="mt-6">
        <form method="get" class="flex gap-2 items-end">
          <div>
            <label class="label"><span class="label-text">Filter Investor</span></label>
            <select name="investor_id" class="select select-bordered w-80">
              <option value="">-- Semua Investor --</option>
              <?php
              $resInv = $conn->query("SELECT id, investor_name FROM investments ORDER BY investor_name");
              while ($inv = $resInv->fetch_assoc()) {
                  $sel = (isset($_GET['investor_id']) && $_GET['investor_id'] == $inv['id']) ? 'selected' : '';
                  echo "<option value='{$inv['id']}' $sel>{$inv['investor_name']}</option>";
              }
              ?>
            </select>
          </div>
          <div>
            <button class="btn btn-primary">Tampilkan</button>
            <a href="report.php" class="btn btn-ghost ml-2">Reset</a>
          </div>
        </form>
      </div>

      <div class="mt-6 overflow-x-auto">
        <table class="table w-full table-zebra">
          <thead>
            <tr>
              <th>Investor</th>
              <th>Tanggal Mulai</th>
              <th>Modal Awal</th>
              <th>Total Masuk</th>
              <th>Total Keluar</th>
              <th>Saldo (Modal + Net Trans)</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $where = "";
                if (!empty($_GET['investor_id'])) {
                    $id = (int)$_GET['investor_id'];
                    $where = "WHERE i.id = $id";
                }

                $sql = "
                    SELECT 
                        i.id,
                        i.investor_name,
                        i.start_date,
                        i.principal,
                        IFNULL(SUM(CASE WHEN t.trans_type = 'masuk' THEN t.amount ELSE 0 END), 0) AS total_masuk,
                        IFNULL(SUM(CASE WHEN t.trans_type = 'keluar' THEN t.amount ELSE 0 END), 0) AS total_keluar
                    FROM investments i
                    LEFT JOIN transactions t ON t.investment_id = i.id
                    $where
                    GROUP BY i.id
                    ORDER BY i.created_at DESC ";
            $res = $conn->query($sql);
            while ($r = $res->fetch_assoc()) {
                $net = $r['principal'] + $r['total_masuk'] - $r['total_keluar'];
                echo "<tr>
                        <td><a class='link' href='report.php?investor_id={$r['id']}'>{$r['investor_name']}</a></td>
                        <td>".date('d M Y', strtotime($r['start_date']))."</td>
                        <td>Rp".number_format($r['principal'],0,',','.')."</td>
                        <td>Rp".number_format($r['total_masuk'],0,',','.')."</td>
                        <td>Rp".number_format($r['total_keluar'],0,',','.')."</td>
                        <td class='font-bold'>Rp".number_format($net,0,',','.')."</td>
                      </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($_GET['investor_id'])): 
          $iid = (int)$_GET['investor_id'];
          // show transaction details
          $trs = $conn->query("SELECT trans_date, description, amount, trans_type FROM transactions WHERE investment_id = $iid ORDER BY trans_date");

      ?>
        <div class="mt-8">
          <h2 class="text-xl font-semibold">Detail Transaksi</h2>
          <div class="overflow-x-auto mt-4">
            <table class="table w-full">
              <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Tipe</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($t = $trs->fetch_assoc()): 
                    $masuk = $t['trans_type'] == 'masuk' ? $t['amount'] : 0;
                    $keluar = $t['trans_type'] == 'keluar' ? $t['amount'] : 0;
                ?>
                <tr>
                    <td><?= date('d M Y', strtotime($t['trans_date'])) ?></td>
                    <td><?= htmlspecialchars($t['description']) ?></td>
                    <td><?= htmlspecialchars(strtoupper($t['trans_type'])) ?></td>
                    <td><?= $masuk ? 'Rp'.number_format($masuk,0,',','.') : '-' ?></td>
                    <td><?= $keluar ? 'Rp'.number_format($keluar,0,',','.') : '-' ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>