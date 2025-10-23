<?php
// cashflow.php
include 'config.php';

// helper to format currency
function rp($n) {
    return 'Rp'.number_format($n,0,',','.');
}

// get investor filter (optional)
$filter_id = !empty($_GET['investor_id']) ? (int)$_GET['investor_id'] : 0;

// fetch investors
$inv_sql = "SELECT id, investor_name FROM investments";
if ($filter_id) $inv_sql .= " WHERE id = $filter_id";
$inv_sql .= " ORDER BY investor_name";
$investors = $conn->query($inv_sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Cashflow & Bunga - Lokka</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-base-200 min-h-screen p-6">
  <div class="max-w-6xl mx-auto">
    <div class="card bg-base-100 shadow-xl p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Cashflow & Bunga (Per 3 Bulan)</h1>
        <div>
          <a href="index.php" class="btn btn-ghost btn-sm">← Dashboard</a>
        </div>
      </div>

      <div class="mt-4">
        <form method="get" class="flex gap-2 items-end">
          <div>
            <label class="label"><span class="label-text">Pilih Investor</span></label>
            <select name="investor_id" class="select select-bordered">
              <option value="0">-- Semua Investor --</option>
              <?php
              $resInv = $conn->query("SELECT id, investor_name FROM investments ORDER BY investor_name");
              while ($inv = $resInv->fetch_assoc()) {
                  $sel = ($filter_id == $inv['id']) ? 'selected' : '';
                  echo "<option value='{$inv['id']}' $sel>{$inv['investor_name']}</option>";
              }
              ?>
            </select>
          </div>
          <div>
            <button class="btn btn-primary">Tampilkan</button>
          </div>
        </form>
      </div>

      <?php
      $chart_labels = [];
      $chart_values = [];

      $inv_list_q = "SELECT id, investor_name, start_date, principal, IFNULL(annual_interest,15) AS annual_interest, IFNULL(period_months,3) AS period_months FROM investments";
      if ($filter_id) $inv_list_q .= " WHERE id = $filter_id";
      $inv_list_q .= " ORDER BY investor_name";
      $inv_list = $conn->query($inv_list_q);

      while ($inv = $inv_list->fetch_assoc()) {
          $iid = $inv['id'];
          $name = $inv['investor_name'];
          $start = $inv['start_date'];
          $principal = (float)$inv['principal'];
          $annual_interest = (float)$inv['annual_interest'];
          $period_months = (int)$inv['period_months'];

          $periods = [];
          for ($p = 0; $p < 4; $p++) {
              $periods[$p] = [
                  'start_expr' => "DATE_ADD('$start', INTERVAL ".($p*$period_months)." MONTH)",
                  'end_expr'   => "DATE_SUB(DATE_ADD('$start', INTERVAL ".(($p+1)*$period_months)." MONTH), INTERVAL 1 DAY)",
              ];
          }

          echo "<div class='mt-6'>
                  <h2 class='text-xl font-semibold'>{$name} — Modal: ".rp($principal)."</h2>
                  <div class='overflow-x-auto mt-3'>
                    <table class='table w-full table-compact'>
                      <thead>
                        <tr>
                          <th>Periode</th>
                          <th>Periode Mulai</th>
                          <th>Periode Akhir</th>
                          <th>Modal Awal Periode</th>
                          <th>Total Masuk</th>
                          <th>Total Keluar</th>
                          <th>Modal Aktif</th>
                          <th>Bunga (Rp)</th>
                          <th>Total Akhir</th>
                        </tr>
                      </thead>
                      <tbody>";
          $inv_tot_bunga = 0;
          $inv_tot_penarikan = 0;
          $last_modal = $principal;

          for ($p = 0; $p < 4; $p++) {
              $rowIndex = $p+2;

              $period_start_q = "SELECT ".$periods[$p]['start_expr']." AS ps, ".$periods[$p]['end_expr']." AS pe";
              $res_period = $conn->query($period_start_q);
              $rp = $res_period->fetch_assoc();
              $ps = $rp['ps'];
              $pe = $rp['pe'];

              $q_in = "SELECT IFNULL(SUM(CASE WHEN trans_type = 'masuk' THEN amount ELSE 0 END),0) AS masuk,
                              IFNULL(SUM(CASE WHEN trans_type = 'keluar' THEN -amount ELSE 0 END),0) AS keluar
                       FROM transactions
                       WHERE investment_id = $iid
                         AND trans_date BETWEEN '$ps' AND '$pe'";
              $rtrans = $conn->query($q_in)->fetch_assoc();
              $masuk = (float)$rtrans['masuk'];
              $keluar = (float)$rtrans['keluar'];

              $q_before = "SELECT IFNULL(SUM(CASE WHEN trans_type = 'masuk' THEN amount ELSE 0 END),0) AS masuk_before,
                                  IFNULL(SUM(CASE WHEN trans_type = 'keluar' THEN -amount ELSE 0 END),0) AS keluar_before
                           FROM transactions
                           WHERE investment_id = $iid
                             AND trans_date < '$ps'";
              $rb = $conn->query($q_before)->fetch_assoc();
              $masuk_before = (float)$rb['masuk_before'];
              $keluar_before = (float)$rb['keluar_before'];

              $modal_awal_periode = $principal + $masuk_before - $keluar_before;
              $modal_aktif = $modal_awal_periode + $masuk - $keluar;

              $bunga_periode = round($modal_aktif * ($annual_interest/100) * ($period_months/12), 0);

              $total_akhir = $modal_aktif + $bunga_periode;

              $inv_tot_bunga += $bunga_periode;
              $inv_tot_penarikan += $keluar;
              $last_modal = $modal_aktif;

              echo "<tr>
                      <td>Periode ".($p+1)."</td>
                      <td>".date('d M Y', strtotime($ps))."</td>
                      <td>".date('d M Y', strtotime($pe))."</td>
                      <td>".rp($modal_awal_periode)."</td>
                      <td>".rp($masuk)."</td>
                      <td>".rp($keluar)."</td>
                      <td>".rp($modal_aktif)."</td>
                      <td>".rp($bunga_periode)."</td>
                      <td class='font-bold'>".rp($total_akhir)."</td>
                    </tr>";

              if ($p == 3) {
                  $tot_masuk_all_q = $conn->query("SELECT IFNULL(SUM(CASE WHEN trans_type = 'masuk' THEN amount ELSE 0 END),0) AS m FROM transactions WHERE investment_id=$iid AND trans_date BETWEEN ".$periods[0]['start_expr']." AND ".$periods[3]['end_expr'])->fetch_assoc()['m'];
                  $tot_keluar_all_q = $conn->query("SELECT IFNULL(SUM(CASE WHEN trans_type = 'keluar' THEN -amount ELSE 0 END),0) AS k FROM transactions WHERE investment_id=$iid AND trans_date BETWEEN ".$periods[0]['start_expr']." AND ".$periods[3]['end_expr'])->fetch_assoc()['k'];
                  $agg_total_akhir = $principal + $tot_masuk_all_q - $tot_keluar_all_q + $inv_tot_bunga;
                  $chart_labels[] = $name;
                  $chart_values[] = (float)$agg_total_akhir;
              }
          } 

          echo "    </tbody>
                  </table>
                </div>";

          echo "<div class='mt-3 text-sm'>
                  <span class='font-semibold'>Total Bunga (1 tahun):</span> ".rp($inv_tot_bunga)."
                  &nbsp; | &nbsp;
                  <span class='font-semibold'>Total Penarikan:</span> ".rp($inv_tot_penarikan)."
                </div>";

          echo "</div>";
      }
      ?>

      <div class="mt-8 card bg-base-200 p-4">
        <h3 class="font-semibold mb-3">Grafik: Total Akhir (per Investor)</h3>
        <canvas id="chartTotal" height="120"></canvas>
      </div>

    </div>
  </div>

  <script>
    const labels = <?= json_encode($chart_labels) ?>;
    const dataVals = <?= json_encode($chart_values) ?>;

    const ctx = document.getElementById('chartTotal').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Akhir (Rp)',
          data: dataVals,
          backgroundColor: 'rgba(59,130,246,0.6)', // blue-500 translucent
          borderColor: 'rgba(59,130,246,1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              // format y ticks as Rp
              callback: function(value) {
                return 'Rp' + value.toLocaleString();
              }
            }
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  </script>
</body>
</html>