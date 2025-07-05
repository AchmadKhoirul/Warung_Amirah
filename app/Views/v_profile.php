<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
History Transaksi Pembelian <strong><?= $username ?></strong>
<hr>
<div class="table-responsive">
    <!-- Table with stripped rows -->
    <table class="table datatable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID Pembelian</th>
                <th scope="col">Waktu Pembelian</th>
                <th scope="col">Total Bayar</th>
                <th scope="col">Alamat</th>
                <th scope="col">Status</th>
                <th scope="col">Metode Pembayaran</th>
                <th scope="col">Bukti Pembayaran</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($buy)) :
                foreach ($buy as $index => $item) :
            ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1 ?></th>
                        <td><?php echo $item['id'] ?></td>
                        <td><?php echo $item['created_at'] ?></td>
                        <td><?php echo number_to_currency($item['total_harga'], 'IDR') ?></td>
                        <td><?php echo $item['alamat'] ?></td>
                        <td>
                            <?php
                            $statusList = [
                                '0' => 'Diproses',
                                '1' => 'Dikemas',
                                '2' => 'Dikirim',
                                '3' => 'Selesai'
                            ];
                            $role = session()->get('role');
                            if ($role === 'admin') :
                            ?>
                                <form method="post" action="<?= base_url('transaksi/update_status/' . $item['id']) ?>">
                                    <select name="status" class="form-select form-select-sm d-inline w-auto">
                                        <?php foreach ($statusList as $key => $label) : ?>
                                            <option value="<?= $key ?>" <?= $item['status'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </form>
                            <?php else : ?>
                                <?= $statusList[$item['status']] ?? '-' ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $metodeLabel = [
                                'bank_transfer' => 'Bank Transfer',
                                'dana' => 'DANA',
                                'shopeepay' => 'ShopeePay',
                                'cod' => 'COD',
                            ];
                            $metode = strtolower($item['metode_pembayaran'] ?? '');
                            echo $metodeLabel[$metode] ?? ucfirst($item['metode_pembayaran'] ?? '-');
                            ?>
                        </td>
                        <td>
                            <?php if ($item['metode_pembayaran'] === 'cod') : ?>
                                <span class="text-success">COD - Tidak perlu upload bukti pembayaran</span>
                            <?php elseif ($item['bukti_pembayaran']) : ?>
                                <a href="<?= base_url('writable/uploads/' . $item['bukti_pembayaran']) ?>" target="_blank">
                                    <img src="<?= base_url('writable/uploads/' . $item['bukti_pembayaran']) ?>" alt="Bukti" style="max-width:80px;max-height:80px;object-fit:cover;cursor:pointer;" />
                                </a>
                                
                                <?php if ($item['status_pembayaran'] === 'lunas') : ?>
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-check-circle"></i> Pembayaran sudah dikonfirmasi admin
                                    </div>
                                <?php elseif ($item['status_pembayaran'] === 'bukti_upload_ulang') : ?>
                                    <div class="text-warning small mt-1">
                                        <i class="bi bi-exclamation-triangle"></i> Admin meminta bukti pembayaran ulang
                                    </div>
                                    <form method="post" action="<?= base_url('transaksi/upload_bukti/' . $item['id']) ?>" enctype="multipart/form-data" class="mt-2">
                                        <input type="file" name="bukti_pembayaran" accept="image/jpeg,image/jpg" required class="form-control mb-2" style="max-width:200px;display:inline-block;">
                                        <button type="submit" class="btn btn-warning btn-sm">Upload Bukti Ulang</button>
                                    </form>
                                <?php else : ?>
                                    <div class="text-info small mt-1">Bukti sudah terupload, menunggu konfirmasi admin</div>
                                    <form method="post" action="<?= base_url('transaksi/upload_bukti/' . $item['id']) ?>" enctype="multipart/form-data" class="mt-2">
                                        <input type="file" name="bukti_pembayaran" accept="image/jpeg,image/jpg" class="form-control mb-2" style="max-width:200px;display:inline-block;">
                                        <button type="submit" class="btn btn-warning btn-sm">Ganti Bukti</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <form method="post" action="<?= base_url('transaksi/upload_bukti/' . $item['id']) ?>" enctype="multipart/form-data">
                                    <input type="file" name="bukti_pembayaran" accept="image/jpeg,image/jpg" required class="form-control mb-2" style="max-width:200px;display:inline-block;">
                                    <button type="submit" class="btn btn-info btn-sm">Kirim Bukti</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            // Hitung selisih waktu dalam detik
                            $created = strtotime($item['created_at']);
                            $now = time();
                            $diffSeconds = $now - $created;
                            $canCancel = $diffSeconds < 60 && (
                                $item['status_pembayaran'] === 'pending' ||
                                $item['status_pembayaran'] === 'bukti_upload' ||
                                ($item['status_pembayaran'] === 'cod' && $item['status'] == '0')
                            );
                            if ($item['status_pembayaran'] === 'cancel') {
                                echo '<span class="text-danger">Dibatalkan</span>';
                            } elseif ($canCancel) {
                                $remaining = 60 - $diffSeconds;
                                $countdownId = 'countdown-cancel-' . $item['id'];
                            ?>
                                <form method="post" action="<?= base_url('transaksi/cancel/' . $item['id']) ?>" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    <div class="small text-muted mt-1">
                                        Batas waktu cancel: <span id="<?= $countdownId ?>"><?= $remaining ?></span> detik
                                    </div>
                                </form>
                                <script>
                                (function() {
                                    var countdown = <?= $remaining ?>;
                                    var el = document.getElementById('<?= $countdownId ?>');
                                    if (el) {
                                        var interval = setInterval(function() {
                                            countdown--;
                                            if (countdown <= 0) {
                                                el.innerText = '0';
                                                clearInterval(interval);
                                                location.reload();
                                            } else {
                                                el.innerText = countdown;
                                            }
                                        }, 1000);
                                    }
                                })();
                                </script>
                            <?php
                            } elseif ($diffSeconds >= 60 && ($item['status_pembayaran'] === 'pending' || $item['status_pembayaran'] === 'bukti_upload')) {
                                echo '<span class="text-muted">Batas waktu cancel habis</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#detailModal-<?= $item['id'] ?>">
                                Detail
                            </button>
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
                                    <?php foreach ($product[$item['id']] as $index2 => $item2) : ?>
                                        <?php echo $index2 + 1 . ")" ?>
                                        <?php if ($item2['foto'] != '' and file_exists("img/" . $item2['foto'] . "")) : ?>
                                            <img src="<?php echo base_url() . "img/" . $item2['foto'] ?>" width="100px">
                                        <?php endif; ?>
                                        <strong><?= $item2['nama'] ?></strong>
                                        <?= number_to_currency($item2['harga'], 'IDR') ?>
                                        <br>
                                        <?= "(" . $item2['jumlah'] . " pcs)" ?><br>
                                        <?= number_to_currency($item2['subtotal_harga'], 'IDR') ?>
                                        <hr>
                                    <?php endforeach; ?>
                                    Ongkir <?= number_to_currency($item['ongkir'], 'IDR') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Detail Modal End -->
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
    <!-- End Table with stripped rows -->
</div>
<?= $this->endSection() ?>