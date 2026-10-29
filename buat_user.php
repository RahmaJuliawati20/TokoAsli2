<?php

include "config/koneksi.php";

$pesan = "";
$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $level = $_POST['level'] ?? '';


    if (
        $username === '' ||
        $password === '' ||
        $nama_lengkap === '' ||
        $level === ''
    ) {

        $error = "Semua data wajib diisi.";

    } elseif (!in_array($level, ['admin', 'pemilik'])) {

        $error = "Level pengguna tidak valid.";

    } elseif (strlen($password) < 6) {

        $error = "Password minimal 6 karakter.";

    } else {

        $username_aman =
            mysqli_real_escape_string(
                $conn,
                $username
            );

        $nama_aman =
            mysqli_real_escape_string(
                $conn,
                $nama_lengkap
            );


        $cek = mysqli_query($conn, "
            SELECT id_user
            FROM users
            WHERE username = '$username_aman'
            LIMIT 1
        ");


        if (mysqli_num_rows($cek) > 0) {

            $error =
                "Username tersebut sudah digunakan.";

        } else {

            $password_hash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            $password_aman =
                mysqli_real_escape_string(
                    $conn,
                    $password_hash
                );


            $query = mysqli_query($conn, "
                INSERT INTO users
                (
                    username,
                    password,
                    nama_lengkap,
                    level
                )
                VALUES
                (
                    '$username_aman',
                    '$password_aman',
                    '$nama_aman',
                    '$level'
                )
            ");


            if ($query) {

                $pesan =
                    "Akun berhasil dibuat. Silakan hapus file buat_user.php setelah selesai.";

            } else {

                $error =
                    "Gagal membuat akun: "
                    . mysqli_error($conn);

            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Buat User - Toko Bangunan Asli 2
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {

            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            background: #f4f6f9;

            margin: 0;

            padding: 40px 20px;
        }

        .container {

            max-width: 500px;

            margin: auto;

            background: white;

            padding: 30px;

            border-radius: 14px;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.08);
        }

        h2 {

            margin-top: 0;

            color: #172033;
        }

        .info {

            background: #fff7ed;

            border: 1px solid #fed7aa;

            color: #9a3412;

            padding: 12px;

            border-radius: 8px;

            font-size: 13px;

            margin-bottom: 20px;
        }

        .success {

            background: #dcfce7;

            border: 1px solid #bbf7d0;

            color: #166534;

            padding: 12px;

            border-radius: 8px;

            font-size: 13px;

            margin-bottom: 20px;
        }

        .error {

            background: #fee2e2;

            border: 1px solid #fecaca;

            color: #b91c1c;

            padding: 12px;

            border-radius: 8px;

            font-size: 13px;

            margin-bottom: 20px;
        }

        label {

            display: block;

            font-size: 13px;

            font-weight: 600;

            margin-bottom: 7px;

            color: #374151;
        }

        .form-group {

            margin-bottom: 17px;
        }

        input,
        select {

            width: 100%;

            padding: 11px 13px;

            border:
                1px solid #d1d5db;

            border-radius: 8px;

            font-size: 14px;
        }

        button {

            width: 100%;

            border: none;

            background: #f59e0b;

            color: white;

            padding: 12px;

            border-radius: 8px;

            font-weight: 700;

            cursor: pointer;
        }

        button:hover {

            background: #d97706;
        }

    </style>

</head>


<body>


<div class="container">

    <h2>
        Buat Akun Pengguna
    </h2>


    <div class="info">

        File ini hanya digunakan untuk membuat
        akun awal Admin/Pemilik.

        Setelah akun berhasil dibuat,
        hapus file <b>buat_user.php</b>.

    </div>


    <?php if ($pesan !== '') { ?>

        <div class="success">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php } ?>


    <?php if ($error !== '') { ?>

        <div class="error">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php } ?>


    <form method="POST">


        <div class="form-group">

            <label>
                Username
            </label>

            <input
                type="text"
                name="username"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                name="nama_lengkap"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                minlength="6"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Level
            </label>

            <select name="level" required>

                <option value="">
                    -- Pilih Level --
                </option>

                <option value="admin">
                    Admin
                </option>

                <option value="pemilik">
                    Pemilik
                </option>

            </select>

        </div>


        <button type="submit">
            Buat Akun
        </button>


    </form>


</div>


</body>

</html>