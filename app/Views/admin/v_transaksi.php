<?php helper('number'); ?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<h3>Data Transaksi Pembelian Customer</h3>
<hr>
<form class="row g-3 mb-3" method="get" action="<?= base_url('admin/transaksi') ?>" id="filterForm">
    <div class="col-auto">
        <label class="form-label">Dari</label>
        <input type="date" name="tanggal_dari" class="form-control" id="tanggalDari" value="<?= isset($_GET['tanggal_dari']) ? htmlspecialchars($_GET['tanggal_dari']) : '' ?>">
    </div>
    <div class="col-auto">
        <label class="form-label">Sampai</label>
        <input type="date" name="tanggal_sampai" class="form-control" id="tanggalSampai" value="<?= isset($_GET['tanggal_sampai']) ? htmlspecialchars($_GET['tanggal_sampai']) : '' ?>">
    </div>
    <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
    </div>
</form>
<a href="<?= base_url('admin/transaksi/print') ?>" target="_blank" class="btn btn-success mb-3">Print</a>
<div class="table-responsive">
    <table class="table datatable">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Waktu Pembelian</th>
                <th>Total Bayar</th>
                <th>Alamat</th>
                <th>Bukti Pembayaran</th>
                <th>Detail</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Status Pengiriman</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($buy)) : foreach ($buy as $index => $item) : ?>
                <tr>
                    <th><?= $index + 1 ?></th>
                    <td><?= $item['username'] ?></td>
                    <td><?= $item['created_at'] ?></td>
                    <td><?= number_to_currency($item['total_harga'], 'IDR') ?></td>
                    <td><?= $item['alamat'] ?></td>
                    <td>
                        <?php if ($item['bukti_pembayaran']) : ?>
                            <a href="<?= base_url('writable/uploads/' . $item['bukti_pembayaran']) ?>" target="_blank">
                                <img src="<?= base_url('writable/uploads/' . $item['bukti_pembayaran']) ?>" alt="Bukti" style="max-width:80px;max-height:80px;object-fit:cover;" />
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#detailModal-<?= $item['id'] ?>">
                            Detail
                        </button>
                    </td>
                    <td>
                        <?php
                        $metodeLabel = [
                            'bank_transfer' => 'Bank Transfer',
                            'dana' => 'DANA',
                            'shopeepay' => 'ShopeePay',
                            'cod' => 'COD',
                        ];
                        $metode = strtolower($item['metode_pembayaran']);
                        echo $metodeLabel[$metode] ?? ucfirst($item['metode_pembayaran']);
                        ?>
                    </td>
                    <td>
                        <?php
                        $statusBayar = [
                            'pending' => 'Menunggu Pembayaran',
                            'bukti_upload' => 'Menunggu Konfirmasi Admin',
                            'cod' => 'COD',
                            'lunas' => 'Lunas',
                        ];
                        if ($item['status_pembayaran'] === 'bukti_upload') : ?>
                            <form method="post" action="<?= base_url('transaksi/update_status_pembayaran/' . $item['id']) ?>?datatable_page=<?= isset($_POST['datatable_page']) ? htmlspecialchars($_POST['datatable_page']) : '0' ?>">
                                <input type="hidden" name="status_pembayaran" value="lunas">
                                <button type="submit" class="btn btn-success btn-sm">Konfirmasi Lunas</button>
                            </form>
                            <form method="post" action="<?= base_url('transaksi/update_status_pembayaran/' . $item['id']) ?>?datatable_page=<?= isset($_POST['datatable_page']) ? htmlspecialchars($_POST['datatable_page']) : '0' ?>" style="margin-top:5px;">
                                <input type="hidden" name="status_pembayaran" value="bukti_upload_ulang">
                                <button type="submit" class="btn btn-warning btn-sm">Minta Kirim Ulang Bukti</button>
                            </form>
                        <?php else :
                            echo $statusBayar[$item['status_pembayaran']] ?? $item['status_pembayaran'];
                        endif; ?>
                    </td>
                    <td>
                        <?php
                        $statusList = [
                            '0' => 'Dikemas',
                            '1' => 'Dikirim',
                            '2' => 'Selesai'
                        ];
                        $canEditStatus = ($item['status_pembayaran'] === 'lunas' || $item['status_pembayaran'] === 'cod') && $item['status'] != '2';
                        if ($canEditStatus) : ?>
                            <form method="post" action="<?= base_url('transaksi/update_status/' . $item['id']) ?>?datatable_page=<?= isset($_POST['datatable_page']) ? htmlspecialchars($_POST['datatable_page']) : '0' ?>" class="d-inline">
                                <select name="status" class="form-select form-select-sm d-inline w-auto">
                                    <?php foreach ($statusList as $key => $label) : ?>
                                        <option value="<?= $key ?>" <?= $item['status'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                            </form>
                            <span class="ms-2 small text-muted">Status: <b><?= $statusList[$item['status']] ?? '-' ?></b></span>
                        <?php elseif ($item['status'] == '2'): ?>
                            <span class="text-success">Transaksi selesai</span>
                        <?php else: ?>
                            <span class="text-muted">Status dapat diubah setelah pembayaran dikonfirmasi</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/transaksi/delete/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus transaksi #<?= $item['id'] ?> dari <?= $item['username'] ?>? Tindakan ini tidak dapat dibatalkan.');">Hapus</a>
                    </td>
                </tr>
                <!-- Detail Modal Begin -->
                <div class="modal fade" id="detailModal-<?= $item['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php if (!empty($product[$item['id']])): foreach ($product[$item['id']] as $index2 => $item2) : ?>
                                    <?= $index2 + 1 . ")" ?>
                                    <?php if ($item2['foto'] != '' && file_exists("img/" . $item2['foto'])) : ?>
                                        <img src="<?= base_url() . "img/" . $item2['foto'] ?>" width="100px">
                                    <?php endif; ?>
                                    <strong><?= $item2['nama'] ?></strong>
                                    <?= number_to_currency($item2['harga'], 'IDR') ?><br>
                                    (<?= $item2['jumlah'] ?> pcs)<br>
                                    <?= number_to_currency($item2['subtotal_harga'], 'IDR') ?>
                                    <hr>
                                <?php endforeach; endif; ?>
                                Ongkir <?= number_to_currency($item['ongkir'], 'IDR') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Detail Modal End -->
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<script>
$(document).ready(function() {
    var table = $('.datatable').DataTable({
        stateSave: true
    });
    // Intercept submit, tambahkan page ke url agar tetap di slide yang sama
    $('.datatable').on('submit', 'form', function(e) {
        var form = this;
        var page = table.page();
        // Simpan page ke localStorage agar survive redirect
        localStorage.setItem('datatable_page', page);
    });
    // Setelah reload, cek localStorage dan set page jika ada
    var savedPage = localStorage.getItem('datatable_page');
    if(savedPage !== null) {
        table.page(parseInt(savedPage)).draw('page');
        localStorage.removeItem('datatable_page');
    }
});

window.addEventListener('DOMContentLoaded', function() {
    var dari = document.getElementById('tanggalDari');
    var sampai = document.getElementById('tanggalSampai');
    var today = new Date().toISOString().slice(0, 10);
    if (!dari.value) dari.value = today;
    if (!sampai.value) sampai.value = today;
});
</script>
<?= $this->endSection() ?>
