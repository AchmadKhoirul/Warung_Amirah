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

            // Debug: Cek apakah ada transaksi selesai
            $allTransactions = $this->transaction->findAll();
            $completedTransactions = $this->transaction->where('status', '2')->findAll();
            
            // Log untuk debugging
            log_message('info', 'Total transactions: ' . count($allTransactions));
            log_message('info', 'Completed transactions: ' . count($completedTransactions));

            // Total penjualan (jumlah transaksi selesai)
            $totalSales = $this->transaction
                ->where('status', '2')
                ->countAllResults();

            // Total penjualan bulan ini
            $monthlySales = $this->transaction
                ->where('status', '2')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
                ->countAllResults();

            // Total pendapatan (revenue) dari transaksi selesai
            $revenueResult = $this->transaction
                ->selectSum('total_harga')
                ->where('status', '2')
                ->get()
                ->getRow();
            $totalRevenue = $revenueResult ? $revenueResult->total_harga : 0;

            // Total pendapatan bulan ini
            $monthlyRevenueResult = $this->transaction
                ->selectSum('total_harga')
                ->where('status', '2')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
                ->get()
                ->getRow();
            $monthlyRevenue = $monthlyRevenueResult ? $monthlyRevenueResult->total_harga : 0;

            // Total customer unik yang sudah melakukan transaksi selesai
            $totalCustomersQuery = $this->transaction
                ->select('username')
                ->where('status', '2')
                ->findAll();
            $totalCustomers = count(array_unique(array_column($totalCustomersQuery, 'username')));

            // Customer baru bulan ini
            $newCustomersQuery = $this->transaction
                ->select('username')
                ->where('status', '2')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
                ->findAll();
            $newCustomers = count(array_unique(array_column($newCustomersQuery, 'username')));

            // Data transaksi terbaru (10 transaksi terakhir)
            $recentTransactions = $this->transaction
                ->where('status', '2')
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();

            // Get product details for recent transactions (tahan error jika produk sudah dihapus)
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
                    // Jika ada error, set empty array
                    $recentTransactionDetails[$transaction['id']] = [];
                    log_message('error', 'Error getting transaction details: ' . $e->getMessage());
                }
            }

            // Data penjualan per bulan (6 bulan terakhir)
            $monthlySalesData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $monthName = date('M Y', strtotime("-$i months"));

                $sales = $this->transaction
                    ->where('status', '2')
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->countAllResults();

                $revenueResult = $this->transaction
                    ->selectSum('total_harga')
                    ->where('status', '2')
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->get()
                    ->getRow();
                $revenue = $revenueResult ? $revenueResult->total_harga : 0;

                $monthlySalesData[] = [
                    'month' => $monthName,
                    'sales' => $sales,
                    'revenue' => $revenue
                ];
            }

            // Top 5 produk terlaris (tahan error jika produk sudah dihapus)
            try {
                $topProducts = $this->transaction_detail
                    ->select('product.nama, product.harga, product.foto, SUM(transaction_detail.jumlah) as total_sold')
                    ->join('product', 'transaction_detail.product_id = product.id', 'left')
                    ->join('transaction', 'transaction_detail.transaction_id = transaction.id')
                    ->where('transaction.status', '2')
                    ->where('product.id IS NOT NULL')
                    ->groupBy('product.id, product.nama, product.harga, product.foto')
                    ->orderBy('total_sold', 'DESC')
                    ->limit(5)
                    ->findAll();
            } catch (\Exception $e) {
                // Jika ada error, set empty array
                $topProducts = [];
                log_message('error', 'Error getting top products: ' . $e->getMessage());
            }

            // Fallback data jika semua kosong
            if (empty($recentTransactions) && empty($topProducts)) {
                // Cek apakah ada transaksi dengan status lain
                $anyTransactions = $this->transaction->findAll();
                if (!empty($anyTransactions)) {
                    log_message('info', 'Found transactions but none are completed. Statuses: ' . implode(', ', array_unique(array_column($anyTransactions, 'status'))));
                }
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
                'currentYear' => $currentYear,
                'debug' => [
                    'total_transactions' => count($allTransactions),
                    'completed_transactions' => count($completedTransactions),
                    'has_data' => !empty($recentTransactions) || !empty($topProducts)
                ]
            ];

            return view('admin/dashboard', $data);

        } catch (\Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            
            // Return empty data jika ada error
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
                'currentYear' => date('Y'),
                'debug' => [
                    'error' => $e->getMessage(),
                    'has_data' => false
                ]
            ]);
        }
    }
} 