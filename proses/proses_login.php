<?php
session_start();
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Cek status user aktif/nonaktif
        if (isset($user['status']) && $user['status'] === 'nonaktif') {
            header("Location: ../pages/login.php?msg=" . urlencode("Akun Anda dinonaktifkan. Silakan hubungi admin."));
            exit;
        }

        // Cek password
        if (password_verify($password, $user['password'])) {
            // Simpan data user ke session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'foto' => !empty($user['foto']) ? $user['foto'] : 'default.jpg',
                'status' => $user['status']
            ];

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: /dashboard_admin.php");
            } else {
                header("Location: ../pages/dashboard_user.php");
            }
            exit;
        } else {
            header("Location: ../pages/login.php?msg=" . urlencode("Password salah"));
            exit;
        }
    } else {
        header("Location: ../pages/login.php?msg=" . urlencode("User tidak ditemukan"));
        exit;
    }
} else {
    header("Location: ../pages/login.php");
    exit;
}
