<?php
require_once '../sesiones.php';
requerirLogin();

$usuario_actual = obtenerUsuarioActual();
$es_admin = esAdministrador();
$mensaje = '';
$error = '';
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: propiedades.php');
    exit();
}

// Obtener la propiedad
try {
    $stmt = $conexion->prepare("SELECT * FROM propiedades WHERE id = ?");
    $stmt->execute([$id]);
    $propiedad = $stmt->fetch();
    
    if (!$propiedad) {
        $error = 'Propiedad no encontrada';
    } else {
        // Verificar permisos
        if (!$es_admin && $propiedad['agente_id'] != $_SESSION['user_id']) {
            $error = 'No tienes permisos para editar esta propiedad';
        }
    }
} catch(PDOException $e) {
    $error = 'Error al obtener la propiedad';
}

// Obtener agentes si es admin
$agentes = [];
if ($es_admin) {
    try {
        $stmt = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE privilegio = 'agente' OR privilegio = 'administrador' ORDER BY nombre");
        $stmt->execute();
        $agentes = $stmt->fetchAll();
    } catch(PDOException $e) {
        $agentes = [];
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error && $propiedad) {
    $titulo = sanitizar($_POST['titulo'] ?? '');
    $tipo = sanitizar($_POST['tipo'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $ubicacion = sanitizar($_POST['ubicacion'] ?? '');
    $descripcion_breve = sanitizar($_POST['descripcion_breve'] ?? '');
    $descripcion_larga = sanitizar($_POST['descripcion_larga'] ?? '');
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $estado = sanitizar($_POST['estado'] ?? 'activa');
    $agente_id = $es_admin && !empty($_POST['agente_id']) ? intval($_POST['agente_id']) : $propiedad['agente_id'];
    
    if (empty($titulo) || empty($tipo) || $precio <= 0 || empty($ubicacion)) {
        $error = 'Todos los campos obligatorios deben estar completos';
    } else {
        try {
            // Manejar imagen destacada
            $imagen_destacada = $propiedad['imagen_destacada'];
            if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['error'] == 0) {
                $upload_dir = '../images/';
                $file_extension = strtolower(pathinfo($_FILES['imagen_destacada']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $filename = 'property_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $upload_path)) {
                        // Eliminar imagen anterior si existe
                        if ($imagen_destacada && file_exists($upload_dir . $imagen_destacada)) {
                            unlink($upload_dir . $imagen_destacada);
                        }
                        $imagen_destacada = $filename;
                    }
                }
            }
            
            // Actualizar propiedad
            $stmt = $conexion->prepare("
                UPDATE propiedades SET 
                titulo = ?, tipo = ?, precio = ?, ubicacion = ?, descripcion_breve = ?, 
                descripcion_larga = ?, destacada = ?, estado = ?, agente_id = ?, imagen_destacada = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $titulo, $tipo, $precio, $ubicacion, $descripcion_breve, 
                $descripcion_larga, $destacada, $estado, $agente_id, $imagen_destacada, $id
            ]);
            
            $mensaje = 'Propiedad actualizada correctamente';
            header('Location: propiedades.php');
            exit();
        } catch(PDOException $e) {
            $error = 'Error al actualizar la propiedad: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Propiedad - UTH SOLUTIONS</title>
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
        
        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        
        .admin-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            width: 100%;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .admin-content {
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-header-content">
                <h1>
                    <i class="fas fa-edit"></i>
                    Editar Propiedad
                </h1>
                <a href="propiedades.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </header>
        
        <div class="admin-content">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$error && $propiedad): ?>
            <div class="form-card">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($propiedad['titulo']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo">Tipo *</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="venta" <?= $propiedad['tipo'] == 'venta' ? 'selected' : '' ?>>Venta</option>
                                <option value="alquiler" <?= $propiedad['tipo'] == 'alquiler' ? 'selected' : '' ?>>Alquiler</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="precio">Precio *</label>
                            <input type="number" id="precio" name="precio" value="<?= $propiedad['precio'] ?>" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="activa" <?= $propiedad['estado'] == 'activa' ? 'selected' : '' ?>>Activa</option>
                                <option value="vendida" <?= $propiedad['estado'] == 'vendida' ? 'selected' : '' ?>>Vendida</option>
                                <option value="alquilada" <?= $propiedad['estado'] == 'alquilada' ? 'selected' : '' ?>>Alquilada</option>
                                <option value="pausada" <?= $propiedad['estado'] == 'pausada' ? 'selected' : '' ?>>Pausada</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ubicacion">Ubicación *</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($propiedad['ubicacion']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion_breve">Descripción Breve</label>
                        <textarea id="descripcion_breve" name="descripcion_breve" placeholder="Breve descripción de la propiedad..."><?= htmlspecialchars($propiedad['descripcion_breve']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion_larga">Descripción Detallada</label>
                        <textarea id="descripcion_larga" name="descripcion_larga" placeholder="Descripción completa de la propiedad..." style="height: 150px;"><?= htmlspecialchars($propiedad['descripcion_larga']) ?></textarea>
                    </div>
                    
                    <?php if ($es_admin): ?>
                    <div class="form-group">
                        <label for="agente_id">Agente Asignado</label>
                        <select id="agente_id" name="agente_id">
                            <?php foreach ($agentes as $agente): ?>
                                <option value="<?= $agente['id'] ?>" <?= $agente['id'] == $propiedad['agente_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($agente['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="imagen_destacada">Imagen Destacada</label>
                        <input type="file" id="imagen_destacada" name="imagen_destacada" accept="image/*">
                        
                        <?php if ($propiedad['imagen_destacada']): ?>
                            <div style="margin-top: 10px;">
                                <strong>Imagen actual:</strong><br>
                                <img src="../images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>" 
                                     alt="Imagen actual" class="image-preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="destacada" name="destacada" <?= $propiedad['destacada'] ? 'checked' : '' ?>>
                            <label for="destacada">Marcar como propiedad destacada</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Actualizar Propiedad
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
