<?php include ('includes/header.php'); ?>

<div class="modal fade" id="addCustomerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Pelanggan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Masukkan Nama Customer</label>
                    <input type="text" id="c_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Masukkan Kelas Pelanggan</label>
                    <input type="text" id="c_class" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Masukkan Nomor Telepon Pelanggan</label>
                    <input type="number" id="c_phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Masukkan Email Pelanggan (optional)</label>
                    <input type="email" id="c_email" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveCustomer">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="mb-0 text-center">Tambahkan Pesanan
                <a href="customers.php" class="btn btn-danger float-end"><i class="fa fa-chevron-left"
                        aria-hidden="true"></i> Back</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>
            <form action="orders-code.php" method="POST">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="">Pilih Produk</label>
                        <?php
                        // Membuat query untuk mengambil data produk dan kategori mereka
                        $query = "SELECT p.id, p.name, c.name as category 
                        FROM products p 
                        JOIN categories c ON p.category_id = c.id";

                        $result = mysqli_query($conn, $query);
                        ?>

                        <select name="product_id" class="form-select mySelect2">
                            <option value="">-- Pilih Produk --</option>
                            <?php
                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    while ($prodItem = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <option value="<?= $prodItem['id'] ?>"><?= $prodItem['name'] ?> -
                                            <?= $prodItem['category'] ?></option>
                                        <?php
                                    }
                                } else {
                                    echo '<option value="">Produk tidak ditemukan!</option>';
                                }
                            } else {
                                echo '<option value="">Ada sesuatu yang salah!</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="">Kuantitas</label>
                        <input type="number" name="quantity" value="1" required class="form-control">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" name="addItem" class="btn btn-success w-100"><i class="fa fa-plus"
                                aria-hidden="true"></i> Tambah Item</button>
                    </div>
                </div>
            </form>
            <form id="barcode-form" action="orders-code.php" method="POST">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="">Pindai Barcode</label>
                        <input type="text" name="product_code" id="barcode-input" class="form-control" autofocus>
                        <input type="hidden" name="quantity" value="1">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header text-center">
            <h4 class="mb-0">Produk</h4>
        </div>
        <div class="card-body" id="productArea">
            <?php if (isset($_SESSION['productItems'])) {
                $sessionProducts = $_SESSION['productItems'];
                if (empty($sessionProducts)) {
                    unset($_SESSION['productItemIds']);
                    unset($_SESSION['productItems']);
                }
                ?>
                <div class="table-responsive mb-3" id="productContent">
                    <table class="table table-bordered table-striped align-items-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Kuantitas</th>
                                <th>Total Harga</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $subtotal = 0; // Initialize subtotal variable
                            foreach ($sessionProducts as $key => $item):
                                $totalPrice = $item['price'] * $item['quantity'];
                                $subtotal += $totalPrice; // Add each item's total price to subtotal
                                ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td>
                                        <?= $item['name']; ?>
                                        <p>Rp. <?= number_format($item['price'], 0, ',', '.'); ?></p>
                                    </td>
                                    <td>
                                        <div class="input-group qtyBox">
                                            <input type="hidden" value="<?= $item['product_id']; ?>" class="prodId">
                                            <button class="input-group-text decrement">-</button>
                                            <input type="text" value="<?= $item['quantity']; ?>"
                                                class="qty form-control quantityInput">
                                            <button class="input-group-text increment">+</button>
                                        </div>
                                    </td>
                                    <td>Rp. <?= number_format($totalPrice, 0, ',', '.'); ?></td>
                                    <td>
                                        <a class="noselect button" href="orders-item-delete.php?index=<?= $key; ?>"><span
                                                class="text">Delete</span><span class="icon"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z">
                                                    </path>
                                                </svg></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                <td colspan="2">Rp. <?= number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    <div class="row">
                        <hr>
                        <div class="col-md-4 mb-3">
                            <label for="">Pilih Metode Pembayaran</label>
                            <select id="payment_mode" class="form-select">
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <option value="Uang Tunai">Uang Tunai</option>
                                <option value="Bayar Online">Bayar Online</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Bayar</label>
                            <input type="hidden" id="cphone" class="form-control" value="">
                            <input type="number" id="money" class="form-control" value="">

                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-warning w-100 proceedToPlace">Lanjutkan <i
                                    class="fa fa-chevron-right" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            <?php } else {
                echo '<h5 class="text-center">Tidak ada yang ditambah</h5>';
            } ?>
        </div>
    </div>
</div>

<?php include ('includes/footer.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barcodeInput = document.getElementById('barcode-input');

        // Focus on the barcode input field on page load
        barcodeInput.focus();

        barcodeInput.addEventListener(function () {
            // Submit the form when input is detected
            document.getElementById('barcode-form').submit();
        });

        // Event listener for form submission to refocus the barcode input
        document.getElementById('barcode-form').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Submit the form via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'orders-code.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Handle the response if necessary
                    console.log('Form submitted successfully');

                    // Clear the input
                    barcodeInput.value = '';

                    // Refocus the input field
                    barcodeInput.focus();
                }
            };
            xhr.send(new URLSearchParams(new FormData(this)).toString());
        });
    });

</script>