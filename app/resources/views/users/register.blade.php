
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren - Sportcentrum De Linde</title>
</head>
<body>

    <h2>Registreer op Sportcentrum De Linde</h2>

    <form method="POST" action="register.blade.php">

        <label>Gebruikersnaam</label><br>
        <input type="text" name="gebruikersnaam" required>

        <br><br>

        <label>Wachtwoord</label><br>
        <input type="password" name="wachtwoord" required>

        <br><br>

        <label>Wachtwoord herhalen</label><br>
        <input type="password" name="wachtwoord_herhalen" required>

        <br><br>

        <label>E-Mail</label><br>
        <input type="email" name="email" required>

        <br><br>

        <label>Telefoonnummer</label><br>
        <input type="tel" name="telefoonnummer" required>

        <br><br>

        <button type="submit">Registreer</button>
        <button type="button" onclick="window.location.href='login.blade.php.php'">
            inloggen
        </button>
    </form>

</body>
</html>
