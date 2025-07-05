<?= $this->extend('admin/layout_dashboard') ?>

<?= $this->section('content') ?>

<?php if (isset($debug) && !$debug['has_data']): ?>
<div class="alert alert-info">
    <h5>Debug Info:</h5>
    <p>Total transactions: <?= $debug['total_transactions'] ?? 0 ?></p>
    <p>Completed transactions: <?= $debug['completed_transactions'] ?? 0 ?></p>
    <?php if (isset($debug['error'])): ?>
        <p class="text-danger">Error: <?= $debug['error'] ?></p>
    <?php endif; ?>
    <p><strong>Dashboard kosong karena tidak ada transaksi dengan status '2' (Selesai)</strong></p>
</div>
<?php endif; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Sales <span>| <?= $currentMonth ?></span></h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= number_format($monthlySales) ?></h6>
                                <span class="text-success small pt-1 fw-bold"><?= $monthlySales > 0 ? round(($monthlySales / max($totalSales, 1)) * 100, 1) : 0 ?>%</span> <span class="text-muted small pt-2 ps-1">increase</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Revenue <span>| <?= $currentMonth ?></span></h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="ps-3">
                                <h6>Rp <?= number_format($monthlyRevenue) ?></h6>
                                <span class="text-success small pt-1 fw-bold"><?= $monthlyRevenue > 0 ? round(($monthlyRevenue / max($totalRevenue, 1)) * 100, 1) : 0 ?>%</span> <span class="text-muted small pt-2 ps-1">increase</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- End Revenue Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Customers <span>| <?= $currentMonth ?></span></h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= number_format($newCustomers) ?></h6>
                                <span class="text-danger small pt-1 fw-bold"><?= $newCustomers > 0 ? round(($newCustomers / max($totalCustomers, 1)) * 100, 1) : 0 ?>%</span> <span class="text-muted small pt-2 ps-1">new customers</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div><!-- End Customers Card -->

            <!-- Reports -->
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <h5 class="card-title">Reports <span>/6 Months - Sales, Revenue & Customers</span></h5>

                        <!-- Line Chart -->
                        <div id="reportsChart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                // Data asli dari PHP
                                const sales = [<?= implode(',', array_column($monthlySalesData, 'sales')) ?>];
                                const revenue = [<?= implode(',', array_column($monthlySalesData, 'revenue')) ?>];
                                const customers = [<?= implode(',', array_column($monthlySalesData, 'customers')) ?>];

                                new ApexCharts(document.querySelector("#reportsChart"), {
                                    series: [
                                        { name: 'Sales', type: 'line', data: sales },
                                        { name: 'Revenue', type: 'line', data: revenue },
                                        { name: 'Customers', type: 'line', data: customers }
                                    ],
                                    chart: {
                                        height: 350,
                                        type: 'line',
                                        toolbar: { show: false }
                                    },
                                    dataLabels: { enabled: false },
                                    stroke: { width: 3, curve: 'smooth' },
                                    colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                    xaxis: {
                                        categories: [<?= '"' . implode('","', array_column($monthlySalesData, 'month')) . '"' ?>]
                                    },
                                    yaxis: [
                                        { title: { text: 'Sales' }, labels: { style: { colors: '#4154f1' } } },
                                        { opposite: true, title: { text: 'Revenue (Rp)' }, labels: { style: { colors: '#2eca6a' } } },
                                        { opposite: true, title: { text: 'Customers' }, labels: { style: { colors: '#ff771d' } } }
                                    ],
                                    tooltip: {
                                        shared: true,
                                        intersect: false,
                                        custom: function({series, seriesIndex, dataPointIndex, w}) {
                                            return `
                                                <div style='padding:8px'>
                                                    <b>${w.globals.labels[dataPointIndex]}</b><br>
                                                    Sales: ${sales[dataPointIndex]}<br>
                                                    Revenue: Rp ${revenue[dataPointIndex].toLocaleString('id-ID')}<br>
                                                    Customers: ${customers[dataPointIndex]}
                                                </div>
                                            `;
                                        }
                                    },
                                    legend: {
                                        position: 'top',
                                        horizontalAlign: 'left'
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <small class="text-muted">Sales</small>
                                    <div class="fw-bold text-primary">Total Transaksi</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <small class="text-muted">Revenue</small>
                                    <div class="fw-bold text-success">Total Pendapatan</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <small class="text-muted">Customers</small>
                                    <div class="fw-bold text-danger">Customer Baru</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div><!-- End Reports -->

            <!-- Recent Sales (NiceAdmin style) -->
            <div class="col-12">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Recent Sales</h5>
                        <table class="table table-borderless datatable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentTransactions)): ?>
                                    <tr><td colspan="6" class="text-center">No entries found</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentTransactions as $index => $transaction): ?>
                                    <tr>
                                        <th scope="row"><a href="#">#<?= $transaction['id'] ?></a></th>
                                        <td><?= esc($transaction['username']) ?></td>
                                        <td>
                                            <?php if (isset($recentTransactionDetails[$transaction['id']]) && $recentTransactionDetails[$transaction['id']]): ?>
                                                <?php foreach ($recentTransactionDetails[$transaction['id']] as $detail): ?>
                                                    <a href="#" class="text-primary"><?= esc($detail['nama']) ?> (<?= esc($detail['jumlah']) ?>x)</a><br>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No products</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Rp <?= number_format($transaction['total_harga']) ?></td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td><?= date('d M Y', strtotime($transaction['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Recent Sales -->

            <!-- Top Selling (NiceAdmin style) -->
            <div class="col-12">
                <div class="card top-selling overflow-auto">
                    <div class="card-body pb-0">
                        <h5 class="card-title">Top Selling</h5>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Preview</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Sold</th>
                                    <th scope="col">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topProducts)): ?>
                                    <tr><td colspan="5" class="text-center">No entries found</td></tr>
                                <?php else: ?>
                                    <?php foreach ($topProducts as $product): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($product['foto'])): ?>
                                                <img src="<?= base_url('img/' . $product['foto']) ?>" alt="<?= esc($product['nama']) ?>" width="40" height="40" style="object-fit:cover; border-radius:5px;">
                                            <?php else: ?>
                                                <img src="<?= base_url('NiceAdmin/assets/img/product-placeholder.png') ?>" alt="No Image" width="40" height="40" style="object-fit:cover; border-radius:5px;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($product['nama']) ?></td>
                                        <td>Rp <?= number_format($product['harga']) ?></td>
                                        <td><?= esc($product['total_sold']) ?></td>
                                        <td>Rp <?= number_format($product['harga'] * $product['total_sold']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Top Selling -->

        </div>
    </section>

</main><!-- End #main -->

<?= $this->endSection() ?> 