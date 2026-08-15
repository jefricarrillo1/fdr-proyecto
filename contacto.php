<?php
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['correo'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$tipo_mensaje = $_POST['tipoMensaje'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

// Ruta directa a tu base de datos Access
$db_path = "C:\\xampp2\\htdocs\\proyecto_fdr\\bd_fdr.accdb";
$conn_string = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$db_path;";

$conn = @odbc_connect($conn_string, "", "");

$exito = false;
$error_msg = "";

if (!$conn) {
    $error_msg = "Error de conexión: " . odbc_errormsg();
} else {
    $sql = "INSERT INTO mensajeriafdr (nombre, correo, telefono, tipoMensaje, mensaje) VALUES ('$nombre', '$correo', '$telefono', '$tipo_mensaje', '$mensaje')";
    $resultado = @odbc_exec($conn, $sql);

    if ($resultado) {
        $exito = true;
    } else {
        $error_msg = "Error al guardar los datos: " . odbc_errormsg();
    }
    @odbc_close($conn);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta - Instituto F.D.R.</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <div class="page-wrapper">
        <main class="contact-card" style="text-align: center; padding: 40px;">
            
            <div class="card-info" style="width: 100%;">
                <div class="logo-container">
                    <img src="FranklinDelano.jpg" alt="Logo Instituto Franklin Delano Roosevelt" class="logo-colegio">
                </div>
                
                <?php if ($exito): ?>
                    <h2 style="color: #27ae60; margin-top: 20px;">¡Mensaje enviado con éxito!</h2>
                    <p style="margin-top: 15px; font-size: 16px;">Tus datos han sido guardados correctamente en la base de datos institucional.</p>
                <?php else: ?>
                    <h2 style="color: #c0392b; margin-top: 20px;">Ocurrió un error</h2>
                    <p style="margin-top: 15px; font-size: 16px; color: #c0392b;"><?php echo $error_msg; ?></p>
                <?php endif; ?>

                <div class="form-actions full-width" style="margin-top: 30px;">
                    <a href="contacto.html" class="submit-btn" style="text-decoration: none; display: inline-block; padding: 12px 25px;">Volver al formulario</a>
                </div>
            </div>

        </main>
    </div>

</body>
</html>