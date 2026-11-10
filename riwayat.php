
<?php
require_once "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id = (int)$_SESSION['id'];

$stmt = $conn->prepare("
    SELECT p.*, a.nama_alat
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.user_id = ?
    ORDER BY p.id DESC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$riwayat = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Peminjaman - LabMate</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">Lab<span>Mate.</span></div>
    <p class="menu-title">MENU UTAMA</p>
    <a href="dashboard.php">▦ Dashboard</a>
    <a href="alat.php">▣ Daftar Alat</a>
    <a href="peminjaman.php">＋ Ajukan Peminjaman</a>
    <p class="menu-title">LAINNYA</p>
    <a href="riwayat.php" class="active">◷ Riwayat Peminjaman</a>
    <a href="pengembalian.php">↩ Pengembalian</a>
    <a href="../logout.php" class="logout">⇥ Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <div>
            <h2>Riwayat Peminjaman</h2>
            <p>Daftar seluruh aktivitas peminjaman alat.</p>
        </div>
        <div class="profile">
            👤 <?= htmlspecialchars($_SESSION['nama']) ?>
        </div>
    </div>

    <?php if(isset($_GET['berhasil'])): ?>
        <div style="background:#dcfce7;color:#166534;padding:14px;border-radius:10px;margin-bottom:20px">
            Pengajuan peminjaman berhasil dikirim!
        </div>
    <?php endif; ?>

    <div class="panel">
        <h3>Daftar Riwayat</h3>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Alat</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

            <?php if($riwayat->num_rows > 0): ?>
                <?php $no=1; while($row=$riwayat->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_alat']) ?></td>
                    <td><?= (int)$row['jumlah'] ?></td>
                    <td><?= htmlspecialchars($row['tanggal_pinjam']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_kembali']) ?></td>
                    <td>
                        <span class="status">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Belum ada riwayat peminjaman.</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>
</body>
</html>
