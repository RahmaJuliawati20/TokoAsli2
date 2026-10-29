<?php

/*
|--------------------------------------------------------------------------
| PENGATURAN SESSION
|--------------------------------------------------------------------------
*/

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();


/*
|--------------------------------------------------------------------------
| KONEKSI DATABASE
|--------------------------------------------------------------------------
*/

include "config/koneksi.php";


/*
|--------------------------------------------------------------------------
| JIKA SUDAH LOGIN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['id_user'])) {

    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token']) ||
    $_SESSION['csrf_token'] === ''
) {

    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));
}


$error = "";

$username = "";


/*
|--------------------------------------------------------------------------
| PROSES LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |----------------------------------------------------------------------
    | Ambil input
    |----------------------------------------------------------------------
    */

    $username =
        trim($_POST['username'] ?? '');

    $password =
        $_POST['password'] ?? '';

    $csrf_token =
        $_POST['csrf_token'] ?? '';


    /*
    |----------------------------------------------------------------------
    | Validasi CSRF
    |----------------------------------------------------------------------
    */

    if (
        !is_string($csrf_token) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $csrf_token
        )
    ) {

        $error =
            "Permintaan tidak valid. Silakan coba lagi.";

    }


    /*
    |----------------------------------------------------------------------
    | Validasi username dan password
    |----------------------------------------------------------------------
    */

    elseif ($username === '' || $password === '') {

        $error =
            "Username dan password wajib diisi.";

    }


    /*
    |----------------------------------------------------------------------
    | Validasi panjang username
    |----------------------------------------------------------------------
    */

    elseif (strlen($username) > 50) {

        $error =
            "Username atau password salah.";

    }


    /*
    |----------------------------------------------------------------------
    | PROSES PENCARIAN USER
    |----------------------------------------------------------------------
    */

    else {

        $sql = "
            SELECT
                id_user,
                username,
                password,
                nama_lengkap,
                level
            FROM users
            WHERE username = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($conn, $sql);


        /*
        |------------------------------------------------------------------
        | Jika prepared statement gagal
        |------------------------------------------------------------------
        */

        if (!$stmt) {

            $error =
                "Terjadi kesalahan pada sistem.";

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $username
            );


            mysqli_stmt_execute($stmt);


            $result =
                mysqli_stmt_get_result($stmt);


            /*
            |--------------------------------------------------------------
            | Ambil data user
            |--------------------------------------------------------------
            */

            $user =
                mysqli_fetch_assoc($result);


            /*
            |--------------------------------------------------------------
            | Verifikasi password
            |--------------------------------------------------------------
            */

            if (
                $user &&
                password_verify(
                    $password,
                    $user['password']
                )
            ) {

                /*
                |----------------------------------------------------------
                | Regenerasi session ID
                |----------------------------------------------------------
                */

                session_regenerate_id(true);


                /*
                |----------------------------------------------------------
                | Simpan data user ke session
                |----------------------------------------------------------
                */

                $_SESSION['id_user'] =
                    (int) $user['id_user'];

                $_SESSION['username'] =
                    $user['username'];

                $_SESSION['nama_lengkap'] =
                    $user['nama_lengkap'];

                $_SESSION['level'] =
                    $user['level'];


                /*
                |----------------------------------------------------------
                | Buat CSRF token baru setelah login
                |----------------------------------------------------------
                */

                $_SESSION['csrf_token'] =
                    bin2hex(random_bytes(32));


                /*
                |----------------------------------------------------------
                | Redirect ke dashboard
                |----------------------------------------------------------
                */

                header("Location: index.php");
                exit;

            } else {

                /*
                |----------------------------------------------------------
                | Pesan dibuat sama untuk username/password salah
                |----------------------------------------------------------
                */

                $error =
                    "Username atau password salah.";
            }


            mysqli_stmt_close($stmt);
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
        Login - Toko Bangunan Asli 2
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #172033,
                    #263650
                );

            padding: 20px;
        }


        .login-container {

            width: 100%;

            max-width: 420px;
        }


        .login-card {

            background: #ffffff;

            border-radius: 18px;

            padding: 40px;

            box-shadow:
                0 20px 60px
                rgba(0, 0, 0, 0.25);
        }


        .logo {

            width: 68px;

            height: 68px;

            margin:
                0 auto 20px;

            border-radius: 16px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #f59e0b;

            color: #ffffff;

            font-size: 21px;

            font-weight: 800;
        }


        .login-title {

            text-align: center;

            color: #172033;

            font-size: 24px;

            margin-bottom: 7px;
        }


        .login-subtitle {

            text-align: center;

            color: #6b7280;

            font-size: 13px;

            margin-bottom: 28px;
        }


        .error {

            background: #fee2e2;

            border:
                1px solid #fecaca;

            color: #b91c1c;

            padding: 11px 13px;

            border-radius: 8px;

            font-size: 13px;

            margin-bottom: 18px;
        }


        .form-group {

            margin-bottom: 18px;
        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            font-size: 13px;

            font-weight: 600;

            color: #374151;
        }


        .form-group input {

            width: 100%;

            padding: 12px 14px;

            border:
                1px solid #d1d5db;

            border-radius: 8px;

            font-size: 14px;

            outline: none;

            transition: 0.2s;
        }


        .form-group input:focus {

            border-color: #f59e0b;

            box-shadow:
                0 0 0 3px
                rgba(245, 158, 11, 0.12);
        }


        .btn-login {

            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 8px;

            background: #f59e0b;

            color: white;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s;
        }


        .btn-login:hover {

            background: #d97706;
        }


        .login-footer {

            text-align: center;

            margin-top: 22px;

            color: #9ca3af;

            font-size: 12px;
        }

    </style>

</head>


<body>


<div class="login-container">

    <div class="login-card">


        <div class="logo">
            A2
        </div>


        <h1 class="login-title">
            Toko Bangunan Asli 2
        </h1>


        <p class="login-subtitle">
            Sistem Informasi Pembelian Barang
        </p>


        <?php if ($error !== '') { ?>

            <div
                class="error"
                role="alert"
            >

                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>

        <?php } ?>


        <form
            method="POST"
            autocomplete="on"
        >


            <!-- CSRF TOKEN -->

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >


            <div class="form-group">

                <label for="username">
                    Username
                </label>


                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    autocomplete="username"
                    value="<?= htmlspecialchars(
                        $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    maxlength="50"
                    required
                    autofocus
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>


                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-login"
            >
                Login
            </button>


        </form>


        <div class="login-footer">

            Toko Bangunan Asli 2

        </div>


    </div>

</div>


</body>

</html>