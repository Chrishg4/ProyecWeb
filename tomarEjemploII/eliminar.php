<?php
require_once 'config.php';

// Verificar que se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin.php?mensaje=error');
    exit;
}

try {
    $id = (int)$_GET['id'];
    
    // Verificar que la empresa existe
    $query_check = "SELECT id, nombre FROM empresas WHERE id = ?";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $empresa = $result_check->fetch_assoc();
    
    if (!$empresa) {
        header('Location: admin.php?mensaje=error');
        exit;
    }

    // Realizar eliminación lógica (marcar como inactivo)
    // Si quieres eliminación física, cambia la consulta por: DELETE FROM empresas WHERE id = ?
    $query = "UPDATE empresas SET activo = 0 WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header('Location: admin.php?mensaje=eliminado');
    } else {
        header('Location: admin.php?mensaje=error');
    }
    
} catch (Exception $e) {
    // En caso de error, redirigir con mensaje de error
    header('Location: admin.php?mensaje=error');
}

exit;
?>
