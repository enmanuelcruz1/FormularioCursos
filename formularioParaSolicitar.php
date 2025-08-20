<?php
// formularioParaSolicitar.php - Con manejo de sesiones
session_start(); // Iniciar sesión para obtener mensajes

// Obtener curso solicitado
$cursoSolicitado = '';

// Prioridad 1: desde sesión (viene de registrar.php)
if (isset($_SESSION['curso_solicitado'])) {
    $cursoSolicitado = $_SESSION['curso_solicitado'];
    //unset($_SESSION['curso_solicitado']); // Limpiar después de usar
}
// Prioridad 2: desde POST (viene de index.php)
elseif (isset($_POST['cursoSolicitado']) && !empty($_POST['cursoSolicitado'])) {
    $cursoSolicitado = htmlspecialchars($_POST['cursoSolicitado'], ENT_QUOTES, 'UTF-8');
    $_SESSION['curso_solicitado'] = $cursoSolicitado;
}
// Si no hay curso, redirigir al inicio

// Obtener mensajes de la sesión
$error = '';
$success = '';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Limpiar después de mostrar
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Limpiar después de mostrar
}

// Obtener datos conservados del formulario
$datosConservados = [
    'cedula' => '',
    'nombres' => '',
    'apellidos' => '',
    'sexo' => ''
];

if (isset($_SESSION['datos_formulario'])) {
    $datos = $_SESSION['datos_formulario'];
    $datosConservados = [
        'cedula' => htmlspecialchars($datos['cedula'] ?? '', ENT_QUOTES, 'UTF-8'),
        'nombres' => htmlspecialchars($datos['nombres'] ?? '', ENT_QUOTES, 'UTF-8'),
        'apellidos' => htmlspecialchars($datos['apellidos'] ?? '', ENT_QUOTES, 'UTF-8'),
        'sexo' => htmlspecialchars($datos['sexo'] ?? '', ENT_QUOTES, 'UTF-8')
    ];
    unset($_SESSION['datos_formulario']); // Limpiar después de usar
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="index.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <title>Formulario de solicitud</title>
    <style>
        /* Estilos para los mensajes */
        .message {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.4s ease-out;
            font-size: 16px;
            line-height: 1.4;
        }

        .error-message {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 5px solid #dc3545;
        }

        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #b8dabd 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
            border-left: 5px solid #28a745;
        }

        .message-icon {
            font-size: 18px;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Animaciones */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-15px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Efecto hover para que el usuario sepa que puede interactuar */
        .message:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        /* Estilo para el botón de regreso */
        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }

        .back-button:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .message {
                padding: 12px 15px;
                font-size: 14px;
                margin: 15px 0;
            }

            .message-icon {
                font-size: 16px;
                margin-right: 8px;
            }
        }
    </style>
</head>

<body>
    <?php
    require_once "header.php";
    require_once "navbar.php";
    ?>
    <div class="registro-container">
        <h2>Registro de Usuario</h2>
        <p><strong>Curso seleccionado:</strong> <?php echo $cursoSolicitado ?></p>

        <?php
        // Mostrar mensaje de error si existe
        if (!empty($error)): ?>
            <div class="message error-message">
                <span class="message-icon">❌</span>
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php
        // Mostrar mensaje de éxito si existe
        if (!empty($success)): ?>
            <div class="message success-message">
                <span class="message-icon">✅</span>
                <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php
        // Solo mostrar el formulario si no hay mensaje de éxito
        if (empty($success)): ?>
            <form action="registrar.php" method="POST">
                <fieldset>
                    <div>
                        <label for="cedula">Cédula:
                            <input type="text" id="cedula" name="cedula" minlength="11" maxlength="11" pattern="\d{11}"
                                placeholder="Cedula sin guiones" value="<?php echo $datosConservados['cedula']; ?>"
                                required />
                        </label>
                    </div>
                    <div>
                        <label for="nombres">Nombre:
                            <input placeholder="Si tiene 2 nombres ingreselos los 2" type="text" id="nombres" name="nombres"
                                value="<?php echo $datosConservados['nombres']; ?>" required />
                        </label>
                    </div>
                    <div>
                        <label for="apellidos">Apellido:
                            <input placeholder="Si tiene 2 apellidos ingreselos los 2" type="text" id="apellidos"
                                name="apellidos" value="<?php echo $datosConservados['apellidos']; ?>" required />
                        </label>
                    </div>
                    <div>
                        <label for="sexo">Sexo:
                            <select id="sexo" name="sexo" required>
                                <option value="">Seleccione</option>
                                <option value="M" <?php echo ($datosConservados['sexo'] == 'M') ? 'selected' : ''; ?>>
                                    Masculino</option>
                                <option value="F" <?php echo ($datosConservados['sexo'] == 'F') ? 'selected' : ''; ?>>Femenino
                                </option>
                            </select>
                        </label>
                    </div>
                    <input type="hidden" name="cursoSolicitado"
                        value="<?= htmlspecialchars($cursoSolicitado, ENT_QUOTES, 'UTF-8') ?>">
                </fieldset>
                <button type="submit" name="submit">Enviar información</button>
            </form>
        <?php else: ?>
            <!-- Mostrar botón para regresar o inscribirse a otro curso -->
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" class="back-button" id="reset">
                    Regresar al inicio
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
    require_once "footer.php";
    ?>

    <?php
    unset($_SESSION['curso_solicitado']);
    ?>

</body>

</html>