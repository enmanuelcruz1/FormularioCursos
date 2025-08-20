<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">
    <title>Cursos Disponibles - TSE</title>

</head>

<body>
    <div>
        <?php
        require_once("header.php");
        require_once("navbar.php");
        ?>

        <h2 class="cursos-titulo">Cursos ofrecidos</h2>
        <div class="cursos-container">
            <div class="curso">
                <div class="imagen">
                    <img src="./imagenes/istockphoto-1135995959-612x612.jpg" style="width: 100%; aspect-ratio: 16/9;"
                        alt="">
                </div>
                <div class="nombreCurso">
                    <h4>Influencia del poder judicial</h4>
                </div>
                <form method="POST" action="formularioParaSolicitar.php">
                    <input type="hidden" name="cursoSolicitado" value="poderJudicialCurso">
                    <button type="submit">Solicitar curso</button>
                </form>
            </div>

            <?php
            require "publicarCursos.php";
            ?>
        </div>

        <!-- CURSO 2 - CORREGIDO: Ahora usa formulario -->
        <!--<div class="curso">
            <div class="imagen">
                <img src="./imagenes/4k-Ghibli-Inspired-Lush-Green-Landscape-Under-Blue-Sky.jpg"
                    style="width: 100%; aspect-ratio: 16/9;" alt="">
            </div>
            <div class="nombreCurso">
                <h4>Influencia del poder administrativo</h4>
            </div>
            <form method="POST" action="formularioParaSolicitar.php">
                <input type="hidden" name="cursoSolicitado" value="poderAdministrativo">
                <button type="submit">Solicitar curso</button>
            </form>
        </div>-->
        <?php
        require_once("footer.php");
        ?>
    </div>
</body>

</html>