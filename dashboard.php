<?php
session_start();

if (!isset($_SESSION['nama'])) {
    header("Location: auth.php");
    exit();
}

require 'koneksi.php';

$nama_sesi = $_SESSION['nama'];
$is_admin  = ($nama_sesi === 'admin');

// Logika Hapus (hanya admin)
if ($is_admin && isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_hapus);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Selamat Datang, <?php echo htmlspecialchars($nama_sesi); ?>!</h2>
    <a href="logout.php"><button>Logout</button></a>

    <hr style="margin: 15px 0; border: none; border-top: 1px solid #ccc;">

    <?php if ($is_admin): ?>
        <h3>Menu Admin: Kelola Pengguna</h3>
        <table border="1" cellpadding="6">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
            <?php
            $result = $conn->query("SELECT id, nama FROM users ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;">
                    <button>Edit</button>
                    </a>
                    <a href="dashboard.php?hapus=<?php echo $row['id']; ?>"
                    onclick="return confirm('Yakin hapus user ini?')" style="text-decoration:none;">
                        <button>Hapus</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>