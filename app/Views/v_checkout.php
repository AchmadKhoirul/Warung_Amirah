<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <!-- Vertical Form -->
        <?= form_open('buy', 'class="row g-3" enctype="multipart/form-data"') ?>
        <?= form_hidden('username', session()->get('username')) ?>
        <?= form_input(['type' => 'hidden', 'name' => 'total_harga', 'id' => 'total_harga', 'value' => '']) ?>
        <div class="col-12">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo session()->get('username'); ?>" required>
        </div>
        <div class="col-12">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat" required>
        </div> 
        <div class="col-12">
            <label for="kelurahan" class="form-label">Kelurahan</label>
            <strong>select kelurahan</strong>
            <select class="form-control" id="kelurahan" name="kelurahan" required></select>
        </div>
        <div class="col-12">
            <label for="layanan" class="form-label">Layanan</label>
            <strong>select layanan</strong>
            <select class="form-control" id="layanan" name="layanan" required></select>
        </div>
        <div class="col-12">
            <label for="ongkir" class="form-label">Ongkir</label>
            <input type="text" class="form-control" id="ongkir" name="ongkir" readonly required>
        </div>
        <div class="col-12">
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="">-- Pilih Metode Pembayaran --</option>
                <option value="bank_transfer">Transfer Bank</option>
                <option value="dana">DANA</option>
                <option value="shopeepay">ShopeePay</option>
                <option value="ovo">OVO</option>
                <option value="gopay">GoPay</option>
                <option value="linkaja">LinkAja</option>
                <option value="cod">Bayar di Tempat (COD)</option>
            </select>
        </div>
        <div class="col-12" id="form_bukti_pembayaran">
            <label for="bukti_pembayaran" class="form-label">Upload Bukti Pembayaran (jpg/jpeg, wajib jika bukan COD)</label>
            <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg,image/jpg">
            <div id="keterangan_transfer" class="alert alert-info mt-2" style="display:none"></div>
            <?php if (!empty($bukti_pembayaran)) : ?>
                <div class="mt-2">
                    <label>Bukti pembayaran saat ini:</label><br>
                    <a href="<?= base_url('writable/uploads/' . $bukti_pembayaran) ?>" target="_blank">
                        <img src="<?= base_url('writable/uploads/' . $bukti_pembayaran) ?>" alt="Bukti" style="max-width:120px;max-height:120px;object-fit:cover;cursor:pointer;" />
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <!-- Vertical Form -->
        <div class="col-12">
            <!-- Default Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    if (!empty($items)) :
                        foreach ($items as $index => $item) :
                    ?>
                            <tr>
                                <td><?php echo $item['name'] ?></td>
                                <td><?php echo number_to_currency($item['price'], 'IDR') ?></td>
                                <td><?php echo $item['qty'] ?></td>
                                <td><?php echo number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                            </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    <tr>
                        <td colspan="2"></td>
                        <td>Subtotal</td>
                        <td><?php echo number_to_currency($total, 'IDR') ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Total</td>
                        <td><span id="total"><?php echo number_to_currency($total, 'IDR') ?></span></td>
                    </tr>
                </tbody>
            </table>
            <!-- End Default Table Example -->
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Buat Pesanan</button>
        </div>
        </form><!-- Vertical Form -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    var ongkir = 0;
    var total = 0; 
    hitungTotal();

    $('#kelurahan').select2({
    placeholder: 'Ketik nama kelurahan...',
    ajax: {
        url: '<?= base_url('get-location') ?>',
        dataType: 'json',
        delay: 1500,
        data: function (params) {
            return {
                search: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data.map(function(item) {
                return {
                    id: item.id,
                    text: item.subdistrict_name + ", " + item.district_name + ", " + item.city_name + ", " + item.province_name + ", " + item.zip_code
                };
                })
            };
        },
        cache: true
    },
    minimumInputLength: 3
});

$("#kelurahan").on('change', function() {
    var id_kelurahan = $(this).val(); 
    $("#layanan").empty();
    ongkir = 0;

    $.ajax({
        url: "<?= site_url('get-cost') ?>",
        type: 'GET',
        data: { 
            'destination': id_kelurahan, 
        },
        dataType: 'json',
        success: function(data) { 
            data.forEach(function(item) {
                var text = item["description"] + " (" + item["service"] + ") : estimasi " + item["etd"] + "";
                $("#layanan").append($('<option>', {
                    value: item["cost"],
                    text: text 
                }));
            });
            hitungTotal(); 
        },
    });
});

