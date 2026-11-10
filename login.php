<?php
require_once "config/koneksi.php";

$error = "";

if (isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $data = $result->fetch_assoc();

        if (password_verify($password, $data['password'])) {

            session_regenerate_id(true);

            $_SESSION['id'] = $data['id'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            header("Location: index.php");
            exit;

        } else {
            $error = "Password yang kamu masukkan salah!";
        }

    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login LabMate</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: linear-gradient(135deg, #fce7f3, #ede9fe);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-container {
    width: 900px;
    max-width: 95%;
    background: white;
    border-radius: 20px;
    display: flex;
    overflow: hidden;
    box-shadow: 0 10px 35px rgba(100,50,120,.15);
}

.left {
    width: 50%;
    background: linear-gradient(135deg,#db2777,#9333ea);
    color: white;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.left h1 {
    font-size: 38px;
    margin-bottom: 15px;
}

.left p {
    line-height: 1.8;
}

.right {
    width: 50%;
    padding: 55px 40px;
}

.right h2 {
    color: #9333ea;
    margin-bottom: 10px;
}

.right p {
    color: #888;
    margin-bottom: 30px;
}

input {
    width: 100%;
    padding: 14px;
    margin: 10px 0 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    outline: none;
}

input:focus {
    border-color: #9333ea;
}

label {
    font-weight: bold;
    color: #444;
}

button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg,#db2777,#9333ea);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    opacity: .9;
}

.error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
}

@media(max-width:650px) {
    .left {
        display: none;
    }
    .right {
        width: 100%;
    }
}
</style>
</head>

<body>

<div class="login-container">

    <div class="left">
        <h1>LabMate.</h1>
        <h3>Laboratory Management System</h3>
        <br>
        <p>
            Sistem informasi peminjaman alat laboratorium
            yang mudah, cepat, dan terorganisir.
        </p>
    </div>

    <div class="right">

        <h2>Selamat Datang!</h2>
        <p>Silakan login ke akun LabMate kamu.</p>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username"
                   placeholder="Masukkan username" required>

            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Masukkan password" required>

            <button type="submit">Login Sekarang</button>

        </form>

    </div>

</div>

</body>
</html>