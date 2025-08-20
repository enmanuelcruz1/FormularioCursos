<?php
require_once("header.php");
require_once("navbar.php");
require_once("registrar.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Document</title>
</head>

<body>
    <div class='success-container'>
        <div class='success'>¡Registro exitoso! Curso: <?= $cursoSolicitado ?>
        </div>
    </div>";
</body>

</html>

<?php
require_once("footer.php");
?>