<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;


class TransaksiController extends BaseController
{
    protected $cart;
    protected $client;
    protected $apiKey;
    protected $transaction;
    protected $transaction_detail;

    function __construct()
    {
        helper('number');
        helper('form');
        $this->cart = \Config\Services::cart();
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
    }

    public function index()
    {
        $data['items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert(array(
            'id'        => $this->request->getPost('id'),
            'qty'       => 1,
            'price'     => $this->request->getPost('harga'),
            'name'      => $this->request->getPost('nama'),
            'options'   => array('foto' => $this->request->getPost('foto'))
        ));
        session()->setflashdata('success', 'Produk berhasil ditambahkan ke keranjang. (<a href="' . base_url() . 'keranjang">Lihat</a>)');
        return redirect()->to(base_url('/'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $value) {
            $this->cart->update(array(
                'rowid' => $value['rowid'],
                'qty'   => $this->request->getPost('qty' . $i++)
            ));
        }

        session()->setflashdata('success', 'Keranjang Berhasil Diedit');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
{
    $data['items'] = $this->cart->contents();
    $data['total'] = $this->cart->total();

    return view('v_checkout', $data);
}

public function getLocation()
{
		//keyword pencarian yang dikirimkan dari halaman checkout
    $search = $this->request->getGet('search');

    $response = $this->client->request(
        'GET', 
        'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search='.$search.'&limit=50', [
            'headers' => [
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ],
        ]
    );

    $body = json_decode($response->getBody(), true); 
    return $this->response->setJSON($body['data']);
}

public function getCost()
{ 
		//ID lokasi yang dikirimkan dari halaman checkout
    $destination = $this->request->getGet('destination');

		//parameter daerah asal pengiriman, berat produk, dan kurir dibuat statis
    //valuenya => 64999 : PEDURUNGAN TENGAH , 1000 gram, dan JNE
    $response = $this->client->request(
        'POST', 
        'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'multipart' => [
                [
                    'name' => 'origin',
                    'contents' => '64999'
                ],
                [
                    'name' => 'destination',
                    'contents' => $destination
                ],
                [
                    'name' => 'weight',
                    'contents' => '1000'
                ],
                [
                    'name' => 'courier',
                    'contents' => 'jne'
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ],
        ]
    );

    $body = json_decode($response->getBody(), true); 
    return $this->response->setJSON($body['data']);
}
public function buy()
{
    if ($this->request->getPost()) {
        // Validasi wajib isi
        $rules = [
            'nama' => 'required',
            'alamat' => 'required',
            'kelurahan' => 'required',
            'layanan' => 'required',
            'ongkir' => 'required',
            'metode_pembayaran' => 'required',
        ];
        $metode = $this->request->getPost('metode_pembayaran');
        if ($metode !== 'cod') {
            $rules['bukti_pembayaran'] = 'uploaded[bukti_pembayaran]|is_image[bukti_pembayaran]|mime_in[bukti_pembayaran,image/jpg,image/jpeg]';
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $status_pembayaran = 'pending';
        if ($metode === 'cod') {
            $status_pembayaran = 'cod';
        }
        $buktiName = null;
        $bukti = $this->request->getFile('bukti_pembayaran');
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            $buktiName = $bukti->getRandomName();
            $bukti->move('writable/uploads', $buktiName);
            if ($metode !== 'cod') {
                $status_pembayaran = 'bukti_upload';
            }
        }
        $dataForm = [
            'username' => $this->request->getPost('username'),
            'total_harga' => $this->request->getPost('total_harga'),
            'alamat' => $this->request->getPost('alamat'),
            'ongkir' => $this->request->getPost('ongkir'),
            'status' => 0,
            'metode_pembayaran' => $metode,
            'status_pembayaran' => $status_pembayaran,
            'bukti_pembayaran' => $buktiName,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ];
        $this->transaction->insert($dataForm);
        $last_insert_id = $this->transaction->getInsertID();
        foreach ($this->cart->contents() as $value) {
            $dataFormDetail = [
                'transaction_id' => $last_insert_id,
                'product_id' => $value['id'],
                'jumlah' => $value['qty'],
                'diskon' => 0,
                'subtotal_harga' => $value['qty'] * $value['price'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];
            $this->transaction_detail->insert($dataFormDetail);
        }
        $this->cart->destroy();
        // Redirect langsung ke profil customer setelah buat pesanan
        return redirect()->to(base_url('profile'));
    }
}

    public function update_status($id)
{
    $status = $this->request->getPost('status');
    $page = $this->request->getGet('datatable_page');
    if ($status !== null) {
        $this->transaction->update($id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        session()->setFlashdata('success', 'Status transaksi berhasil diupdate.');
    }
    if ($page !== null) {
        return redirect()->to(base_url('admin/transaksi') . '?datatable_page=' . $page);
    }
    return redirect()->to(base_url('admin/transaksi'));
}

public function update_status_pembayaran($id)
{
    $status = $this->request->getPost('status_pembayaran');
    $page = $this->request->getGet('datatable_page');
    if ($status) {
        $this->transaction->update($id, [
            'status_pembayaran' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        session()->setFlashdata('success', 'Status pembayaran berhasil diupdate.');
    }
    if ($page !== null) {
        return redirect()->to(base_url('admin/transaksi') . '?datatable_page=' . $page);
    }
    return redirect()->to(base_url('admin/transaksi'));
}

public function upload_bukti($id)
{
    $bukti = $this->request->getFile('bukti_pembayaran');
    if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
        $buktiName = $bukti->getRandomName();
        $bukti->move('writable/uploads', $buktiName);
        $this->transaction->update($id, [
            'bukti_pembayaran' => $buktiName,
            'status_pembayaran' => 'bukti_upload',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        session()->setFlashdata('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.');
    } else {
        session()->setFlashdata('failed', 'Gagal upload bukti pembayaran.');
    }
    return redirect()->back();
}

public function cancel($id)
{
    $transaksi = $this->transaction->find($id);
    if (!$transaksi) {
        session()->setFlashdata('failed', 'Transaksi tidak ditemukan.');
        return redirect()->back();
    }
    // Cek waktu (ubah ke 1 menit)
    $created = strtotime($transaksi['created_at']);
    $now = time();
    $diff = round(($now - $created) / 60, 2); // 2 desimal, lebih presisi
    // Izinkan cancel jika status_pembayaran 'pending', 'bukti_upload', atau 'cod' dan status masih '0' (Diproses)
    if ($diff > 1 || !in_array($transaksi['status_pembayaran'], ['pending', 'bukti_upload', 'cod']) || $transaksi['status'] != '0') {
        session()->setFlashdata('failed', 'Batas waktu cancel sudah habis atau transaksi tidak bisa dibatalkan.');
        return redirect()->back();
    }
    $this->transaction->update($id, [
        'status_pembayaran' => 'cancel',
        'status' => 0,
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    session()->setFlashdata('success', 'Transaksi berhasil dibatalkan.');
    return redirect()->back();
}

}
