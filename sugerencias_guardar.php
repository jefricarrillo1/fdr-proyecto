<?php
// =========================================================
// GUARDAR SUGERENCIA - Instituto F.D.R.
// Recibe los datos del formulario y los inserta en la
// tabla "sugerencias" de MySQL.
// =========================================================

// --- Conexión (lee el .env y conecta con SSL hacia Aiven) ---
require_once __DIR__ . '/config.php';

$nombre        = trim($_POST['nombre'] ?? '');
$id_carrera    = (int)($_POST['carrera'] ?? 0);
$id_grado      = (int)($_POST['grado'] ?? 0);
$id_seccion    = (int)($_POST['seccion'] ?? 0);
$texto         = trim($_POST['texto'] ?? '');

$exito    = false;
$error_msg = "";
$carreraNombre = $gradoNombre = $seccionNombre = "";

if ($nombre === '' || $id_carrera <= 0 || $id_grado <= 0 || $id_seccion <= 0 || $texto === '') {
    $error_msg = "Todos los campos son obligatorios.";
} else {
    $conn = db_connect();

    if ($conn->connect_errno) {
        $error_msg = "Error de conexión con MySQL: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("INSERT INTO sugerencias (nombre_remitente, id_carrera, id_grado, id_seccion, texto_sugerencia) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $error_msg = "Error al preparar la consulta: " . $conn->error;
        } else {
            $stmt->bind_param("siiis", $nombre, $id_carrera, $id_grado, $id_seccion, $texto);
            if ($stmt->execute()) {
                $exito = true;
            } else {
                $error_msg = "Error al guardar la sugerencia: " . $stmt->error;
            }
            $stmt->close();
        }

        if ($exito) {
            // Obtener los nombres para mostrar en la respuesta
            $c = $conn->query("SELECT nombre_carrera FROM carreras WHERE id_carrera = $id_carrera");
            $g = $conn->query("SELECT nombre_grado FROM grados WHERE id_grado = $id_grado");
            $s = $conn->query("SELECT nombre_seccion FROM secciones WHERE id_seccion = $id_seccion");
            if ($c && $r = $c->fetch_assoc()) {
                $carreraNombre = preg_replace('/^Bachillerato en\s*/i', '', $r['nombre_carrera']);
                $carreraNombre = preg_replace('/^Bach\.\s*/i', '', $carreraNombre);
                $carreraNombre = trim($carreraNombre);
                if (preg_match('/maritimo portuario/i', $carreraNombre)) $carreraNombre = 'Marítimo Portuario';
                if (strtolower($carreraNombre) === 'administración de empresas') $carreraNombre = 'Administración de Empresas';
            }
            if ($g && $r = $g->fetch_assoc()) $gradoNombre = $r['nombre_grado'];
            if ($s && $r = $s->fetch_assoc()) {
                $seccionNombre = $r['nombre_seccion'];
                $mapSec = ['A' => '1', 'B' => '2', 'C' => '3'];
                if (isset($mapSec[$seccionNombre])) $seccionNombre = $mapSec[$seccionNombre];
            }
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
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?><br>
                        <strong>Carrera:</strong> <?php echo htmlspecialchars($carreraNombre); ?><br>
                        <strong>Grado:</strong> <?php echo htmlspecialchars($gradoNombre); ?><br>
                        <strong>Sección:</strong> <?php echo htmlspecialchars($seccionNombre); ?>
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