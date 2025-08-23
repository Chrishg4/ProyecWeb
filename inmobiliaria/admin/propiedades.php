<?php
require_once '../sesiones.php';
requerirLogin();

$usuario_actual = obtenerUsuarioActual();
$es_admin = esAdministrador();
$mensaje = '';
$error = '';

// Obtener propiedades según el rol
if ($es_admin) {
    // Admin ve todas las propiedades
    $stmt = $conexion->prepare("
        SELECT p.*, u.nombre as agente_nombre 
        FROM propiedades p 
        LEFT JOIN usuarios u ON p.agente_id = u.id 
        ORDER BY p.fecha_creacion DESC
    ");
    $stmt->execute();
} else {
    // Agente solo ve sus propiedades
    $stmt = $conexion->prepare("
        SELECT p.*, u.nombre as agente_nombre 
        FROM propiedades p 
        LEFT JOIN usuarios u ON p.agente_id = u.id 
        WHERE p.agente_id = ? 
        ORDER BY p.fecha_creacion DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
}
$propiedades = $stmt->fetchAll();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion == 'eliminar') {
        $id = intval($_POST['id']);
        
        // Verificar permisos
        if (!$es_admin) {
            $stmt = $conexion->prepare("SELECT agente_id FROM propiedades WHERE id = ?");
            $stmt->execute([$id]);
            $propiedad = $stmt->fetch();
            
            if (!$propiedad || $propiedad['agente_id'] != $_SESSION['user_id']) {
                $error = 'No tienes permisos para eliminar esta propiedad';
            }
        }
        
        if (!$error) {
            try {
                // Eliminar imágenes asociadas
                $stmt = $conexion->prepare("DELETE FROM imagenes_propiedades WHERE propiedad_id = ?");
                $stmt->execute([$id]);
                
                // Eliminar propiedad
                $stmt = $conexion->prepare("DELETE FROM propiedades WHERE id = ?");
                $stmt->execute([$id]);
                
                $mensaje = 'Propiedad eliminada correctamente';
                header('Location: propiedades.php');
                exit();
            } catch(PDOException $e) {
                $error = 'Error al eliminar la propiedad';
            }
        }
    }
    
    if ($accion == 'cambiar_estado') {
        $id = intval($_POST['id']);
        $nuevo_estado = $_POST['estado'];
        
        // Verificar permisos
        if (!$es_admin) {
            $stmt = $conexion->prepare("SELECT agente_id FROM propiedades WHERE id = ?");
            $stmt->execute([$id]);
            $propiedad = $stmt->fetch();
            
            if (!$propiedad || $propiedad['agente_id'] != $_SESSION['user_id']) {
                $error = 'No tienes permisos para modificar esta propiedad';
            }
        }
        
        if (!$error) {
            try {
                $stmt = $conexion->prepare("UPDATE propiedades SET estado = ? WHERE id = ?");
                $stmt->execute([$nuevo_estado, $id]);
                $mensaje = 'Estado actualizado correctamente';
                header('Location: propiedades.php');
                exit();
            } catch(PDOException $e) {
                $error = 'Error al actualizar el estado';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Propiedades - UTH SOLUTIONS</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .admin-header {
            background: linear-gradient(135deg, <?= $es_admin ? '#1a237e' : '#4CAF50' ?> 0%, <?= $es_admin ? '#303f9f' : '#66BB6A' ?> 100%);
            color: white;
            padding: 20px 0;
        }
        
        .admin-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .btn-add {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn-add:hover {
            background: #45a049;
        }
        
        .properties-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .property-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-activa {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactiva {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-small {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #333;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-toggle {
            background: #17a2b8;
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="admin-header-content">
                <div class="admin-title">
                    <h1><i class="fas fa-building"></i> Gestión de Propiedades</h1>
                    <p><?= $es_admin ? 'Panel de Administrador' : 'Panel de Agente' ?></p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($usuario_actual['nombre'], 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <h3><?= htmlspecialchars($usuario_actual['nombre']) ?></h3>
                        <p><?= ucfirst($usuario_actual['privilegio']) ?></p>
                    </div>
                    <a href="<?= $es_admin ? 'dashboard.php' : 'agente.php' ?>" class="btn-small btn-edit">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="admin-content">
            <?php if ($mensaje): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="page-header">
                <h2>Mis Propiedades</h2>
                <a href="agregar_propiedad.php" class="btn-add">
                    <i class="fas fa-plus"></i> Agregar Propiedad
                </a>
            </div>
            
            <div class="properties-table">
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Ubicación</th>
                            <?php if ($es_admin): ?>
                                <th>Agente</th>
                            <?php endif; ?>
                            <th>Estado</th>
                            <th>Destacada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($propiedades)): ?>
                            <tr>
                                <td colspan="<?= $es_admin ? '9' : '8' ?>" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-home" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                    <p style="color: #666; font-size: 18px;">No hay propiedades registradas</p>
                                    <a href="agregar_propiedad.php" class="btn-add" style="margin-top: 15px;">
                                        <i class="fas fa-plus"></i> Agregar tu primera propiedad
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($propiedades as $propiedad): ?>
                                <tr>
                                    <td>
                                        <img src="../images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>" 
                                             alt="Propiedad" class="property-image">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($propiedad['titulo']) ?></strong>
                                        <br>
                                        <small style="color: #666;">
                                            <?= htmlspecialchars(substr($propiedad['descripcion_breve'], 0, 50)) ?>...
                                        </small>
                                    </td>
                                    <td>
                                        <span style="text-transform: uppercase; font-weight: bold; color: <?= $propiedad['tipo'] == 'venta' ? '#dc3545' : '#28a745' ?>;">
                                            <?= ucfirst($propiedad['tipo']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>$<?= number_format($propiedad['precio'], 0, '.', ',') ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($propiedad['ubicacion']) ?></td>
                                    <?php if ($es_admin): ?>
                                        <td><?= htmlspecialchars($propiedad['agente_nombre']) ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <span class="status-badge status-<?= $propiedad['estado'] ?>">
                                            <?= ucfirst($propiedad['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $propiedad['destacada'] ? '<i class="fas fa-star" style="color: #ffd700;"></i>' : '<i class="far fa-star" style="color: #ccc;"></i>' ?>
                                    </td>
                                    <td>
                                        <a href="editar_propiedad.php?id=<?= $propiedad['id'] ?>" class="btn-small btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Cambiar estado de la propiedad?')">
                                            <input type="hidden" name="accion" value="cambiar_estado">
                                            <input type="hidden" name="id" value="<?= $propiedad['id'] ?>">
                                            <input type="hidden" name="estado" value="<?= $propiedad['estado'] == 'activa' ? 'inactiva' : 'activa' ?>">
                                            <button type="submit" class="btn-small btn-toggle" title="Cambiar estado">
                                                <i class="fas fa-toggle-<?= $propiedad['estado'] == 'activa' ? 'on' : 'off' ?>"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta propiedad?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $propiedad['id'] ?>">
                                            <button type="submit" class="btn-small btn-delete" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
