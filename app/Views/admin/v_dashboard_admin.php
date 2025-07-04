<?php
// Data grafik multi dataset
$labels = $labels ?? [];
$sales = $sales ?? [];
$revenue = $revenue ?? [];
$customers = $customers ?? [];
?>
<canvas id="chartTransaksi" height="100"></canvas>
<script src="/public/NiceAdmin/assets/vendor/chart.js/chart.umd.js"></script>
<script>
const ctx = document.getElementById('chartTransaksi').getContext('2d');
const labels = <?= json_encode($labels) ?>;
const sales = <?= json_encode($sales) ?>;
const revenue = <?= json_encode($revenue) ?>;
const customers = <?= json_encode($customers) ?>;
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [
      {
        label: 'Total Transaksi (Sales)',
        data: sales,
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1,
        yAxisID: 'y',
      },
      {
        label: 'Total Pendapatan (Revenue)',
        data: revenue,
        backgroundColor: 'rgba(255, 206, 86, 0.7)',
        borderColor: 'rgba(255, 206, 86, 1)',
        borderWidth: 1,
        type: 'line',
        yAxisID: 'y1',
      },
      {
        label: 'Jumlah Customer',
        data: customers,
        backgroundColor: 'rgba(75, 192, 192, 0.7)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        type: 'line',
        yAxisID: 'y2',
      }
    ]
  },
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Statistik Penjualan, Pendapatan, dan Customer per Bulan',
        font: { size: 18 }
      }
    },
    responsive: true,
    interaction: {
      mode: 'index',
      intersect: false,
    },
    scales: {
      y: {
        beginAtZero: true,
        position: 'left',
        title: {
          display: true,
          text: 'Total Transaksi'
        }
      },
      y1: {
        beginAtZero: true,
        position: 'right',
        grid: { drawOnChartArea: false },
        title: {
          display: true,
          text: 'Total Pendapatan'
        }
      },
      y2: {
        beginAtZero: true,
        position: 'right',
        grid: { drawOnChartArea: false },
        title: {
          display: true,
          text: 'Jumlah Customer'
        },
        offset: true
      }
    }
  }
});
</script>