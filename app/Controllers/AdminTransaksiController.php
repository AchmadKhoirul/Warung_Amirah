<?php
namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class AdminTransaksiController extends BaseController
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
        $tanggal_dari = $this->request->getGet('tanggal_dari');
        $tanggal_sampai = $this->request->getGet('tanggal_sampai');
        $builder = $this->transaction;
        if ($tanggal_dari && $tanggal_sampai) {
            $builder = $builder->where('DATE(created_at) >=', $tanggal_dari)->where('DATE(created_at) <=', $tanggal_sampai);
        } elseif ($tanggal_dari) {
            $builder = $builder->where('DATE(created_at) >=', $tanggal_dari);
        } elseif ($tanggal_sampai) {
            $builder = $builder->where('DATE(created_at) <=', $tanggal_sampai);
        }
        $buy = $builder->findAll();
        $product = [];
        if (!empty($buy)) {
            foreach ($buy as $item) {
                $detail = $this->transaction_detail
                    ->select('transaction_detail.*, product.nama, product.harga, product.foto')
                    ->join('product', 'transaction_detail.product_id=product.id')
                    ->where('transaction_id', $item['id'])
                    ->findAll();
                if (!empty($detail)) {
                    $product[$item['id']] = $detail;
                }
            }
        }
        $data['buy'] = $buy;
        $data['product'] = $product;
        return view('admin/v_transaksi', $data);
    }

    public function printTransaksi()
    {
        $status = $this->request->getGet('status');
        $tanggal_dari = $this->request->getGet('tanggal_dari');
        $tanggal_sampai = $this->request->getGet('tanggal_sampai');
        $builder = $this->transaction;
        if ($status !== null && $status !== '') {
            $builder = $builder->where('status', $status);
        }
        if ($tanggal_dari && $tanggal_sampai) {
            $builder = $builder->where('DATE(created_at) >=', $tanggal_dari)->where('DATE(created_at) <=', $tanggal_sampai);
        } elseif ($tanggal_dari) {
            $builder = $builder->where('DATE(created_at) >=', $tanggal_dari);
        } elseif ($tanggal_sampai) {
            $builder = $builder->where('DATE(created_at) <=', $tanggal_sampai);
        }
        $buy = $builder->findAll();
        $product = [];
        if (!empty($buy)) {
            foreach ($buy as $item) {
                $detail = $this->transaction_detail
                    ->select('transaction_detail.*, product.nama, product.harga, product.foto')
                    ->join('product', 'transaction_detail.product_id=product.id')
                    ->where('transaction_id', $item['id'])
                    ->findAll();
                if (!empty($detail)) {
                    $product[$item['id']] = $detail;
                }
            }
        }
        $data['buy'] = $buy;
        $data['product'] = $product;
        $data['status'] = $status;
        $data['tanggal_dari'] = $tanggal_dari;
        $data['tanggal_sampai'] = $tanggal_sampai;
        return view('admin/v_transaksi_print', $data);
    }
}
