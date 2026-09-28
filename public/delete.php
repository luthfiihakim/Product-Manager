<?php

session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Hanya terima POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Metode tidak diizinkan.");
}

// 2. Cek token CSRF
$token = $_POST["csrf"] ?? "";
if (empty($_SESSION["csrf"]) || $token === "" || !hash_equals($_SESSION["csrf"], $token)) {
    http_response_code(403);
    exit("Token tidak valid. Kembali ke daftar produk, refresh halaman, lalu coba lagi.");
}

// 3. Validasi ID
$id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT);
if ($id === false || $id <= 0) {
    $_SESSION["flash"] = "ID produk tidak valid.";
    header("Location: index.php");
    exit;
}

// 4. Hapus dengan prepared statement
$stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute(["id" => $id]);

// 5. Pesan + PRG
if ($stmt->rowCount() > 0) {
    $_SESSION["flash"] = "Produk berhasil dihapus.";
} else {
    $_SESSION["flash"] = "Produk tidak ditemukan.";
}

header("Location: index.php");
exit;