<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="pagetitle">
  <h1>Detail Transaksi</h1>
</div>
<section class="section">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Info Transaksi</h5>
      <ul>
        <li><b>ID:</b> <?= $transaction['id'] ?></li>
        <li><b>Username:</b> <?= $transaction['username'] ?></li>
        <li><b>Alamat:</b> <?= $transaction['alamat'] ?></li>
        <li><b>Ongkir:</b> <?= $transaction['ongkir'] ?></li>
        <li><b>Total Harga:</b> Rp <?= number_format($transaction['total_harga'],0,',','.') ?></li>
        <li><b>Status:</b> <?php
                if ($transaction['status'] == 0) echo 'Diproses';
                elseif ($transaction['status'] == 1) echo 'Dikemas';
                elseif ($transaction['status'] == 2) echo 'Dikirim';
                elseif ($transaction['status'] == 3) echo 'Selesai';
                else echo 'Unknown';
              ?></li>
        <li><b>Waktu:</b> <?= $transaction['created_at'] ?></li>
      </ul>
      <h5 class="card-title">Produk yang Dibeli</h5>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($details as $d): ?>
          <tr>
            <td><?php
              $db = db_connect();
              $produk = $db->table('product')->where('id', $d['product_id'])->get()->getRowArray();
              echo $produk ? $produk['nama'] : '-';
            ?></td>
            <td><?= $d['jumlah'] ?></td>
            <td>Rp <?= number_format($d['subtotal_harga'],0,',','.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?= $this->endSection() ?> 