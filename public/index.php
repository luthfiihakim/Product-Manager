<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

$flash = $_SESSION["flash"] ?? "";
unset($_SESSION["flash"]);

$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll();

function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Product Manager</h1>

        <?php if ($flash): ?>
            <div class="alert alert-success"><?= e($flash) ?></div>
        <?php endif; ?>

        <a class="btn" href="create.php">+ Tambah Produk</a>

        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <h3><?= e($product["name"]) ?></h3>
                    <p class="category"><?= e($product["category"]) ?></p>
                    <p class="price">Rp<?= number_format($product["price"], 0, ",", ".") ?></p>
                    <p>Stok: <?= e($product["stock"]) ?></p>
                    <div class="actions">
                        <a class="btn" href="edit.php?id=<?= (int)$product["id"] ?>">Edit</a>
                        <form method="POST" action="delete.php"
                              onsubmit="return confirm('Hapus produk ini?')">
                            <input type="hidden" name="id" value="<?= (int)$product["id"] ?>">
                            <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$products): ?>
            <p>Belum ada produk.</p>
        <?php endif; ?>
    </div>
</body>
</html>