<?php
require_once '../sesiones.php';
require_once '../conexion.php';
requerirLogin();
requerirAdmin();

$success = '';
$error = '';

// Obtener configuración actual
try {
    $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch();
} catch(PDOException $e) {
    $config = null;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_sitio = sanitizar($_POST['nombre_sitio'] ?? 'UTH SOLUTIONS REAL STATE');
    $color_esquema = $_POST['color_esquema'] ?? 'azul';
    $mensaje_banner = sanitizar($_POST['mensaje_banner'] ?? 'PERMÍTENOS AYUDARTE A CUMPLIR TUS SUEÑOS');
    $titulo_quienes_somos = sanitizar($_POST['titulo_quienes_somos'] ?? 'Quienes Somos');
    $descripcion_quienes_somos = sanitizar($_POST['descripcion_quienes_somos'] ?? '');
    $facebook_url = sanitizar($_POST['facebook_url'] ?? '');
    $youtube_url = sanitizar($_POST['youtube_url'] ?? '');
    $instagram_url = sanitizar($_POST['instagram_url'] ?? '');
    $direccion = sanitizar($_POST['direccion'] ?? '');
    $telefono_contacto = sanitizar($_POST['telefono_contacto'] ?? '');
    $email_contacto = sanitizar($_POST['email_contacto'] ?? '');
    
    // Verificar si se solicitó resetear esquema de colores
    if (isset($_POST['reset_colores'])) {
        $color_esquema = 'azul'; // Color por defecto
    }
    
    // Obtener configuración actual para mantener valores existentes
    $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
    $stmt->execute();
    $config_actual = $stmt->fetch();
    
    $logo_navbar = $config_actual ? $config_actual['logo_navbar'] : null;
    $logo_footer = $config_actual ? $config_actual['logo_footer'] : null;
    $banner_imagen = $config_actual ? $config_actual['banner_imagen'] : null;
    $imagen_quienes_somos = $config_actual ? $config_actual['imagen_quienes_somos'] : null;
    
    // Manejar subida de logo navbar
    if (isset($_FILES['logo_navbar']) && $_FILES['logo_navbar']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['logo_navbar']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['logo_navbar']['name'], PATHINFO_EXTENSION);
            $logo_navbar = 'logo_navbar_' . time() . '.' . $extension;
            $upload_path = '../uploads/logos/' . $logo_navbar;
            
            if (move_uploaded_file($_FILES['logo_navbar']['tmp_name'], $upload_path)) {
                // Eliminar logo anterior si existe
                if ($config_actual && $config_actual['logo_navbar'] && file_exists('../uploads/logos/' . $config_actual['logo_navbar'])) {
                    unlink('../uploads/logos/' . $config_actual['logo_navbar']);
                }
            } else {
                $error = 'Error al subir el logo del navbar';
            }
        } else {
            $error = 'Tipo de archivo no válido para logo navbar. Use JPG, PNG, GIF o WebP';
        }
    }
    
    // Manejar subida de logo footer
    if (isset($_FILES['logo_footer']) && $_FILES['logo_footer']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['logo_footer']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['logo_footer']['name'], PATHINFO_EXTENSION);
            $logo_footer = 'logo_footer_' . time() . '.' . $extension;
            $upload_path = '../uploads/logos/' . $logo_footer;
            
            if (move_uploaded_file($_FILES['logo_footer']['tmp_name'], $upload_path)) {
                // Eliminar logo anterior si existe
                if ($config_actual && $config_actual['logo_footer'] && file_exists('../uploads/logos/' . $config_actual['logo_footer'])) {
                    unlink('../uploads/logos/' . $config_actual['logo_footer']);
                }
            } else {
                $error = 'Error al subir el logo del footer';
            }
        } else {
            $error = 'Tipo de archivo no válido para logo footer. Use JPG, PNG, GIF o WebP';
        }
    }
    
    // Manejar subida de banner
    if (isset($_FILES['banner_imagen']) && $_FILES['banner_imagen']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['banner_imagen']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['banner_imagen']['name'], PATHINFO_EXTENSION);
            $banner_imagen = 'banner_' . time() . '.' . $extension;
            $upload_path = '../uploads/banners/' . $banner_imagen;
            
            if (move_uploaded_file($_FILES['banner_imagen']['tmp_name'], $upload_path)) {
                // Eliminar banner anterior si existe
                if ($config_actual && $config_actual['banner_imagen'] && file_exists('../uploads/banners/' . $config_actual['banner_imagen'])) {
                    unlink('../uploads/banners/' . $config_actual['banner_imagen']);
                }
            } else {
                $error = 'Error al subir la imagen del banner';
            }
        } else {
            $error = 'Tipo de archivo no válido para banner. Use JPG, PNG, GIF o WebP';
        }
    }
    
    // Manejar subida de imagen quienes somos
    if (isset($_FILES['imagen_quienes_somos']) && $_FILES['imagen_quienes_somos']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['imagen_quienes_somos']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['imagen_quienes_somos']['name'], PATHINFO_EXTENSION);
            $imagen_quienes_somos = 'quienes_somos_' . time() . '.' . $extension;
            $upload_path = '../images/' . $imagen_quienes_somos;
            
            if (move_uploaded_file($_FILES['imagen_quienes_somos']['tmp_name'], $upload_path)) {
                // Eliminar imagen anterior si existe
                if ($config_actual && $config_actual['imagen_quienes_somos'] && file_exists('../images/' . $config_actual['imagen_quienes_somos'])) {
                    unlink('../images/' . $config_actual['imagen_quienes_somos']);
                }
            } else {
                $error = 'Error al subir la imagen de quienes somos';
            }
        } else {
            $error = 'Tipo de archivo no válido para imagen de equipo. Use JPG, PNG, GIF o WebP';
        }
    }
    
    // Validaciones - Solo email si se proporciona
    if (!empty($email_contacto) && !validarEmail($email_contacto)) {
        $error = 'El email de contacto no es válido';
    }
    
    if (empty($error)) {
        try {
            // Verificar si existe la configuración
            $stmt = $conexion->prepare("SELECT id FROM configuracion_sitio WHERE id = 1");
            $stmt->execute();
            $existe = $stmt->fetch();
            
            if ($existe) {
                // Actualizar
                $stmt = $conexion->prepare("
                    UPDATE configuracion_sitio SET 
                    nombre_sitio = ?, color_esquema = ?, mensaje_banner = ?,
                    titulo_quienes_somos = ?, descripcion_quienes_somos = ?,
                    facebook_url = ?, youtube_url = ?, instagram_url = ?,
                    direccion = ?, telefono_contacto = ?, email_contacto = ?,
                    logo_navbar = ?, logo_footer = ?, banner_imagen = ?, imagen_quienes_somos = ?
                    WHERE id = 1
                ");
                $stmt->execute([
                    $nombre_sitio, $color_esquema, $mensaje_banner,
                    $titulo_quienes_somos, $descripcion_quienes_somos,
                    $facebook_url, $youtube_url, $instagram_url,
                    $direccion, $telefono_contacto, $email_contacto,
                    $logo_navbar, $logo_footer, $banner_imagen, $imagen_quienes_somos
                ]);
            } else {
                // Insertar
                $stmt = $conexion->prepare("
                    INSERT INTO configuracion_sitio 
                    (nombre_sitio, color_esquema, mensaje_banner, titulo_quienes_somos, 
                     descripcion_quienes_somos, facebook_url, youtube_url, instagram_url,
                     direccion, telefono_contacto, email_contacto, logo_navbar, logo_footer, banner_imagen, imagen_quienes_somos) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $nombre_sitio, $color_esquema, $mensaje_banner,
                    $titulo_quienes_somos, $descripcion_quienes_somos,
                    $facebook_url, $youtube_url, $instagram_url,
                    $direccion, $telefono_contacto, $email_contacto,
                    $logo_navbar, $logo_footer, $banner_imagen, $imagen_quienes_somos
                ]);
            }
            
            $success = 'Configuración actualizada correctamente';
            
            // Recargar configuración
            $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
            $stmt->execute();
            $config = $stmt->fetch();
            
        } catch(PDOException $e) {
            $error = 'Error al guardar la configuración: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar Página - UTH SOLUTIONS</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%);
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .form-section h3 {
            color: #1a237e;
            font-size: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            gap: 20px;
        }
        
        .form-grid.two-cols {
            grid-template-columns: 1fr 1fr;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1a237e;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .color-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        
        .color-option {
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .color-option.selected {
            border-color: #1a237e;
            background: #f0f4ff;
        }
        
        .color-option input {
            margin-right: 8px;
        }
        
        .color-azul {
            background: linear-gradient(135deg, #1a237e, #303f9f);
            color: white;
        }
        
        .color-amarillo {
            background: linear-gradient(135deg, #FFC107, #F57F17);
            color: #333;
        }
        
        .color-gris {
            background: linear-gradient(135deg, #607D8B, #37474F);
            color: white;
        }
        
        .color-blanco-gris {
            background: linear-gradient(135deg, #FFFFFF, #F5F5F5);
            color: #333;
            border: 2px solid #ddd;
        }
        
        .submit-btn {
            background: #1a237e;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }
        
        .submit-btn:hover {
            background: #303f9f;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fee;
            color: #d32f2f;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .file-upload-group {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s;
            background: #f9f9f9;
        }
        
        .file-upload-group:hover {
            border-color: #1a237e;
        }
        
        .file-upload-group input[type="file"] {
            display: none;
        }
        
        .file-upload-label {
            display: inline-block;
            padding: 12px 24px;
            background: #1a237e;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 10px;
        }
        
        .file-upload-label:hover {
            background: #303f9f;
        }
        
        .file-info {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
        
        .current-image {
            max-width: 150px;
            max-height: 100px;
            border-radius: 6px;
            margin-top: 10px;
            border: 2px solid #ddd;
        }
        
        .reset-btn {
            background: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
            margin-left: 10px;
        }
        
        .reset-btn:hover {
            background: #d32f2f;
        }
        
        .color-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .preview-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .preview-section h4 {
            margin-bottom: 15px;
            color: #1a237e;
        }
        
        @media (max-width: 768px) {
            .admin-header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-grid.two-cols {
                grid-template-columns: 1fr;
            }
            
            .color-preview {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-header-content">
                <div>
                    <h1>Personalizar Página</h1>
                    <p>Configura la apariencia y contenido de tu sitio web</p>
                </div>
                <a href="dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </header>
        
        <div class="admin-content">
            <div class="form-container">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                                <form method="POST" enctype="multipart/form-data" class="config-form">
                    <!-- Información General -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Información General
                        </h3>
                        
                        <div class="form-group">
                            <label for="nombre_sitio">Nombre del Sitio</label>
                            <input type="text" id="nombre_sitio" name="nombre_sitio" 
                                   value="<?= $config ? htmlspecialchars($config['nombre_sitio']) : '' ?>">
                        </div>
                    </div>
                    
                    <!-- Logos y Branding -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-image"></i>
                            Logos y Branding
                        </h3>
                        
                        <div class="form-grid two-cols">
                            <div class="form-group">
                                <label>Logo del Navbar</label>
                                <div class="file-upload-group">
                                    <label for="logo_navbar" class="file-upload-label">
                                        <i class="fas fa-upload"></i> Seleccionar Logo Navbar
                                    </label>
                                    <input type="file" id="logo_navbar" name="logo_navbar" accept="image/*">
                                    <div class="file-info">
                                        Formatos: JPG, PNG, GIF, WebP (máx. 2MB)<br>
                                        Tamaño recomendado: 90px de altura
                                    </div>
                                    <?php if ($config && $config['logo_navbar']): ?>
                                        <div style="margin-top: 10px;">
                                            <strong>Logo actual:</strong><br>
                                            <img src="../uploads/logos/<?= htmlspecialchars($config['logo_navbar']) ?>" 
                                                 alt="Logo Navbar" class="current-image">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Logo del Footer</label>
                                <div class="file-upload-group">
                                    <label for="logo_footer" class="file-upload-label">
                                        <i class="fas fa-upload"></i> Seleccionar Logo Footer
                                    </label>
                                    <input type="file" id="logo_footer" name="logo_footer" accept="image/*">
                                    <div class="file-info">
                                        Formatos: JPG, PNG, GIF, WebP (máx. 2MB)<br>
                                        Tamaño recomendado: 90px de altura
                                    </div>
                                    <?php if ($config && $config['logo_footer']): ?>
                                        <div style="margin-top: 10px;">
                                            <strong>Logo actual:</strong><br>
                                            <img src="../uploads/logos/<?= htmlspecialchars($config['logo_footer']) ?>" 
                                                 alt="Logo Footer" class="current-image">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Esquema de Colores -->
                    <div class="form-section">
                        <div class="color-section-header">
                            <h3>
                                <i class="fas fa-palette"></i>
                                Esquema de Colores
                            </h3>
                            <button type="submit" name="reset_colores" class="reset-btn" 
                                    onclick="return confirm('¿Está seguro de que desea resetear el esquema de colores al predeterminado (azul)?')">
                                <i class="fas fa-undo"></i> Resetear a Azul
                            </button>
                        </div>
                        
                        <div class="color-preview">
                            <label class="color-option color-azul <?= (!$config || $config['color_esquema'] == 'azul') ? 'selected' : '' ?>">
                                <input type="radio" name="color_esquema" value="azul" 
                                       <?= (!$config || $config['color_esquema'] == 'azul') ? 'checked' : '' ?>>
                                Azul
                            </label>
                            
                            <label class="color-option color-amarillo <?= ($config && $config['color_esquema'] == 'amarillo') ? 'selected' : '' ?>">
                                <input type="radio" name="color_esquema" value="amarillo" 
                                       <?= ($config && $config['color_esquema'] == 'amarillo') ? 'checked' : '' ?>>
                                Amarillo
                            </label>
                            
                            <label class="color-option color-gris <?= ($config && $config['color_esquema'] == 'gris') ? 'selected' : '' ?>">
                                <input type="radio" name="color_esquema" value="gris" 
                                       <?= ($config && $config['color_esquema'] == 'gris') ? 'checked' : '' ?>>
                                Gris
                            </label>
                            
                            <label class="color-option color-blanco-gris <?= ($config && $config['color_esquema'] == 'blanco_gris') ? 'selected' : '' ?>">
                                <input type="radio" name="color_esquema" value="blanco_gris" 
                                       <?= ($config && $config['color_esquema'] == 'blanco_gris') ? 'checked' : '' ?>>
                                Blanco y Gris
                            </label>
                        </div>
                    </div>
                    
                    <!-- Banner Principal -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-image"></i>
                            Banner Principal
                        </h3>
                        
                        <div class="form-group">
                            <label for="mensaje_banner">Mensaje del Banner</label>
                            <input type="text" id="mensaje_banner" name="mensaje_banner" 
                                   value="<?= $config ? htmlspecialchars($config['mensaje_banner']) : '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Imagen de Fondo del Banner (Opcional)</label>
                            <div class="file-upload-group">
                                <label for="banner_imagen" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Seleccionar Imagen de Banner
                                </label>
                                <input type="file" id="banner_imagen" name="banner_imagen" accept="image/*">
                                <div class="file-info">
                                    Formatos: JPG, PNG, GIF, WebP (máx. 5MB)<br>
                                    Tamaño recomendado: 1920x600px o similar
                                </div>
                                <?php if ($config && $config['banner_imagen']): ?>
                                    <div style="margin-top: 10px;">
                                        <strong>Imagen actual:</strong><br>
                                        <img src="../uploads/banners/<?= htmlspecialchars($config['banner_imagen']) ?>" 
                                             alt="Banner" class="current-image" style="max-width: 300px; max-height: 150px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección Quienes Somos -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-users"></i>
                            Sección "Quienes Somos"
                        </h3>
                        
                        <div class="form-group">
                            <label for="titulo_quienes_somos">Título de la Sección</label>
                            <input type="text" id="titulo_quienes_somos" name="titulo_quienes_somos" 
                                   value="<?= $config ? htmlspecialchars($config['titulo_quienes_somos']) : '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion_quienes_somos">Descripción</label>
                            <textarea id="descripcion_quienes_somos" name="descripcion_quienes_somos" rows="5"><?= $config ? htmlspecialchars($config['descripcion_quienes_somos']) : '' ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Imagen del Equipo (Opcional)</label>
                            <div class="file-upload-group">
                                <label for="imagen_quienes_somos" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Seleccionar Imagen del Equipo
                                </label>
                                <input type="file" id="imagen_quienes_somos" name="imagen_quienes_somos" accept="image/*">
                                <div class="file-info">
                                    Formatos: JPG, PNG, GIF, WebP (máx. 3MB)<br>
                                    Tamaño recomendado: 600x400px o similar
                                </div>
                                <?php if ($config && $config['imagen_quienes_somos']): ?>
                                    <div style="margin-top: 10px;">
                                        <strong>Imagen actual:</strong><br>
                                        <img src="../images/<?= htmlspecialchars($config['imagen_quienes_somos']) ?>" 
                                             alt="Equipo" class="current-image" style="max-width: 200px; max-height: 150px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Redes Sociales -->
                    <div class="form-section">
                        <h3>
                            <i class="fab fa-facebook"></i>
                            Redes Sociales
                        </h3>
                        
                        <div class="form-grid two-cols">
                            <div class="form-group">
                                <label for="facebook_url">URL de Facebook</label>
                                <input type="url" id="facebook_url" name="facebook_url" 
                                       value="<?= $config ? htmlspecialchars($config['facebook_url']) : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="youtube_url">URL de YouTube</label>
                                <input type="url" id="youtube_url" name="youtube_url" 
                                       value="<?= $config ? htmlspecialchars($config['youtube_url']) : '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram_url">URL de Instagram</label>
                            <input type="url" id="instagram_url" name="instagram_url" 
                                   value="<?= $config ? htmlspecialchars($config['instagram_url']) : '' ?>">
                        </div>
                    </div>
                    
                    <!-- Información de Contacto -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-phone"></i>
                            Información de Contacto
                        </h3>
                        
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="3"><?= $config ? htmlspecialchars($config['direccion']) : '' ?></textarea>
                        </div>
                        
                        <div class="form-grid two-cols">
                            <div class="form-group">
                                <label for="telefono_contacto">Teléfono</label>
                                <input type="tel" id="telefono_contacto" name="telefono_contacto" 
                                       value="<?= $config ? htmlspecialchars($config['telefono_contacto']) : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email_contacto">Email</label>
                                <input type="email" id="email_contacto" name="email_contacto" 
                                       value="<?= $config ? htmlspecialchars($config['email_contacto']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Manejar selección de colores
        document.querySelectorAll('input[name="color_esquema"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.color-option').forEach(option => {
                    option.classList.remove('selected');
                });
                this.closest('.color-option').classList.add('selected');
            });
        });
        
        // Manejar subida de archivos
        function setupFileUpload(inputId, labelId) {
            const input = document.getElementById(inputId);
            const label = document.querySelector(`label[for="${inputId}"]`);
            
            if (input && label) {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const fileName = file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                        label.innerHTML = `<i class="fas fa-check"></i> ${fileName} (${fileSize} MB)`;
                        label.style.background = '#4caf50';
                        
                        // Validar tamaño
                        const maxSize = inputId === 'banner_imagen' ? 5 : 2; // MB
                        if (file.size > maxSize * 1024 * 1024) {
                            alert(`El archivo es demasiado grande. Máximo ${maxSize}MB`);
                            this.value = '';
                            resetLabel(inputId);
                        }
                        
                        // Validar tipo
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Tipo de archivo no válido. Use JPG, PNG, GIF o WebP');
                            this.value = '';
                            resetLabel(inputId);
                        }
                    } else {
                        resetLabel(inputId);
                    }
                });
            }
        }
        
        function resetLabel(inputId) {
            const label = document.querySelector(`label[for="${inputId}"]`);
            if (label) {
                let originalText = '';
                switch(inputId) {
                    case 'logo_navbar':
                        originalText = '<i class="fas fa-upload"></i> Seleccionar Logo Navbar';
                        break;
                    case 'logo_footer':
                        originalText = '<i class="fas fa-upload"></i> Seleccionar Logo Footer';
                        break;
                    case 'banner_imagen':
                        originalText = '<i class="fas fa-upload"></i> Seleccionar Imagen de Banner';
                        break;
                    case 'imagen_quienes_somos':
                        originalText = '<i class="fas fa-upload"></i> Seleccionar Imagen del Equipo';
                        break;
                }
                label.innerHTML = originalText;
                label.style.background = '#1a237e';
            }
        }
        
        // Configurar todos los uploads
        document.addEventListener('DOMContentLoaded', function() {
            setupFileUpload('logo_navbar');
            setupFileUpload('logo_footer');
            setupFileUpload('banner_imagen');
            setupFileUpload('imagen_quienes_somos');
        });
    </script>
</body>
</html>
