<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset ="UTF-8">
        <title>Proiect PAI</title>
    </head>
    <body>
        <h1>Salut! Acesta este proiectul la PAI.</h1>
        <p>Aceaste este un test daca serverul Apache rulează corect.</p>

            <?php
            // Fusul orar al Romaniei
            date_default_timezone_set('Europe/Bucharest');
            // BOra si data curenta pe server
            echo "<p>Data si ora pe server este: " . date("d.m.Y H:i:s") . "</p>";
            ?>

        <p>Versiunea PHP este: <?php echo phpversion(); ?></p>
    </body>
</html>