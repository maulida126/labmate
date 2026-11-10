
<?php
require_once "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id = (int)$_SESSION['id'];
$pesan = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $pinjam_id = (int)$_POST['pinjam_id'];

    $stmt = $conn->prepare("
        UPDATE peminjaman
        SET status = 'Menunggu Pengembalian'
        WHERE id = ?
        AND user_id = ?
        AND status = 'Dipinjam'
    ");

    $stmt->bind_param("ii", $pinjam_id, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $pesan = "Pengajuan pengembalian berhasil dikirim!";
    } else {
        $error = "Pengajuan gagal. Pastikan status peminjaman adalah Dipinjam.";
    }
}

$stmt = $conn->prepare("
    SELECT p.*, a.nama_alat
    FROM peminjaman p
    JOIN alat a ON p.alat_id = a.id
    WHERE p.user_id = ?
    AND p.status IN (
        'Dipinjam',
        'Menunggu Pengembalian',
        'Selesai'
    )
    ORDER BY p.id DESC
");

$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengembalian - LabMate</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.btn-return {
    background:#9333ea;
    color:white;
    border:0;
    padding:9px 13px;
    border-radius:8px;
    cursor:pointer;
    font-size:11px;
}
.btn-return:hover {
    background:#7e22ce;
}
.message {
    padding:13px;
    border-radius:9px;
    margin-bottom:20px;
}
.success {
    background:#dcfce7;
    color:#166534;
}
.error {
    background:#fee2e2;
    color:#b91c1c;
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="logo">Lab<span>Mate.</span></div>

    <p class="menu-title">MENU UTAMA</p>
    <a href="dashboard.php">▦ Dashboard</a>
    <a href="alat.php">▣ Daftar Alat</a>
    <a href="peminjaman.php">＋ Ajukan Peminjaman</a>

    <p class="menu-title">LAINNYA</p>
    <a href="riwayat.php">◷ Riwayat Peminjaman</a>
    <a href="pengembalian.php" class="active">↩ Pengembalian</a>
    <a href="../logout.php" class="logout">⇥ Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <div>
            <h2>Pengembalian Alat</h2>
            <p>Kelola pengajuan pengembalian alat laboratorium.</p>
        </div>

        <div class="profile">
            👤 <?= htmlspecialchars($_SESSION['nama']) ?>
        </div>
    </div>

    <?php if($pesan): ?>
        <div class="message success">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="message error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="panel">

        <h3>Daftar Alat yang Dipinjam</h3>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Alat</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if($data->num_rows > 0): ?>
                <?php $no=1; while($row=$data->fetch_assoc()): ?>

                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        <?= htmlspecialchars($row['nama_alat']) ?>
                    </td>

                    <td><?= (int)$row['jumlah'] ?></td>

                    <td>
                        <?= htmlspecialchars($row['tanggal_pinjam']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['tanggal_kembali']) ?>
                    </td>

                    <td>
                        <span class="status">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>

                    <td>
                        <?php if($row['status'] == 'Dipinjam'): ?>

                            <form method="POST"
                                onsubmit="return confirm('Ajukan pengembalian alat ini?')">

                                <input type="hidden" name="pinjam_id"
                                    value="<?= (int)$row['id'] ?>">

                                <button type="submit" class="btn-return">
                                    Kembalikan
                                </button>
                            </form>

                        <?php elseif($row['status'] == 'Menunggu Pengembalian'): ?>

                            <span class="status">Menunggu Petugas</span>

                        <?php else: ?>

                            <span class="status">Selesai</span>

                        <?php endif; ?>
                    </td>
                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7">
                        Belum ada alat yang sedang dipinjam.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
