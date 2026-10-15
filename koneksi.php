
<?php

$localhost = "localhost";
$username = "root";
$password = "";
$db = "db_laporan_fasilitas";

try {
    $conn = new PDO(
        "mysql:host=$localhost;dbname=$db;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conn->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
