<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummyDashboardSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        // Tambah produk dummy
        $products = [
            ['nama' => 'Produk A', 'harga' => 10000, 'foto' => null],
            ['nama' => 'Produk B', 'harga' => 20000, 'foto' => null],
            ['nama' => 'Produk C', 'harga' => 15000, 'foto' => null],
        ];
        foreach ($products as $p) {
            $db->table('product')->insert($p);
        }
        $productIds = $db->table('product')->select('id')->get()->getResultArray();
        $productIds = array_column($productIds, 'id');

        // Tambah transaksi selesai (status 3)
        for ($i = 1; $i <= 5; $i++) {
            $transaksi = [
                'username' => 'user' . $i,
                'total_harga' => rand(20000, 100000),
                'status' => 3,
                'created_at' => date('Y-m-d H:i:s', strtotime("-$i days")),
            ];
            $db->table('transaction')->insert($transaksi);
            $transId = $db->insertID();
            // Tambah detail transaksi (acak produk)
            for ($j = 0; $j < rand(1, 3); $j++) {
                $db->table('transaction_detail')->insert([
                    'transaction_id' => $transId,
                    'product_id' => $productIds[array_rand($productIds)],
                    'jumlah' => rand(1, 5)
                ]);
            }
        }
    }
} 