$("#layanan").on('change', function() {
    ongkir = parseInt($(this).val());
    hitungTotal();
});  

    function hitungTotal() {
        total = ongkir + <?= $total ?>;

        $("#ongkir").val(ongkir);
        $("#total").html("IDR " + total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
        $("#total_harga").val(total);
    }

    function showInstruksiPembayaran() {
        var metode = $('#metode_pembayaran').val();
        var info = '';
        if(metode === 'bank_transfer') {
            info = '<div class="alert alert-info">' +
                   '<h6><i class="bi bi-bank"></i> <strong>Transfer Bank</strong></h6>' +
                   '<p><strong>Bank BCA</strong><br>' +
                   'No. Rekening: <strong>1234567890</strong><br>' +
                   'Atas Nama: <strong>WARUNG AMIRAH</strong><br>' +
                   'Cabang: <strong>Jakarta Pusat</strong></p>' +
                   '<p><strong>Bank Mandiri</strong><br>' +
                   'No. Rekening: <strong>0987654321</strong><br>' +
                   'Atas Nama: <strong>WARUNG AMIRAH</strong><br>' +
                   'Cabang: <strong>Jakarta Selatan</strong></p>' +
                   '<p><strong>Bank BRI</strong><br>' +
                   'No. Rekening: <strong>1122334455</strong><br>' +
                   'Atas Nama: <strong>WARUNG AMIRAH</strong><br>' +
                   'Cabang: <strong>Jakarta Barat</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Transfer sesuai total pembayaran ke salah satu rekening di atas</li>' +
                   '<li>Simpan bukti transfer (screenshot atau foto)</li>' +
                   '<li>Upload bukti transfer pada form di bawah ini</li>' +
                   '<li>Klik "Buat Pesanan" untuk mengirim pesanan</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Pembayaran akan dikonfirmasi dalam 1x24 jam setelah bukti transfer diterima.</p>' +
                   '</div>';
        } else if(metode === 'dana') {
            info = '<div class="alert alert-info">' +
                   '<h6><i class="bi bi-phone"></i> <strong>DANA</strong></h6>' +
                   '<p><strong>Nomor DANA:</strong> <strong>0812-3456-7890</strong><br>' +
                   '<strong>Atas Nama:</strong> <strong>WARUNG AMIRAH</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Buka aplikasi DANA</li>' +
                   '<li>Pilih menu "Kirim"</li>' +
                   '<li>Masukkan nomor <strong>0812-3456-7890</strong></li>' +
                   '<li>Masukkan nominal sesuai total pembayaran</li>' +
                   '<li>Tambahkan catatan: "Pembayaran Warung Amirah"</li>' +
                   '<li>Konfirmasi dan kirim pembayaran</li>' +
                   '<li>Simpan bukti transfer (screenshot)</li>' +
                   '<li>Upload bukti transfer pada form di bawah ini</li>' +
                   '<li>Klik "Buat Pesanan" untuk mengirim pesanan</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Pembayaran akan dikonfirmasi dalam 1x24 jam setelah bukti transfer diterima.</p>' +
                   '</div>';
        } else if(metode === 'shopeepay') {
            info = '<div class="alert alert-info">' +
                   '<h6><i class="bi bi-credit-card"></i> <strong>ShopeePay</strong></h6>' +
                   '<p><strong>Nomor ShopeePay:</strong> <strong>0812-3456-7890</strong><br>' +
                   '<strong>Atas Nama:</strong> <strong>WARUNG AMIRAH</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Buka aplikasi Shopee</li>' +
                   '<li>Pilih menu "ShopeePay"</li>' +
                   '<li>Pilih "Kirim"</li>' +
                   '<li>Masukkan nomor <strong>0812-3456-7890</strong></li>' +
                   '<li>Masukkan nominal sesuai total pembayaran</li>' +
                   '<li>Tambahkan catatan: "Pembayaran Warung Amirah"</li>' +
                   '<li>Konfirmasi dan kirim pembayaran</li>' +
                   '<li>Simpan bukti transfer (screenshot)</li>' +
                   '<li>Upload bukti transfer pada form di bawah ini</li>' +
                   '<li>Klik "Buat Pesanan" untuk mengirim pesanan</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Pembayaran akan dikonfirmasi dalam 1x24 jam setelah bukti transfer diterima.</p>' +
                   '</div>';
        } else if(metode === 'ovo') {
            info = '<div class="alert alert-info">' +
                   '<h6><i class="bi bi-phone"></i> <strong>OVO</strong></h6>' +
                   '<p><strong>Nomor OVO:</strong> <strong>0812-3456-7890</strong><br>' +
                   '<strong>Atas Nama:</strong> <strong>WARUNG AMIRAH</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Buka aplikasi OVO</li>' +
                   '<li>Pilih menu "Transfer"</li>' +
                   '<li>Pilih "Transfer ke OVO"</li>' +
                   '<li>Masukkan nomor <strong>0812-3456-7890</strong></li>' +
                   '<li>Masukkan nominal sesuai total pembayaran</li>' +
                   '<li>Tambahkan pesan: "Pembayaran Warung Amirah"</li>' +
                   '<li>Konfirmasi dan kirim pembayaran</li>' +
                   '<li>Simpan bukti transfer (screenshot)</li>' +
                   '<li>Upload bukti transfer pada form di bawah ini</li>' +
                   '<li>Klik "Buat Pesanan" untuk mengirim pesanan</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Pembayaran akan dikonfirmasi dalam 1x24 jam setelah bukti transfer diterima.</p>' +
                   '</div>';
        } else if(metode === 'gopay') {
            info = '<div class="alert alert-info">' +
                   '<h6><i class="bi bi-phone"></i> <strong>GoPay</strong></h6>' +
                   '<p><strong>Nomor GoPay:</strong> <strong>0812-3456-7890</strong><br>' +
                   '<strong>Atas Nama:</strong> <strong>WARUNG AMIRAH</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Buka aplikasi Gojek</li>' +
                   '<li>Pilih menu "GoPay"</li>' +
                   '<li>Pilih "Transfer"</li>' +
                   '<li>Pilih "Transfer ke GoPay"</li>' +
                   '<li>Masukkan nomor <strong>0812-3456-7890</strong></li>' +
                   '<li>Masukkan nominal sesuai total pembayaran</li>' +
                   '<li>Tambahkan catatan: "Pembayaran Warung Amirah"</li>' +
                   '<li>Konfirmasi dan kirim pembayaran</li>' +
                   '<li>Simpan bukti transfer (screenshot)</li>' +
                   '<li>Upload bukti transfer pada form di bawah ini</li>' +
                   '<li>Klik "Buat Pesanan" untuk mengirim pesanan</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Pembayaran akan dikonfirmasi dalam 1x24 jam setelah bukti transfer diterima.</p>' +
                   '</div>';
        } else if(metode === 'linkaja') {
            info = '<div class="alert alert-info">' +
                   '<h6><i class="bi bi-phone"></i> <strong>LinkAja</strong></h6>' +
                   '<p><strong>Nomor LinkAja:</strong> <strong>0812-3456-7890</strong><br>' +
                   '<strong>Atas Nama:</strong> <strong>WARUNG AMIRAH</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Buka aplikasi LinkAja</li>' +
                   '<li>Pilih menu "Transfer"</li>' +
                   '<li>Pilih "Transfer ke LinkAja"</li>' +
                   '<li>Masukkan nomor <strong>0812-3456-7890</strong></li>' +
                   '<li>Masukkan nominal sesuai total pembayaran</li>' +
                   '<li>Tambahkan pesan: "Pembayaran Warung Amirah"</li>' +
                   '<li>Konfirmasi dan kirim pembayaran</li>' +
                   '<li>Simpan bukti transfer (screenshot)</li>' +
                   '<li>Upload bukti transfer pada form di bawah ini</li>' +
                   '<li>Klik "Buat Pesanan" untuk mengirim pesanan</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Pembayaran akan dikonfirmasi dalam 1x24 jam setelah bukti transfer diterima.</p>' +
                   '</div>';
        } else if(metode === 'cod') {
            info = '<div class="alert alert-success">' +
                   '<h6><i class="bi bi-cash-coin"></i> <strong>Bayar di Tempat (COD)</strong></h6>' +
                   '<p><strong>Pembayaran dilakukan saat barang diterima</strong></p>' +
                   '<hr>' +
                   '<p><strong>Langkah Pembayaran:</strong></p>' +
                   '<ol>' +
                   '<li>Pesanan akan diproses setelah checkout</li>' +
                   '<li>Barang akan dikirim ke alamat yang telah diisi</li>' +
                   '<li>Pembayaran dilakukan saat barang diterima</li>' +
                   '<li>Pastikan menyiapkan uang tunai sesuai total pembayaran</li>' +
                   '</ol>' +
                   '<p><strong>Catatan:</strong> Tidak perlu upload bukti pembayaran untuk metode COD.</p>' +
                   '</div>';
        }
        $('#keterangan_transfer').html(info).toggle(info !== '');
    }
    function toggleBuktiPembayaran() {
        var metode = $('#metode_pembayaran').val();
        if (metode === 'cod') {
            $('#form_bukti_pembayaran').hide();
            $('#bukti_pembayaran').prop('required', false);
        } else {
            $('#form_bukti_pembayaran').show();
            $('#bukti_pembayaran').prop('required', true);
        }
    }
    $('#metode_pembayaran').on('change', function() {
        toggleBuktiPembayaran();
        showInstruksiPembayaran();
    });
    toggleBuktiPembayaran(); // initial
    showInstruksiPembayaran();
    $('#metode_pembayaran').trigger('change');
});
</script>
<?= $this->endSection() ?>