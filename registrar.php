<?php
//Repasar logica matematica

//Conexion a base de datos
session_start(); // Iniciar sesión para manejar mensajes

include "dbConexion.php"; //Se puede usar include o require

// registrar.php - Código refactorizado

if (!isset($_POST['submit'])) {
    header("Location: formularioParaSolicitar.php");
    exit;
}

try {
    // 1. Obtener y validar curso solicitado PRIMERO
    $cursoSolicitado = trim($_POST['cursoSolicitado'] ?? '');

    if (empty($cursoSolicitado)) {
        $_SESSION['error'] = "No se ha seleccionado ningún curso.";
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 1.5 Verificar que el curso exista en la tabla cursos
    if (!verificarCursoExiste($dbConexion, $cursoSolicitado)) {
        $_SESSION['error'] = "El curso seleccionado no existe en nuestros registros.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 2. Verificar cupos disponibles
    if (!verificacionDeCuposDisponibles($dbConexion, $cursoSolicitado)) {
        $_SESSION['error'] = "Lo sentimos, el curso '{$cursoSolicitado}' ha alcanzado su límite de 100 estudiantes.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 3. Validar y sanitizar cédula
    $cedula = trim($_POST['cedula'] ?? '');

    if (!ctype_digit($cedula) || strlen($cedula) !== 11) {
        $_SESSION['error'] = "Cédula inválida. Debe contener exactamente 11 dígitos.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        $_SESSION['datos_formulario'] = $_POST; // Conservar datos
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 4. Verificar existencia de cédula
    if (!verificarCedulaExiste($dbConexion, $cedula)) {
        $_SESSION['error'] = "Su cédula no existe en nuestros registros.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        $_SESSION['datos_formulario'] = $_POST;
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    //4.5. Verificar que la cedula le pertenezca a esa persona
    if (!verificarCedulaCorrespondeNombresApellidos($dbConexion, $cedula)) {
        $_SESSION['error'] = "Los datos no coinciden con los registros.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        $_SESSION['datos_formulario'] = $_POST;
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 4.6 Verificar si la cédula ya está registrada en este curso
    if (verificarCedulaEnCurso($dbConexion, $cedula, $cursoSolicitado)) {
        $_SESSION['error'] = "Esta cédula ya está registrada en el curso seleccionado.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        $_SESSION['datos_formulario'] = $_POST;
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 5. Procesar datos del formulario
    $datosFormulario = sanitizarDatos($_POST);

    // 6. Verificar cupos nuevamente (doble verificación)
    if (!verificacionDeCuposDisponibles($dbConexion, $cursoSolicitado)) {
        $_SESSION['error'] = "Lo sentimos, el curso se llenó mientras procesábamos su solicitud.";
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
        $_SESSION['datos_formulario'] = $_POST;
        header("Location: formularioParaSolicitar.php");
        exit;
    }

    // 7. Registrar solicitante
    registrarSolicitante($dbConexion, $datosFormulario);

    $_SESSION['success'] = "¡Registro exitoso! Se ha inscrito correctamente al curso: {$cursoSolicitado}";
    $_SESSION['curso_solicitado'] = $cursoSolicitado;
    header("Location: formularioParaSolicitar.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    if (!empty($cursoSolicitado)) {
        $_SESSION['curso_solicitado'] = $cursoSolicitado;
    }
    if (isset($_POST)) {
        $_SESSION['datos_formulario'] = $_POST;
    }
    header("Location: formularioParaSolicitar.php");
    exit;
} finally {
    if (isset($dbConexion)) {
        $dbConexion->close();
    }
}

// ========== FUNCIONES AUXILIARES CORREGIDAS ==========

/**
 * Verifica si hay cupos disponibles para un curso específico
 * @param object $conexion - Conexión a la base de datos  
 * @param string $cursoSolicitado - Código del curso
 * @return bool - true si hay cupos disponibles, false si está lleno
 */

function verificarCursoExiste($conexion, $cursoCodigo)
{
    $query = "SELECT 1 FROM cursos WHERE nombreDeCurso = ? LIMIT 1";
    $stmt = $conexion->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparando consulta de verificación de curso.");
    }

    $stmt->bind_param("s", $cursoCodigo);
    $stmt->execute();
    $result = $stmt->get_result();
    $existe = $result->num_rows > 0;

    $stmt->close();
    return $existe;
}

function verificacionDeCuposDisponibles($conexion, $cursoSolicitado)
{
    // Preparar consulta con placeholder correcto
    $queryDeConteo = "SELECT COUNT(*) as total FROM solicitantes WHERE curso_solicitado = ?";
    $stmt = $conexion->prepare($queryDeConteo);

    if (!$stmt) {
        throw new Exception("Error preparando consulta de verificación de cupos.");
    }

    // Vincular parámetro y ejecutar
    $stmt->bind_param("s", $cursoSolicitado);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $totalInscritos = (int) $fila['total'];

    $stmt->close();

    // Retornar true si hay cupos disponibles (menos de 101)
    return $totalInscritos < 2;
}

/**
 * Verifica si una cédula existe en la base de datos
 */
function verificarCedulaExiste($conexion, $cedula)
{
    $query = "SELECT 1 FROM cedulas WHERE cedula = ? LIMIT 1";
    $stmt = $conexion->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparando consulta de verificación de cédula.");
    }

    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    $existe = $result->num_rows > 0;

    $stmt->close();
    return $existe;
}

function verificarCedulaCorrespondeNombresApellidos($conexion, $cedula)
{
    if ($conexion->connect_errno) {
        die("Connection failed: " . $conexion->connect_error);
    }

    // 1) Traer todo con una sola consulta y usando prepared statements
    $sql = "SELECT
                LOWER(TRIM(nombre))  AS nombre,
                LOWER(TRIM(nombre2)) AS nombre2,
                LOWER(TRIM(apellido))  AS apellido,
                LOWER(TRIM(apellido2)) AS apellido2
            FROM cedulas
            WHERE cedula = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conexion->error);
    }
    $stmt->bind_param("s", $cedula);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if (!$row) {
        throw new Exception("Cédula no encontrada en los registros.");
    }

    // 2) Normalizar INPUT del usuario
    $nombresInput = isset($_POST["nombres"])
        ? array_values(array_filter(preg_split('/\s+/', strtolower(trim($_POST["nombres"])))))
        : [];
    $apellidosInput = isset($_POST["apellidos"])
        ? array_values(array_filter(preg_split('/\s+/', strtolower(trim($_POST["apellidos"])))))
        : [];

    // 3) Convertir el resultado de la DB (asociativo) a arreglos INDEXADOS
    $dbNombres = array_values(array_filter([$row['nombre'] ?? '', $row['nombre2'] ?? '']));
    $dbApellidos = array_values(array_filter([$row['apellido'] ?? '', $row['apellido2'] ?? '']));

    // 4) VALIDACIONES REQUERIDAS

    // Los nombres NO pueden estar vacíos - debe poner al menos uno
    if (empty($nombresInput)) {
        throw new Exception("Los nombres son requeridos.");
    }

    // Los apellidos NO pueden estar vacíos - debe poner al menos uno
    if (empty($apellidosInput)) {
        throw new Exception("Los apellidos son requeridos.");
    }

    //===================Función interna auxiliar===================
    function validarTokensFlexibles(array $input, array $db): bool
    {
        // Si el usuario escribió más partes de las que existen en DB, falla
        if (count($input) > count($db)) {
            return false;
        }

        // Si pone solo uno, puede ser cualquiera de los que están en DB
        if (count($input) == 1) {
            return in_array($input[0], $db);
        }

        // Si pone más de uno, deben respetar el orden de la DB
        // y deben ser consecutivos desde el inicio
        for ($i = 0; $i < count($input); $i++) {
            if ($input[$i] !== $db[$i]) {
                return false;
            }
        }
        return true;
    }

    // 5) Validar nombres y apellidos con la nueva lógica
    $okNombres = validarTokensFlexibles($nombresInput, $dbNombres);
    $okApellidos = validarTokensFlexibles($apellidosInput, $dbApellidos);

    if (!$okNombres) {
        throw new Exception("Los nombres ingresados no coinciden con los registros.");
    }

    if (!$okApellidos) {
        throw new Exception("Los apellidos ingresados no coinciden con los registros.");
    }

    // Si todo está bien, continúa con el flujo normal...
    return true;
}

function verificarCedulaEnCurso($conexion, $cedula, $cursoSolicitado)
{
    $query = "SELECT 1 FROM solicitantes WHERE cedula = ? AND curso_solicitado = ? LIMIT 1";
    $stmt = $conexion->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparando consulta de verificación de cédula en curso.");
    }

    $stmt->bind_param("ss", $cedula, $cursoSolicitado);
    $stmt->execute();
    $result = $stmt->get_result();
    $existe = $result->num_rows > 0;

    $stmt->close();
    return $existe;
}

function sanitizarNombreYApellido($campoEnStrings, $campoAVerificar)
{
    $space_count = substr_count($campoEnStrings, ' ');

    if ($space_count > 1) {
        throw new Exception("Asegurese de que solo haya un espacio para los {$campoAVerificar}.");
    }
}

/**
 * Sanitiza y valida todos los datos del formulario
 */
function sanitizarDatos($post)
{
    $campos = ['cedula', 'nombres', 'apellidos', 'sexo', 'cursoSolicitado'];
    $datos = [];

    foreach ($campos as $campo) {
        $valor = trim($post[$campo] ?? '');

        if (empty($valor)) {
            throw new Exception("El campo {$campo} es obligatorio.");
        }

        $datos[$campo] = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }

    // Solo validar sexo
    if (!in_array($datos['sexo'], ['M', 'F'])) {
        throw new Exception("Sexo debe ser M o F.");
    }

    return $datos;
}

/**
 * Registra un nuevo solicitante en la base de datos
 */
function registrarSolicitante($conexion, $datos)
{
    $query = "INSERT INTO solicitantes (cedula, nombres, apellidos, sexo, curso_solicitado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción.");
    }

    $datosCedula = strtolower($datos['cedula']);
    $datosNombres = strtolower($datos['nombres']);
    $datosApellidos = strtolower($datos['apellidos']);
    $datosSexo = strtolower($datos['sexo']);
    $datosCursoSolicitad = strtolower($datos['cursoSolicitado']);

    $stmt->bind_param(
        "sssss",
        $datosCedula,
        $datosNombres,
        $datosApellidos,
        $datosSexo,
        $datosCursoSolicitad
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar los datos: " . $stmt->error);
    }

    $stmt->close();
}
?>