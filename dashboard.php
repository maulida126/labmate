
<?php
require_once "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id = (int) $_SESSION['id'];

function hitungSiswa($conn, $sql) {
    $result = $conn->query($sql);
    return $result ? $result->fetch_assoc()['total'] : 0;
}

$total = hitungSiswa($conn,
    "SELECT COUNT(*) AS total FROM peminjaman WHERE user_id=$id"
);

$menunggu = hitungSiswa($conn,
    "SELECT COUNT(*) AS total FROM peminjaman WHERE user_id=$id AND status='Menunggu'"
);

$disetujui = hitungSiswa($conn,
    "SELECT COUNT(*) AS total FROM peminjaman WHERE user_id=$id AND status IN ('Disetujui','Dipinjam')"
);

$selesai = hitungSiswa($conn,
    "SELECT COUNT(*) AS total FROM peminjaman WHERE user_id=$id AND status='Selesai'"
);

$terbaru = $conn->query("
    SELECT p.*, a.nama_alat
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.user_id = $id
    ORDER BY p.id DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Siswa - LabMate</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="sidebar">

    <div class="logo">Lab<span>Mate.</span></div>

    <p class="menu-title">MENU UTAMA</p>

    <a href="dashboard.php" class="active">▦ Dashboard</a>
    <a href="alat.php">▣ Daftar Alat</a>
    <a href="peminjaman.php">＋ Ajukan Peminjaman</a>

    <p class="menu-title">LAINNYA</p>

    <a href="riwayat.php">◷ Riwayat Peminjaman</a>
    <a href="pengembalian.php">↩ Pengembalian</a>
    <a href="../logout.php" class="logout">⇥ Logout</a>

</div>

<div class="main">

    <div class="topbar">
        <div>
            <h2>Dashboard Siswa</h2>
            <p>Informasi peminjaman alat laboratorium</p>
        </div>

        <div class="profile">
            👤 <?= htmlspecialchars($_SESSION['nama']) ?>
        </div>
    </div>

    <div class="welcome">
        <h2>Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! 👋</h2>
        <p>Selamat datang di LabMate. Temukan dan pinjam alat laboratorium dengan mudah.</p>
    </div>

    <div class="stats">

        <div class="stat-card">
            <div class="icon">📋</div>
            <h3><?= $total ?></h3>
            <p>Total Peminjaman</p>
        </div>

        <div class="stat-card">
            <div class="icon">⏳</div>
            <h3><?= $menunggu ?></h3>
            <p>Menunggu</p>
        </div>

        <div class="stat-card">
            <div class="icon">📦</div>
            <h3><?= $disetujui ?></h3>
            <p>Disetujui / Dipinjam</p>
        </div>

        <div class="stat-card">
            <div class="icon">✅</div>
            <h3><?= $selesai ?></h3>
            <p>Selesai</p>
        </div>

    </div>

    <div class="panel">

        <h3>📋 Riwayat Peminjaman Terbaru</h3>

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

            <?php if ($terbaru && $terbaru->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = $terbaru->fetch_assoc()): ?>

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
                    <td colspan="6">Kamu belum memiliki peminjaman.</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
