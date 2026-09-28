<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

$flash = $_SESSION["flash"] ?? "";
unset($_SESSION["flash"]);

function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
}

// Parameter GET
$q   = trim($_GET["q"] ?? "");
$cat = trim($_GET["category"] ?? "");

// Query produk (search + filter) dengan prepared statement
$sql = "SELECT * FROM products WHERE (name LIKE :q1 OR category LIKE :q2)";
$params = ["q1" => "%$q%", "q2" => "%$q%"];

if ($cat !== "") {
    $sql .= " AND category = :cat";
    $params["cat"] = $cat;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Daftar kategori untuk dropdown
$stmt = $pdo->prepare("SELECT DISTINCT category FROM products ORDER BY category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

$isFiltering = ($q !== "" || $cat !== "");
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

        <form method="GET" class="search-form">
            <input type="text" name="q" placeholder="Cari nama atau kategori..."
                   value="<?= e($q) ?>">
            <select name="category">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c) ?>" <?= $c === $cat ? "selected" : "" ?>>
                        <?= e($c) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Cari</button>
            <?php if ($isFiltering): ?>
                <a href="index.php" class="reset">Reset</a>
            <?php endif; ?>
        </form>

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
            <p><?= $isFiltering ? "Tidak ada produk yang cocok." : "Belum ada produk." ?></p>
        <?php endif; ?>
    </div>
</body>
</html>