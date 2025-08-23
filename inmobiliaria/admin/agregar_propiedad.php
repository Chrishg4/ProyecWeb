<?php
require_once '../sesiones.php';
requerirLogin();

$usuario_actual = obtenerUsuarioActual();
$es_admin = esAdministrador();
$mensaje = '';
$error = '';

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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = sanitizar($_POST['titulo'] ?? '');
    $tipo = sanitizar($_POST['tipo'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $ubicacion = sanitizar($_POST['ubicacion'] ?? '');
    $descripcion_breve = sanitizar($_POST['descripcion_breve'] ?? '');
    $descripcion_larga = sanitizar($_POST['descripcion_larga'] ?? '');
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $agente_id = $es_admin && !empty($_POST['agente_id']) ? intval($_POST['agente_id']) : $_SESSION['user_id'];
    
    if (empty($titulo) || empty($tipo) || $precio <= 0 || empty($ubicacion)) {
        $error = 'Todos los campos obligatorios deben estar completos';
    } else {
        try {
            // Manejar imagen destacada
            $imagen_destacada = '';
            if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['error'] == 0) {
                $upload_dir = '../images/';
                $file_extension = strtolower(pathinfo($_FILES['imagen_destacada']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $filename = 'property_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $upload_path)) {
                        $imagen_destacada = $filename;
                    }
                }
            }
            
            // Insertar propiedad
            $stmt = $conexion->prepare("
                INSERT INTO propiedades (titulo, tipo, precio, ubicacion, descripcion_breve, descripcion_larga, destacada, agente_id, imagen_destacada) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $titulo, $tipo, $precio, $ubicacion, $descripcion_breve, $descripcion_larga, $destacada, $agente_id, $imagen_destacada
            ]);
            
            $mensaje = 'Propiedad agregada correctamente';
            header('Location: propiedades.php');
            exit();
        } catch(PDOException $e) {
            $error = 'Error al agregar la propiedad: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Propiedad - UTH SOLUTIONS</title>
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
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        
        .btn-submit {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #45a049;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            margin-right: 15px;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
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
        
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="admin-header-content">
                <div class="admin-title">
                    <h1><i class="fas fa-plus-circle"></i> Agregar Propiedad</h1>
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
            
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="titulo">Título <span class="required">*</span></label>
                            <input type="text" id="titulo" name="titulo" required maxlength="200" 
                                   value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo <span class="required">*</span></label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="venta" <?= ($_POST['tipo'] ?? '') == 'venta' ? 'selected' : '' ?>>Venta</option>
                                <option value="alquiler" <?= ($_POST['tipo'] ?? '') == 'alquiler' ? 'selected' : '' ?>>Alquiler</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="precio">Precio <span class="required">*</span></label>
                            <input type="number" id="precio" name="precio" required min="0" step="0.01"
                                   value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="ubicacion">Ubicación <span class="required">*</span></label>
                            <input type="text" id="ubicacion" name="ubicacion" required maxlength="255"
                                   value="<?= htmlspecialchars($_POST['ubicacion'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <?php if ($es_admin && !empty($agentes)): ?>
                    <div class="form-group">
                        <label for="agente_id">Asignar a Agente</label>
                        <select id="agente_id" name="agente_id">
                            <option value="">Seleccionar agente (opcional)</option>
                            <?php foreach ($agentes as $agente): ?>
                                <option value="<?= $agente['id'] ?>" <?= ($_POST['agente_id'] ?? '') == $agente['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($agente['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="descripcion_breve">Descripción Breve</label>
                        <textarea id="descripcion_breve" name="descripcion_breve" maxlength="500"><?= htmlspecialchars($_POST['descripcion_breve'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion_larga">Descripción Completa</label>
                        <textarea id="descripcion_larga" name="descripcion_larga"><?= htmlspecialchars($_POST['descripcion_larga'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="imagen_destacada">Imagen Destacada</label>
                        <input type="file" id="imagen_destacada" name="imagen_destacada" accept="image/*">
                        <small style="color: #666; display: block; margin-top: 5px;">
                            Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 5MB
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="destacada" name="destacada" value="1" <?= ($_POST['destacada'] ?? '') ? 'checked' : '' ?>>
                            <label for="destacada">Marcar como propiedad destacada</label>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <a href="propiedades.php" class="btn-cancel">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Guardar Propiedad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
