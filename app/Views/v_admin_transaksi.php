<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="pagetitle">
  <h1>Manajemen Transaksi</h1>
</div>
<section class="section">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Daftar Transaksi</h5>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Total Harga</th>
            <th>Alamat</th>
            <th>Ongkir</th>
            <th>Status</th>
            <th>Waktu</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transactions as $trx): ?>
          <tr>
            <td><?= $trx['id'] ?></td>
            <td><?= $trx['username'] ?></td>
            <td>Rp <?= number_format($trx['total_harga'],0,',','.') ?></td>
            <td><?= $trx['alamat'] ?></td>
            <td><?= $trx['ongkir'] ?></td>
            <td>
              <?php
                if ($trx['status'] == 0) echo 'Diproses';
                elseif ($trx['status'] == 1) echo 'Dikemas';
                elseif ($trx['status'] == 2) echo 'Dikirim';
                elseif ($trx['status'] == 3) echo 'Selesai';
                else echo 'Unknown';
              ?>
            </td>
            <td><?= $trx['created_at'] ?></td>
            <td>
              <a href="<?= base_url('admin/transaksi/detail/'.$trx['id']) ?>" class="btn btn-info btn-sm">Detail</a>
              <?php if ($trx['status'] == 0): ?>
                <a href="<?= base_url('admin/transaksi/ubahStatus/'.$trx['id'].'/1') ?>" class="btn btn-warning btn-sm">Dikemas</a>
              <?php elseif ($trx['status'] == 1): ?>
                <a href="<?= base_url('admin/transaksi/ubahStatus/'.$trx['id'].'/2') ?>" class="btn btn-primary btn-sm">Kirim</a>
              <?php elseif ($trx['status'] == 2): ?>
                <a href="<?= base_url('admin/transaksi/ubahStatus/'.$trx['id'].'/3') ?>" class="btn btn-success btn-sm">Selesai</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?= $this->endSection() ?> 