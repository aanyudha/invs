<?php
$host = "localhost";
$port = "3310";
$user = "root";
$pass = "12345";
$db   = "lokka_investor";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>