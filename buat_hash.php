<?php

$password = $_POST['password'] ?? '';

if ($password !== '') {

    echo "<h3>Password Hash:</h3>";

    echo "<textarea rows='4' cols='80'>";

    echo htmlspecialchars(
        password_hash(
            $password,
            PASSWORD_DEFAULT
        )
    );

    echo "</textarea>";

} else {

?>

    <form method="POST">

        <label>
            Masukkan password:
        </label>

        <br><br>

        <input
            type="password"
            name="password"
            required
        >

        <button type="submit">
            Buat Hash
        </button>

    </form>

<?php

}

?>