<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
$name = $category = $price = $stock = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Normalisasi
    $name     = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price    = trim($_POST["price"] ?? "");
    $stock    = trim($_POST["stock"] ?? "");

    // Validasi
    if (mb_strlen($name) < 3) {
        $errors[] = "Nama minimal 3 karakter.";
    }
    if ($category === "") {
        $errors[] = "Kategori wajib diisi.";
    }
    $priceInt = filter_var($price, FILTER_VALIDATE_INT);
    if ($priceInt === false || $priceInt <= 0) {
        $errors[] = "Harga harus angka bulat lebih dari 0.";
    }
    $stockInt = filter_var($stock, FILTER_VALIDATE_INT);
    if ($stockInt === false || $stockInt < 0) {
        $errors[] = "Stok harus angka bulat 0 atau lebih.";
    }

    // Nama harus unik
    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE name = :name");
        $stmt->execute(["name" => $name]);
        if ($stmt->fetch()) {
            $errors[] = "Nama produk sudah dipakai.";
        }
    }

    // Simpan + PRG
    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO products (name, category, price, stock)
             VALUES (:name, :category, :price, :stock)"
        );
        $stmt->execute([
            "name"     => $name,
            "category" => $category,
            "price"    => $priceInt,
            "stock"    => $stockInt,
        ]);
        $_SESSION["flash"] = "Produk berhasil ditambahkan.";
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Tambah Produk</h1>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e, ENT_QUOTES, "UTF-8") ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <label>Nama
                <input type="text" name="name"
                       value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>">
            </label>
            <label>Kategori
                <input type="text" name="category"
                       value="<?= htmlspecialchars($category, ENT_QUOTES, "UTF-8") ?>">
            </label>
            <label>Harga
                <input type="text" name="price"
                       value="<?= htmlspecialchars($price, ENT_QUOTES, "UTF-8") ?>">
            </label>
            <label>Stok
                <input type="text" name="stock"
                       value="<?= htmlspecialchars($stock, ENT_QUOTES, "UTF-8") ?>">
            </label>
            <button type="submit">Simpan</button>
            <a href="index.php">Kembali</a>
        </form>
    </div>
</body>
</html>