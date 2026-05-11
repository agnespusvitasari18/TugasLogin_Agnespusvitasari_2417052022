<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['nama'] !== 'admin' || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

require 'koneksi.php';

$id    = (int) $_GET['id'];
$pesan = "";

$stmt = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nama_lama);
$stmt->fetch();
$stmt->close();

if (empty($nama_lama)) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_baru = trim($_POST['nama']);
    $pass_baru = $_POST['password'];

    if (empty($nama_baru) || empty($pass_baru)) {
        $pesan = "Nama dan password tidak boleh kosong.";
    } elseif (strlen($pass_baru) < 6) {
        $pesan = "Password minimal 6 karakter.";
    } else {
        $hashed = password_hash($pass_baru, PASSWORD_BCRYPT);
        $stmt2  = $conn->prepare("UPDATE users SET nama = ?, password = ? WHERE id = ?");
        $stmt2->bind_param("ssi", $nama_baru, $hashed, $id);

        if ($stmt2->execute()) {
            $stmt2->close();
            header("Location: dashboard.php");
            exit();
        } else {
            $pesan = "Gagal update: " . $stmt2->error;
            $stmt2->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Pengguna</title>
</head>
<body>
    <h2>Edit Data Pengguna</h2>

    <?php if ($pesan !== ""): ?>
        <p style="color:red;"><?php echo $pesan; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Nama Pengguna:</label><br>
        <input type="text" name="nama"
               value="<?php echo htmlspecialchars($nama_lama); ?>" required><br><br>

        <label>Password Baru:</label><br>
        <input type="password" name="password"
               placeholder="Masukkan password baru" required><br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <br>
    <a href="dashboard.php"><button>Batal</button></a>
</body>
</html>