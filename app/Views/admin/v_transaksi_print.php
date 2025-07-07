<!DOCTYPE html>
<html>
<head>
    <title>Print Data Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px 8px; font-size: 13px; }
        th { background: #eee; }
        .img-bukti { max-width: 80px; max-height: 80px; object-fit: cover; }
        .print-title { text-align: center; margin-bottom: 20px; }
        .no-print { display: block; margin-bottom: 15px; }
        @media print { .no-print { display: none !important; } }
        .filter-form { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; background: #f8f9fa; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; }
        .filter-form label { margin-bottom: 0; font-weight: 500; }
        .filter-form select, .filter-form input[type="date"] { padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc; }
        .filter-form button { padding: 5px 14px; border-radius: 4px; border: none; background: #198754; color: #fff; font-weight: 500; cursor: pointer; }
        .filter-form button[type="button"] { background: #0d6efd; margin-left: 4px; }
        .filter-form button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <h2 class="print-title">Data Transaksi Pembelian Customer</h2>
    <a href="<?= base_url('admin/transaksi') ?>" class="no-print" style="display:inline-block;margin-bottom:12px;padding:6px 16px;background:#6c757d;color:#fff;border-radius:5px;text-decoration:none;font-weight:500;">&larr; Kembali ke Data Transaksi</a>
    <form class="no-print filter-form" method="get" action="<?= base_url('admin/transaksi/print') ?>">
        <label>Status</label>
        <select name="status">
            <option value="">Semua</option>
            <option value="0" <?= (isset($status) && $status === '0') ? 'selected' : '' ?>>Diproses</option>
            <option value="1" <?= (isset($status) && $status === '1') ? 'selected' : '' ?>>Dikemas</option>
            <option value="2" <?= (isset($status) && $status === '2') ? 'selected' : '' ?>>Dikirim</option>
            <option value="3" <?= (isset($status) && $status === '3') ? 'selected' : '' ?>>Selesai</option>
        </select>
        <label>Dari</label>
        <input type="date" name="tanggal_dari" id="tanggalDariPrint" value="<?= isset($tanggal_dari) ? htmlspecialchars($tanggal_dari) : '' ?>">
        <label>Sampai</label>
        <input type="date" name="tanggal_sampai" id="tanggalSampaiPrint" value="<?= isset($tanggal_sampai) ? htmlspecialchars($tanggal_sampai) : '' ?>">
        <button type="submit">Terapkan Filter</button>
        <button type="button" onclick="window.print()">Print</button>
    </form>
    <script>
    window.addEventListener('DOMContentLoaded', function() {
        var dari = document.getElementById('tanggalDariPrint');
        var sampai = document.getElementById('tanggalSampaiPrint');
        var today = new Date().toISOString().slice(0, 10);
        if (!dari.value) dari.value = today;
        if (!sampai.value) sampai.value = today;
    });
    </script>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Waktu Pembelian</th>
                <th>Total Bayar</th>
                <th>Alamat</th>
                <th>Bukti Pembayaran</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Status Pengiriman</th>
                <th>Detail Produk</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($buy)) : foreach ($buy as $index => $item) : ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $item['username'] ?></td>
                    <td><?= $item['created_at'] ?></td>
                    <td><?= number_format($item['total_harga'],0,',','.') ?></td>
                    <td><?= $item['alamat'] ?></td>
                    <td>
                        <?php if ($item['bukti_pembayaran']) : ?>
                            <img src="<?= base_url('writable/uploads/' . $item['bukti_pembayaran']) ?>" class="img-bukti" />
                        <?php else: ?>-
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $metodeLabel = [
                            'bank_transfer' => 'Bank Transfer',
                            'dana' => 'DANA',
                            'shopeepay' => 'ShopeePay',
                            'ovo' => 'OVO',
                            'gopay' => 'GoPay',
                            'linkaja' => 'LinkAja',
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
                        echo $statusBayar[$item['status_pembayaran']] ?? $item['status_pembayaran'];
                        ?>
                    </td>
                    <td>
                        <?php
                        $statusList = [
                            '0' => 'Diproses',
                            '1' => 'Dikemas',
                            '2' => 'Dikirim',
                            '3' => 'Selesai'
                        ];
                        echo $statusList[$item['status']] ?? '-';
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($product[$item['id']])): foreach ($product[$item['id']] as $index2 => $item2) : ?>
                            <?= $index2 + 1 . ") " ?>
                            <strong><?= $item2['nama'] ?></strong>
                            (<?= $item2['jumlah'] ?> pcs) - Rp <?= number_format($item2['subtotal_harga'],0,',','.') ?><br>
                        <?php endforeach; endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</body>
</html> 