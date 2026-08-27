<?php
// =========================================================
// API DE SUGERENCIAS - Instituto F.D.R.
// Devuelve datos en JSON para llenar los selects.
//
// Uso:
//   sugerencias_api.php?action=carreras
//   sugerencias_api.php?action=grados&carrera=1
//   sugerencias_api.php?action=secciones
//   sugerencias_api.php?action=listar
// =========================================================

header('Content-Type: application/json; charset=utf-8');

// --- Conexión (lee el .env y conecta con SSL hacia Aiven) ---
require_once __DIR__ . '/config.php';
$conn = db_connect();

if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'carreras':
        $result = $conn->query("SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Limpieza: quitar prefijo Bachillerato/Bach. y normalizar
            $n = preg_replace('/^Bachillerato en\s*/i', '', $row['nombre_carrera']);
            $n = preg_replace('/^Bach\.\s*/i', '', $n);
            $n = trim($n);
            if (preg_match('/maritimo portuario/i', $n)) $n = 'Marítimo Portuario';
            if (strtolower($n) === 'administración de empresas') $n = 'Administración de Empresas';
            $row['nombre_carrera'] = $n;
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    case 'grados':
        $carrera = (int)($_GET['carrera'] ?? 0);
        if ($carrera <= 0) {
            echo json_encode([]);
            break;
        }
        $stmt = $conn->prepare("SELECT id_grado, nombre_grado FROM grados WHERE id_carrera = ? ORDER BY id_grado");
        $stmt->bind_param('i', $carrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        $stmt->close();
        break;

    case 'secciones':
        $result = $conn->query("SELECT id_seccion, nombre_seccion FROM secciones ORDER BY nombre_seccion");
        $map = ['A' => '1', 'B' => '2', 'C' => '3'];
        $data = [];
        while ($row = $result->fetch_assoc()) {
            if (isset($map[$row['nombre_seccion']])) $row['nombre_seccion'] = $map[$row['nombre_seccion']];
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    case 'listar':
        $sql = "SELECT s.id_sugerencia, s.nombre_remitente, s.texto_sugerencia, s.fecha_creacion,
                       c.nombre_carrera, g.nombre_grado, sec.nombre_seccion
                FROM sugerencias s
                INNER JOIN carreras c ON c.id_carrera = s.id_carrera
                INNER JOIN grados   g ON g.id_grado   = s.id_grado
                INNER JOIN secciones sec ON sec.id_seccion = s.id_seccion
                ORDER BY s.fecha_creacion DESC";
        $result = $conn->query($sql);
        $mapSec = ['A' => '1', 'B' => '2', 'C' => '3'];
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $n = preg_replace('/^Bachillerato en\s*/i', '', $row['nombre_carrera']);
            $n = preg_replace('/^Bach\.\s*/i', '', $n);
            $n = trim($n);
            if (preg_match('/maritimo portuario/i', $n)) $n = 'Marítimo Portuario';
            if (strtolower($n) === 'administración de empresas') $n = 'Administración de Empresas';
            $row['nombre_carrera'] = $n;
            if (isset($mapSec[$row['nombre_seccion']])) $row['nombre_seccion'] = $mapSec[$row['nombre_seccion']];
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
}

$conn->close();