<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'total_harga', 'alamat', 'ongkir', 'status', 'bukti_pembayaran', 'metode_pembayaran', 'status_pembayaran', 'created_at', 'updated_at'
    ];
}