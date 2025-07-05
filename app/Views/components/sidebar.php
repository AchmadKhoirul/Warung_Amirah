<?php helper('number'); ?><?php helper('number'); ?><!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
        <?php
        if (session()->get('role') == 'admin') {
        ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'admin/dashboard') ? "" : "collapsed" ?>" href="<?= base_url('admin/dashboard') ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->
        <?php
        }
        ?>

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == '') ? "" : "collapsed" ?>" href="/">
                <i class="bi bi-grid"></i>
                <span>Home</span>
            </a>
        </li><!-- End Home Nav -->

        <!-- Blok menu Keranjang dihapus di sini -->
        <?php if (session()->get('role') !== 'admin') : ?>
        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'keranjang') ? '' : 'collapsed' ?>" href="keranjang">
                <i class="bi bi-cart-check"></i>
                <span>Keranjang</span>
            </a>
        </li><!-- End Keranjang Nav -->
        <?php endif; ?>

        <?php
        if (session()->get('role') == 'admin') {
        ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'produk') ? "" : "collapsed" ?>" href="<?= base_url('produk') ?>">
                    <i class="bi bi-receipt"></i>
                    <span>Produk</span>
                </a>
            </li><!-- End Produk Nav -->
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'admin/transaksi') ? "" : "collapsed" ?>" href="<?= base_url('admin/transaksi') ?>">
                    <i class="bi bi-list-check"></i>
                    <span>Data Transaksi</span>
                </a>
            </li><!-- End Data Transaksi Nav -->
        <?php
        }
        ?>
        <li class="nav-item">
    <a class="nav-link <?php echo (uri_string() == 'profile') ? "" : "collapsed" ?>" href="<?= base_url('profile') ?>">
        <i class="bi bi-person"></i>
        <span><?php echo (session()->get('role') == 'admin') ? 'Profile' : 'Data Pembelian'; ?></span>
    </a>
</li><!-- End Profile/Data Pembelian Nav -->
    </ul>

</aside><!-- End Sidebar-->