
<?php
require_once "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$keyword = trim($_GET['cari'] ?? '');

$stmt = $conn->prepare("
    SELECT * FROM alat
    WHERE nama_alat LIKE ? OR kategori LIKE ?
    ORDER BY id DESC
");

$search = "%$keyword%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$alat = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Alat - LabMate</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.search {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}
.search input {
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    flex:1;
    min-width:0;
}
.btn {
    background:#9333ea;
    color:white;
    padding:10px 15px;
    border-radius:8px;
    text-decoration:none;
    border:0;
    cursor:pointer;
    font-size:12px;
}
.btn:hover { background:#7e22ce; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">Lab<span>Mate.</span></div>
    <p class="menu-title">MENU UTAMA</p>
    <a href="dashboard.php">▦ Dashboard</a>
    <a href="alat.php" class="active">▣ Daftar Alat</a>
    <a href="peminjaman.php">＋ Ajukan Peminjaman</a>
    <p class="menu-title">LAINNYA</p>
    <a href="riwayat.php">◷ Riwayat Peminjaman</a>
    <a href="pengembalian.php">↩ Pengembalian</a>
    <a href="../logout.php" class="logout">⇥ Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <div>
            <h2>Daftar Alat Laboratorium</h2>
            <p>Lihat alat yang tersedia untuk dipinjam.</p>
        </div>
        <div class="profile">
            👤 <?= htmlspecialchars($_SESSION['nama']) ?>
        </div>
    </div>

    <div class="panel">
        <h3>Inventaris Laboratorium</h3>

        <form method="GET" class="search">
            <input type="text" name="cari"
                placeholder="Cari nama atau kategori alat..."
                value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn">Cari</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($alat->num_rows > 0): ?>
                <?php $no=1; while($row=$alat->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_alat']) ?></td>
                    <td><?= htmlspecialchars($row['kategori']) ?></td>
                    <td><?= (int)$row['jumlah'] ?></td>
                    <td><?= htmlspecialchars($row['kondisi']) ?></td>
                    <td><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                    <td>
                        <?php if ($row['jumlah'] > 0 && $row['kondisi'] == 'Baik'): ?>
                            <a class="btn" href="peminjaman.php?alat=<?= (int)$row['id'] ?>">
                                Pinjam
                            </a>
                        <?php else: ?>
                            <span class="status">Tidak tersedia</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Alat tidak ditemukan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
