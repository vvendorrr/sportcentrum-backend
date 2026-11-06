<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Login - Sportcentrum De Linde</title>
</head>
<body>

    <h2>Login op Sportcentrum De Linde</h2>

    <form method="POST" action="login.blade.php">

        <label>Gebruikersnaam</label><br>
        <input type="text" name="gebruikersnaam" required>

        <br><br>

        <label>Wachtwoord</label><br>
        <input type="password" name="wachtwoord" required>

        <br><br>

        <input type="checkbox" name="trainer" value="1">
        <label>Ik ben een trainer</label>

        <br><br>

        <button type="submit">Login</button>

        <br><br>

        <button type="button" onclick="window.location.href='register.blade.php.php'">
            Registreer
        </button>

    </form>

</body>
</html>
