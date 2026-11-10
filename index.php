<?php
require_once "config/koneksi.php";

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'petugas') {
    header("Location: petugas/dashboard.php");
    exit;
}

if ($_SESSION['role'] == 'siswa') {
    header("Location: siswa/dashboard.php");
    exit;
}

session_destroy();
header("Location: login.php");
exit;
?>