<?php
include "../includes/header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
require_once "../config/database.php";

$user = $_SESSION['user'];
$msgProfile = '';
$msgPassword = '';

// --- Update Profil (nama, email, foto) ---
if (isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $foto = $user['foto']; // default foto lama

    // Cek upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['size'] > 0) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid('profil_') . '.' . $ext;
            $upload_dir = '../uploads/profiles/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                // Hapus foto lama jika bukan default
                if (!empty($foto) && $foto !== 'default.jpg' && file_exists($upload_dir . $foto)) {
                    unlink($upload_dir . $foto);
                }
                $foto = $filename;
            } else {
                $msgProfile = "Gagal mengunggah foto. Coba lagi.";
            }
        } else {
            $msgProfile = "Format gambar harus JPG/PNG/WEBP.";
        }
    }

    // Update DB & session
    $update = mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', foto='$foto' WHERE id={$user['id']}");
    if ($update) {
        $msgProfile = "Profil berhasil diperbarui!";
        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['foto'] = $foto;
        $user = $_SESSION['user'];
    } else {
        $msgProfile = "Gagal memperbarui profil. Coba lagi.";
    }
}

// --- Ganti Password ---
if (isset($_POST['change_password'])) {
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konf_pass_baru = $_POST['konf_pass_baru'];
    // Ambil password lama dari DB
    $q = mysqli_query($conn, "SELECT password FROM users WHERE id={$user['id']}");
    $dbpass = mysqli_fetch_assoc($q)['password'];

    if (!password_verify($pass_lama, $dbpass)) {
        $msgPassword = "Password lama salah!";
    } elseif ($pass_baru !== $konf_pass_baru) {
        $msgPassword = "Konfirmasi password tidak sama!";
    } elseif (strlen($pass_baru) < 5) {
        $msgPassword = "Password baru minimal 5 karakter!";
    } else {
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        if (mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id={$user['id']}")) {
            $msgPassword = "Password berhasil diubah!";
        } else {
            $msgPassword = "Gagal mengubah password.";
        }
    }
}

// Cek apakah file foto benar-benar ada, jika tidak pakai default
$fotoFile = (!empty($user['foto']) && file_exists("../uploads/profiles/" . $user['foto']))
    ? $user['foto']
    : 'default.jpg';
?>

<div class="main-content" style="max-width: 600px; margin: 36px auto; background: var(--soft); padding: 36px 24px 32px; border-radius: 20px; box-shadow: 0 2px 18px #b3c5ec30; position:relative;">
    <h2 style="text-align:center; color: var(--primary); margin-bottom: 14px; font-size:2.1rem; font-weight:900; letter-spacing:1px;">
        Profil Saya
    </h2>
    <!-- Avatar -->
    <div style="text-align:center; margin-bottom: 10px;">
        <img src="../uploads/profiles/<?= htmlspecialchars($fotoFile) ?>"
            alt="Foto Profil"
            style="width:128px; height:128px; border-radius: 50%; object-fit: cover; border: 4px solid var(--secondary); box-shadow: 0 3px 12px #ffead7;"
        />
        <!-- Username (di bawah foto) -->
        <div style="font-weight:800; color:#2a3e81; margin-top:13px; font-size:1.13rem;">
            <?= htmlspecialchars($user['username']) ?>
        </div>
    </div>

    <!-- Form Update Profil -->
    <form method="POST" enctype="multipart/form-data" autocomplete="off" style="display:flex; flex-direction: column; gap: 16px; margin-bottom: 38px; background:#fff; border-radius:14px; box-shadow:0 1px 8px #c9dafc2a; padding:22px 18px;">
        <?php if ($msgProfile): ?>
            <div id="formAlert" style="color: var(--secondary); font-weight: 700; text-align: center; margin-bottom: 12px;">
                <?= htmlspecialchars($msgProfile) ?>
            </div>
        <?php endif; ?>

        <label for="nama" style="font-weight: 700; color: var(--primary);">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required
            style="padding: 11px 15px; font-size: 1rem; border-radius: 11px; border: 1.5px solid #b6bfe2; outline:none; background:#f7faff;">

        <label for="email" style="font-weight: 700; color: var(--primary);">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
            style="padding: 11px 15px; font-size: 1rem; border-radius: 11px; border: 1.5px solid #b6bfe2; outline:none; background:#f7faff;">

        <label for="foto" style="font-weight: 700; color: var(--primary);">Foto Profil (opsional)</label>
        <input type="file" id="foto" name="foto" accept="image/*" style="font-size: 0.98rem;">

        <button type="submit" name="update_profile" class="btn orange" style="width: 100%; margin-top: 12px;">Perbarui Profil</button>
    </form>

    <!-- Form Ganti Password -->
    <form method="POST" autocomplete="off" style="display:flex; flex-direction: column; gap: 15px; background:#fff; border-radius:14px; box-shadow:0 1px 8px #c9dafc1b; padding:22px 18px;">
        <h3 style="color: var(--primary); margin-bottom: 9px; text-align:center;">Ganti Password</h3>
        <?php if ($msgPassword): ?>
            <div id="formAlert" style="color: var(--secondary); font-weight: 700; text-align: center; margin-bottom: 8px;">
                <?= htmlspecialchars($msgPassword) ?>
            </div>
        <?php endif; ?>
        <label for="pass_lama" style="font-weight: 700; color: var(--primary);">Password Lama</label>
        <input type="password" id="pass_lama" name="pass_lama" required
            style="padding: 11px 15px; font-size: 1rem; border-radius: 11px; border: 1.5px solid #b6bfe2; outline:none; background:#f7faff;">

        <label for="pass_baru" style="font-weight: 700; color: var(--primary);">Password Baru</label>
        <input type="password" id="pass_baru" name="pass_baru" required
            style="padding: 11px 15px; font-size: 1rem; border-radius: 11px; border: 1.5px solid #b6bfe2; outline:none; background:#f7faff;">

        <label for="konf_pass_baru" style="font-weight: 700; color: var(--primary);">Konfirmasi Password Baru</label>
        <input type="password" id="konf_pass_baru" name="konf_pass_baru" required
            style="padding: 11px 15px; font-size: 1rem; border-radius: 11px; border: 1.5px solid #b6bfe2; outline:none; background:#f7faff;">

        <button type="submit" name="change_password" class="btn orange" style="width: 100%; margin-top: 8px;">Ganti Password</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
