<?php
// =========================================================
// CONFIGURACIÓN DE LA BASE DE DATOS MYSQL
// Ajusta estos datos según tu instalación (MySQL Workbench).
// =========================================================
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'proyecto_fdr';

// =========================================================
// RECIBIR DATOS DEL FORMULARIO
// =========================================================
$nombre      = trim($_POST['nombre'] ?? '');
$apellido    = trim($_POST['apellido'] ?? '');
$curso       = trim($_POST['curso'] ?? '');
$observacion = trim($_POST['observacion'] ?? '');

$exito    = false;
$error_msg = "";

// =========================================================
// CONEXIÓN Y GUARDADO (prepared statements)
// =========================================================
if ($nombre === '' || $apellido === '' || $curso === '' || $observacion === '') {
    $error_msg = "Todos los campos son obligatorios.";
} else {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset('utf8mb4');

    if ($conn->connect_error) {
        $error_msg = "Error de conexión con MySQL: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("INSERT INTO observaciones (nombre, apellido, curso, observacion) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $error_msg = "Error al preparar la consulta: " . $conn->error;
        } else {
            $stmt->bind_param("ssss", $nombre, $apellido, $curso, $observacion);
            if ($stmt->execute()) {
                $exito = true;
            } else {
                $error_msg = "Error al guardar la sugerencia: " . $stmt->error;
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta - Sugerencias F.D.R.</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <div class="page-wrapper">
        <main class="contact-card" style="text-align: center; padding: 40px;">

            <div class="card-info" style="width: 100%;">
                <div class="logo-container">
                    <img src="IMAGENES/FranklinDelano.jpg" alt="Logo Instituto Franklin Delano Roosevelt" class="logo-colegio">
                </div>

                <?php if ($exito): ?>
                    <h2 style="color: #27ae60; margin-top: 20px;">¡Sugerencia enviada con éxito!</h2>
                    <p style="margin-top: 15px; font-size: 16px;">Tus datos han sido guardados correctamente en la base de datos MySQL.</p>
                    <p style="margin-top: 10px; font-size: 15px;">
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?><br>
                        <strong>Curso:</strong> <?php echo htmlspecialchars($curso); ?>
                    </p>
                <?php else: ?>
                    <h2 style="color: #c0392b; margin-top: 20px;">Ocurrió un error</h2>
                    <p style="margin-top: 15px; font-size: 16px; color: #c0392b;"><?php echo htmlspecialchars($error_msg); ?></p>
                <?php endif; ?>

                <div class="form-actions full-width" style="margin-top: 30px;">
                    <a href="observaciones.html" class="submit-btn" style="text-decoration: none; display: inline-block; padding: 12px 25px;">Volver al formulario</a>
                    <a href="index.html" class="submit-btn" style="text-decoration: none; display: inline-block; padding: 12px 25px; margin-left: 10px; background: linear-gradient(135deg, #C29B38 0%, #A8842F 100%);">Ir al inicio</a>
                </div>
            </div>

        </main>
    </div>

</body>
</html>