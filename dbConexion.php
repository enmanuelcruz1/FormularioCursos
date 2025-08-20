<?php // database_conn.php
$hostname = 'localhost'; // hostnames
$dbUsername = 'root'; // database usernames
$dbPassword = ''; // database passwords
$dbName = 'solicitudes_cursos'; //database name



//Diferencia entre method y action
//Diferencia entre require e include?
//Actualizar base de datos con el campo cursoSocilitado y solicitantes para la tabla
//Next, como enlazar un CSS a esto.

// connection to database
$dbConexion = new mysqli($hostname, $dbUsername, $dbPassword, $dbName);

if (!$dbConexion) {
     die('Could not connect to database:' . mysqli_connect_error());
}