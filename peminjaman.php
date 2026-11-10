
<?php
require_once "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

$pesan = "";
$error = "";
$pilihan = (int)($_GET['alat'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alat_id = (int)$_POST['alat_id'];
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $user_id = (int)$_SESSION['id'];

    $alatStmt = $conn->prepare(
        "SELECT jumlah, kondisi FROM alat WHERE id=?"
    );
    $alatStmt->bind_param("i", $alat_id);
    $alatStmt->execute();
    $dataAlat = $alatStmt->get_result()->fetch_assoc();

    if (!$dataAlat) {
        $error = "Alat tidak ditemukan.";
    } elseif ($jumlah < 1 || $jumlah > $dataAlat['jumlah']) {
        $error = "Jumlah peminjaman melebihi stok alat.";
    } elseif ($dataAlat['kondisi'] != 'Baik') {
        $error = "Alat sedang tidak dalam kondisi baik.";
    } elseif (
        !$tanggal_pinjam || !$tanggal_kembali ||
        $tanggal_pinjam < date('Y-m-d') ||
        $tanggal_kembali <= $tanggal_pinjam
    ) {
        $error = "Tanggal peminjaman atau pengembalian tidak valid.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO peminjaman
            (user_id, alat_id, jumlah, tanggal_pinjam,
             tanggal_kembali, status)
            VALUES (?, ?, ?, ?, ?, 'Menunggu')
        ");

        $stmt->bind_param(
            "iiiss",
            $user_id,
            $alat_id,
            $jumlah,
            $tanggal_pinjam,
            $tanggal_kembali
        );

        if ($stmt->execute()) {
            header("Location: riwayat.php?berhasil=1");
            exit;
        } else {
            $error = "Pengajuan gagal disimpan.";
        }
    }
}

$daftarAlat = $conn->query("
    SELECT id, nama_alat, jumlah
    FROM alat
    WHERE jumlah > 0 AND kondisi='Baik'
    ORDER BY nama_alat
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ajukan Peminjaman - LabMate</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.form-box {
    max-width:650px;
    background:white;
    padding:30px;
    border-radius:15px;
}
.form-box label {
    display:block;
    font-size:13px;
    font-weight:600;
    margin-bottom:8px;
}
.form-box input, .form-box select {
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:9px;
    margin-bottom:20px;
}
.btn-submit {
    width:100%;
    padding:13px;
    color:white;
    background:linear-gradient(90deg,#db2777,#9333ea);
    border:0;
    border-radius:9px;
    cursor:pointer;
}
.message {
    padding:12px;
    border-radius:8px;
    margin-bottom:18px;
}
.error { background:#fee2e2;color:#b91c1c; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">Lab<span>Mate.</span></div>
    <p class="menu-title">MENU UTAMA</p>
    <a href="dashboard.php">▦ Dashboard</a>
    <a href="alat.php">▣ Daftar Alat</a>
    <a href="peminjaman.php" class="active">＋ Ajukan Peminjaman</a>
    <p class="menu-title">LAINNYA</p>
    <a href="riwayat.php">◷ Riwayat Peminjaman</a>
    <a href="pengembalian.php">↩ Pengembalian</a>
    <a href="../logout.php" class="logout">⇥ Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <div>
            <h2>Ajukan Peminjaman</h2>
            <p>Isi formulir peminjaman alat laboratorium.</p>
        </div>
        <div class="profile">
            👤 <?= htmlspecialchars($_SESSION['nama']) ?>
        </div>
    </div>

    <div class="form-box">
        <h3 style="margin-bottom:25px;color:#9333ea">
            Formulir Peminjaman
        </h3>

        <?php if ($error): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Nama Siswa</label>
            <input type="text"
                value="<?= htmlspecialchars($_SESSION['nama']) ?>"
                readonly>

            <label>Pilih Alat</label>
            <select name="alat_id" required>
                <option value="">-- Pilih Alat --</option>
                <?php while($a = $daftarAlat->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>"
                        <?= $pilihan == $a['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nama_alat']) ?>
                        (Stok: <?= $a['jumlah'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Jumlah yang Dipinjam</label>
            <input type="number" name="jumlah"
                min="1" required placeholder="Masukkan jumlah">

            <label>Tanggal Peminjaman</label>
            <input type="date" name="tanggal_pinjam"
                min="<?= date('Y-m-d') ?>" required>

            <label>Rencana Tanggal Pengembalian</label>
            <input type="date" name="tanggal_kembali"
                min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>

            <button type="submit" class="btn-submit">
                Kirim Pengajuan Peminjaman
            </button>

        </form>
    </div>

</div>
</body>
</html>
