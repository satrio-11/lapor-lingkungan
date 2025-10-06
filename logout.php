<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm'])) {
        session_destroy();
        header("Location: pages/login.php?msg=" . urlencode("Anda berhasil logout."));
        exit;
    } elseif (isset($_POST['cancel'])) {
        header("Location: pages/dashboard_user.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Konfirmasi Logout - LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body, html {
            margin: 0; padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7fafc;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logout-confirmation {
            background: #fff;
            padding: 30px 40px;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(44,62,80,0.15);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .logout-confirmation h2 {
            color: #2a3e81;
            font-size: 1.8rem;
            margin-bottom: 16px;
        }
        .logout-confirmation p {
            color: #444;
            font-size: 1.1rem;
            margin-bottom: 28px;
        }
        .btn {
            padding: 12px 28px;
            font-size: 1rem;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            margin: 0 12px;
            transition: background-color 0.3s ease;
        }
        .btn.confirm {
            background-color: #ff7f21;
            color: #fff;
        }
        .btn.confirm:hover {
            background-color: #e36a00;
        }
        .btn.cancel {
            background-color: #ccc;
            color: #555;
        }
        .btn.cancel:hover {
            background-color: #bbb;
        }
        @media (max-width: 480px) {
            .logout-confirmation {
                padding: 24px 20px;
            }
            .btn {
                margin: 8px 6px;
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="logout-confirmation">
        <h2>Konfirmasi Logout</h2>
        <p>Apakah Anda yakin ingin logout dari akun?</p>
        <form method="POST" action="">
            <button type="submit" name="confirm" class="btn confirm">Logout</button>
            <button type="submit" name="cancel" class="btn cancel">Batal</button>
        </form>
    </div>
</body>
</html>
