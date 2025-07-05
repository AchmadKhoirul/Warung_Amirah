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
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

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
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

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
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

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

                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Reports <span>/6 Months</span></h5>

                        <!-- Line Chart -->
                        <div id="reportsChart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                new ApexCharts(document.querySelector("#reportsChart"), {
                                    series: [{
                                        name: 'Sales',
                                        data: [<?= implode(',', array_column($monthlySalesData, 'sales')) ?>]
                                    }, {
                                        name: 'Revenue',
                                        data: [<?= implode(',', array_column($monthlySalesData, 'revenue')) ?>]
                                    }],
                                    chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                            show: false
                                        },
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                    fill: {
                                        type: "gradient",
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.4,
                                            stops: [0, 90, 100]
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        width: 2
                                    },
                                    xaxis: {
                                        categories: [<?= '"' . implode('","', array_column($monthlySalesData, 'month')) . '"' ?>]
                                    },
                                    tooltip: {
                                        x: {
                                            format: 'dd/MM/yy HH:mm'
                                        },
                                    }
                                }).render();
                            });
                        </script>
                        <!-- End Line Chart -->

                    </div>

                </div>
            </div><!-- End Reports -->

            <!-- Recent Sales -->
            <div class="col-12">
                <div class="card recent-sales overflow-auto">

                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Recent Sales <span>| Today</span></h5>

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
                                <?php foreach ($recentTransactions as $index => $transaction): ?>
                                <tr>
                                    <th scope="row"><a href="#">#<?= $transaction['id'] ?></a></th>
                                    <td><?= $transaction['username'] ?></td>
                                    <td>
                                        <?php if (isset($recentTransactionDetails[$transaction['id']])): ?>
                                            <?php foreach ($recentTransactionDetails[$transaction['id']] as $detail): ?>
                                                <a href="#" class="text-primary"><?= $detail['nama'] ?> (<?= $detail['jumlah'] ?>x)</a><br>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <a href="#" class="text-primary">No products</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>Rp <?= number_format($transaction['total_harga']) ?></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td><?= date('d M Y', strtotime($transaction['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div><!-- End Recent Sales -->

            <!-- Top Selling -->
            <div class="col-12">
                <div class="card top-selling overflow-auto">

                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Top Selling <span>| Today</span></h5>

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
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data produk terlaris</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <th scope="row">
                                        <a href="#">
                                            <?php if (!empty($product['foto']) && file_exists(FCPATH . 'img/' . $product['foto'])): ?>
                                                <img src="<?= base_url('img/' . $product['foto']) ?>" alt="<?= $product['nama'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" onerror="this.src='<?= base_url('NiceAdmin/assets/img/product-1.jpg') ?>'">
                                            <?php else: ?>
                                                <img src="<?= base_url('NiceAdmin/assets/img/product-1.jpg') ?>" alt="No Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <td><a href="#" class="text-primary fw-bold"><?= $product['nama'] ?></a></td>
                                    <td>Rp <?= number_format($product['harga']) ?></td>
                                    <td class="fw-bold"><?= $product['total_sold'] ?></td>
                                    <td>Rp <?= number_format($product['harga'] * $product['total_sold']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div><!-- End Top Selling -->

        </div>
    </section>

</main><!-- End #main -->

<?= $this->endSection() ?> 