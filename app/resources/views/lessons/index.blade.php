@extends('layout')

@section('content')


<?php
$days = ["Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag"];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Sportschool rooster</title>
</head>
<body>

    <h1>Lesrooster</h1>

    <!-- Datum kiezen -->
    <input type="date">

    <!-- Lessen filteren -->
    <select>
        <option>Alle lessen</option>
    </select>

    <button onclick="document.getElementById('lesForm').hidden = false;">
        +
    </button>

    <!-- Rooster -->
    <table border="1">
        <tr>
            <th>Tijd</th>

            <?php foreach ($dagen as $dag): ?>
                <th><?= $dag ?></th>
            <?php endforeach; ?>
        </tr>

        <?php for ($uur = 6; $uur <= 19; $uur++): ?>
            <tr>
                <td><?= sprintf("%02d:00", $uur) ?></td>

                <?php foreach ($dagen as $dag): ?>
                    <td></td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>

    </table>

</body>
</html>
