<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Ambil ID dari URL dan pastikan angka valid
$id = filter_var($_GET["id"] ?? "", FILTER_VALIDATE_INT);
if ($id === false || $id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil produk berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(["id" => $id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION["flash"] = "Produk tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$errors = [];
$name     = $product["name"];
$category = $product["category"];
$price    = (string)$product["price"];
$stock    = (string)$product["stock"];

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

    // Nama harus unik (kecuali produk ini sendiri)
    if (!$errors) {
        $stmt = $pdo->prepare(
            "SELECT id FROM products WHERE name = :name AND id <> :id"
        );
        $stmt->execute(["name" => $name, "id" => $id]);
        if ($stmt->fetch()) {
            $errors[] = "Nama produk sudah dipakai.";
        }
    }

    // Update + PRG
    if (!$errors) {
        $stmt = $pdo->prepare(
            "UPDATE products
             SET name = :name, category = :category, price = :price, stock = :stock
             WHERE id = :id"
        );
        $stmt->execute([
            "name"     => $name,
            "category" => $category,
            "price"    => $priceInt,
            "stock"    => $stockInt,
            "id"       => $id,
        ]);
        $_SESSION["flash"] = "Produk berhasil diperbarui.";
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
    <title>Edit Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>

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
            <button type="submit">Perbarui</button>
            <a href="index.php">Kembali</a>
        </form>
    </div>
</body>
</html>