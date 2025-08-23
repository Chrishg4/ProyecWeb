<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['accion'])) {
    header('Location: admin.php');
    exit;
}

$pdo = conectarDB();

// Determinar la acción
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

try {
    switch ($accion) {
        case 'agregar':
            agregarEmpresa($pdo);
            break;
        case 'actualizar':
            actualizarEmpresa($pdo);
            break;
        case 'eliminar':
            eliminarEmpresa($pdo);
            break;
        default:
            header('Location: admin.php?mensaje=error');
            exit;
    }
} catch (Exception $e) {
    header('Location: admin.php?mensaje=error');
    exit;
}

function agregarEmpresa($pdo) {
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $sitio_web = $_POST['sitio_web'] ?? '';
    $imagen = $_POST['imagen'] ?? 'default.jpg';

    // Validaciones básicas
    if (empty($nombre) || empty($categoria) || empty($descripcion)) {
        throw new Exception('Campos requeridos faltantes');
    }

    $sql = "INSERT INTO empresas (nombre, categoria, descripcion, telefono, email, direccion, sitio_web, imagen) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $descripcion, $telefono, $email, $direccion, $sitio_web, $imagen]);
    
    header('Location: admin.php?mensaje=agregado');
    exit;
}

function actualizarEmpresa($pdo) {
    $id = $_POST['id'] ?? 0;
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $sitio_web = $_POST['sitio_web'] ?? '';
    $imagen = $_POST['imagen'] ?? 'default.jpg';

    // Validaciones
    if (!$id || empty($nombre) || empty($categoria) || empty($descripcion)) {
        throw new Exception('Datos inválidos para actualización');
    }

    $sql = "UPDATE empresas SET 
            nombre = ?, categoria = ?, descripcion = ?, telefono = ?, 
            email = ?, direccion = ?, sitio_web = ?, imagen = ?
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $descripcion, $telefono, $email, $direccion, $sitio_web, $imagen, $id]);
    
    header('Location: admin.php?mensaje=actualizado');
    exit;
}

function eliminarEmpresa($pdo) {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        throw new Exception('ID de empresa no válido');
    }

    // Eliminación lógica (marcar como inactivo)
    $sql = "UPDATE empresas SET activo = 0 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    // Si quieres eliminación física, usa esto en su lugar:
    // $sql = "DELETE FROM empresas WHERE id = ?";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([$id]);
    
    header('Location: admin.php?mensaje=eliminado');
    exit;
}
?>
