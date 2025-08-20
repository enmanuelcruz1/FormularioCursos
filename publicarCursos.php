<?php
require "dbConexion.php";

$sqlBuscarCursos = "SELECT * FROM cursos";
$result = mysqli_query($dbConexion, $sqlBuscarCursos);

while ($row = mysqli_fetch_array($result)) {
    $nombreDelCurso = $row["nombreDeCurso"]; //cambiar en la Db a nombre curso y hacer todos los comandos para que el jefe solo tenga que ejecutar
    $imagenCurso = base64_encode($row["imagen_del_curso"]);

    echo "
    <div class='curso'>
        <div class='imagen'>
            <img src='data:image/jpg;base64, $imagenCurso'
                style='width: 100%; aspect-ratio: 16/9;' alt=''>
        </div>
        <div class='nombreCurso'>
                    <h4>$nombreDelCurso</h4>
            </div>
            <form method='POST' action='formularioParaSolicitar.php'>
            <input type='hidden' name='cursoSolicitado' value='$nombreDelCurso'>
            <button type='submit'>Solicitar curso</button>
            </form>
    </div>
    ";
}
//Mas adelante insertar una validacion de los cursos mas compleja como un switch o algo asi que almacene los cursos habilitados de la Db y los compare con el nombre del curso a solicitar



/**INSERT INTO cursos (nombreDeCurso, imagen_del_curso)
VALUES 
('Poder judicial curso', LOAD_FILE('C:\\xampp\\htdocs\\cursos\\imagenes\\istockphoto-1135995959-612x612.jpg')),
('Fundamentos del Derecho', LOAD_FILE('C:\\xampp\\htdocs\\cursos\\imagenes\\istockphoto-1135995959-612x612.jpg')),
('Introducción al Estudio de las Leyes', LOAD_FILE('C:\\xampp\\htdocs\\cursos\\imagenes\\istockphoto-1135995959-612x612.jpg')),
('Derecho y Sociedad', LOAD_FILE('C:\\xampp\\htdocs\\cursos\\imagenes\\istockphoto-1135995959-612x612.jpg')),
('Historia y Evolución del Derecho', LOAD_FILE('C:\\xampp\\htdocs\\cursos\\imagenes\\istockphoto-1135995959-612x612.jpg')); */
?>