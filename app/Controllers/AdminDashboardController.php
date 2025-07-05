<?php
namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class AdminDashboardController extends BaseController
{
    protected $transaction;
    protected $transaction_detail;

    public function __construct()
    {
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
    }

    public function index()
    {
        try {
            $currentMonth = date('Y-m');
            $currentYear = date('Y');

            // Total penjualan (jumlah transaksi selesai)
            $totalSales = $this->transaction
                ->where('status', '3')
                ->countAllResults();

            // Total penjualan bulan ini
            $monthlySales = $this->transaction
                ->where('status', '3')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
                ->countAllResults();

            // Total pendapatan (revenue) dari transaksi selesai
            $revenueResult = $this->transaction
                ->selectSum('total_harga')
                ->where('status', '3')
                ->get()
                ->getRow();
            $totalRevenue = $revenueResult ? $revenueResult->total_harga : 0;

            // Total pendapatan bulan ini
            $monthlyRevenueResult = $this->transaction
                ->selectSum('total_harga')
                ->where('status', '3')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
                ->get()
                ->getRow();
            $monthlyRevenue = $monthlyRevenueResult ? $monthlyRevenueResult->total_harga : 0;

            // Total customer unik yang sudah melakukan transaksi selesai
            $totalCustomersQuery = $this->transaction
                ->select('username')
                ->where('status', '3')
                ->findAll();
            $totalCustomers = count(array_unique(array_column($totalCustomersQuery, 'username')));

            // Customer baru bulan ini
            $newCustomersQuery = $this->transaction
                ->select('username')
                ->where('status', '3')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
                ->findAll();
            $newCustomers = count(array_unique(array_column($newCustomersQuery, 'username')));

            // Data transaksi terbaru (10 transaksi terakhir, semua status)
            $recentTransactions = $this->transaction
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();

            // Get product details for recent transactions
            $recentTransactionDetails = [];
            foreach ($recentTransactions as $transaction) {
                try {
                    $details = $this->transaction_detail
                        ->select('product.nama, transaction_detail.jumlah')
                        ->join('product', 'transaction_detail.product_id = product.id', 'left')
                        ->where('transaction_id', $transaction['id'])
                        ->findAll();
                    $recentTransactionDetails[$transaction['id']] = $details;
                } catch (\Exception $e) {
                    $recentTransactionDetails[$transaction['id']] = [];
                }
            }

            // Data penjualan per bulan (6 bulan terakhir)
            $monthlySalesData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $monthName = date('M Y', strtotime("-$i months"));

                $sales = $this->transaction
                    ->where('status', '3')
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->countAllResults();

                $revenueResult = $this->transaction
                    ->selectSum('total_harga')
                    ->where('status', '3')
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->get()
                    ->getRow();
                $revenue = $revenueResult ? $revenueResult->total_harga : 0;

                $customersQuery = $this->transaction
                    ->select('username')
                    ->where('status', '3')
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->findAll();
                $customers = count(array_unique(array_column($customersQuery, 'username')));

                $monthlySalesData[] = [
                    'month' => $monthName,
                    'sales' => $sales,
                    'revenue' => $revenue,
                    'customers' => $customers
                ];
            }

            // Top 5 produk terlaris
            try {
                $topProducts = $this->transaction_detail
                    ->select('product.nama, product.harga, product.foto, SUM(transaction_detail.jumlah) as total_sold')
                    ->join('product', 'transaction_detail.product_id = product.id', 'left')
                    ->join('transaction', 'transaction_detail.transaction_id = transaction.id')
                    ->where('transaction.status', '3')
                    ->where('product.id IS NOT NULL')
                    ->groupBy('product.id, product.nama, product.harga, product.foto')
                    ->orderBy('total_sold', 'DESC')
                    ->limit(5)
                    ->findAll();
            } catch (\Exception $e) {
                $topProducts = [];
            }

            $data = [
                'totalSales' => $totalSales,
                'monthlySales' => $monthlySales,
                'totalRevenue' => $totalRevenue,
                'monthlyRevenue' => $monthlyRevenue,
                'totalCustomers' => $totalCustomers,
                'newCustomers' => $newCustomers,
                'recentTransactions' => $recentTransactions ?: [],
                'recentTransactionDetails' => $recentTransactionDetails,
                'monthlySalesData' => $monthlySalesData,
                'topProducts' => $topProducts ?: [],
                'currentMonth' => date('F Y'),
                'currentYear' => $currentYear
            ];

            return view('admin/dashboard', $data);
        } catch (\Exception $e) {
            return view('admin/dashboard', [
                'totalSales' => 0,
                'monthlySales' => 0,
                'totalRevenue' => 0,
                'monthlyRevenue' => 0,
                'totalCustomers' => 0,
                'newCustomers' => 0,
                'recentTransactions' => [],
                'recentTransactionDetails' => [],
                'monthlySalesData' => [],
                'topProducts' => [],
                'currentMonth' => date('F Y'),
                'currentYear' => date('Y')
            ]);
        }
    }

    // Endpoint AJAX untuk data dashboard dinamis (chart & tabel)
    public function data()
    {
        $filter = $this->request->getGet('filter') ?? 'month';
        $now = date('Y-m-d');
        $currentMonth = date('Y-m');
        $currentYear = date('Y');

        // Filter range
        if ($filter === 'today') {
            $dateWhere = ['DATE(created_at)' => $now];
            $groupFormat = '%H:00'; // per jam
            $labelFormat = 'H:00';
            $range = range(0, 23);
            $labels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', $range);
        } elseif ($filter === 'year') {
            $dateWhere = ["DATE_FORMAT(created_at, '%Y')" => $currentYear];
            $groupFormat = '%m'; // per bulan
            $labelFormat = 'M';
            $range = range(1, 12);
            $labels = array_map(fn($m) => date('M', mktime(0,0,0,$m,1)), $range);
        } else { // default: month
            $dateWhere = ["DATE_FORMAT(created_at, '%Y-%m')" => $currentMonth];
            $groupFormat = '%d'; // per hari
            $labelFormat = 'j M';
            $days = date('t');
            $range = range(1, $days);
            $labels = array_map(fn($d) => $d . ' ' . date('M'), $range);
        }

        // Grafik: sales, revenue, customers per label
        $salesData = [];
        $revenueData = [];
        $customersData = [];
        foreach ($range as $i => $val) {
            if ($filter === 'today') {
                $start = date('Y-m-d') . ' ' . str_pad($val, 2, '0', STR_PAD_LEFT) . ':00:00';
                $end = date('Y-m-d') . ' ' . str_pad($val, 2, '0', STR_PAD_LEFT) . ':59:59';
                $where = [
                    'status' => '3',
                    'created_at >=' => $start,
                    'created_at <=' => $end
                ];
            } elseif ($filter === 'year') {
                $month = str_pad($val, 2, '0', STR_PAD_LEFT);
                $where = [
                    'status' => '3',
                    "DATE_FORMAT(created_at, '%Y-%m')" => $currentYear . '-' . $month
                ];
            } else { // month
                $day = str_pad($val, 2, '0', STR_PAD_LEFT);
                $where = [
                    'status' => '3',
                    "DATE_FORMAT(created_at, '%Y-%m-%d')" => $currentMonth . '-' . $day
                ];
            }
            // Sales
            $sales = $this->transaction->where($where)->countAllResults();
            // Revenue
            $revenueResult = $this->transaction->selectSum('total_harga')->where($where)->get()->getRow();
            $revenue = $revenueResult ? $revenueResult->total_harga : 0;
            // Customers
            $customersQuery = $this->transaction->select('username')->where($where)->findAll();
            $customers = count(array_unique(array_column($customersQuery, 'username')));
            $salesData[] = $sales;
            $revenueData[] = $revenue;
            $customersData[] = $customers;
            $this->transaction->resetQuery();
        }

        // Recent Sales (10 terakhir sesuai filter)
        $recentTransactions = $this->transaction
            ->where('status', '3')
            ->where($dateWhere)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();
        $recentTransactionDetails = [];
        foreach ($recentTransactions as $transaction) {
            try {
                $details = $this->transaction_detail
                    ->select('product.nama, transaction_detail.jumlah')
                    ->join('product', 'transaction_detail.product_id = product.id', 'left')
                    ->where('transaction_id', $transaction['id'])
                    ->findAll();
                $recentTransactionDetails[$transaction['id']] = $details;
            } catch (\Exception $e) {
                $recentTransactionDetails[$transaction['id']] = [];
            }
        }

        // Top Selling (top 5 sesuai filter)
        try {
            $topProducts = $this->transaction_detail
                ->select('product.nama, product.harga, product.foto, SUM(transaction_detail.jumlah) as total_sold')
                ->join('product', 'transaction_detail.product_id = product.id', 'left')
                ->join('transaction', 'transaction_detail.transaction_id = transaction.id')
                ->where('transaction.status', '3')
                ->where($dateWhere)
                ->where('product.id IS NOT NULL')
                ->groupBy('product.id, product.nama, product.harga, product.foto')
                ->orderBy('total_sold', 'DESC')
                ->limit(5)
                ->findAll();
        } catch (\Exception $e) {
            $topProducts = [];
        }

        return $this->response->setJSON([
            'labels' => $labels,
            'sales' => $salesData,
            'revenue' => $revenueData,
            'customers' => $customersData,
            'recentTransactions' => $recentTransactions,
            'recentTransactionDetails' => $recentTransactionDetails,
            'topProducts' => $topProducts
        ]);
    }
} 