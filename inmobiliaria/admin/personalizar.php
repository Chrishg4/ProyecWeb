<?php
require_once '../sesiones.php';
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
    $nombre_sitio = sanitizar($_POST['nombre_sitio'] ?? '');
    $color_esquema = $_POST['color_esquema'] ?? 'azul';
    $mensaje_banner = sanitizar($_POST['mensaje_banner'] ?? '');
    $titulo_quienes_somos = sanitizar($_POST['titulo_quienes_somos'] ?? '');
    $descripcion_quienes_somos = sanitizar($_POST['descripcion_quienes_somos'] ?? '');
    $facebook_url = sanitizar($_POST['facebook_url'] ?? '');
    $youtube_url = sanitizar($_POST['youtube_url'] ?? '');
    $instagram_url = sanitizar($_POST['instagram_url'] ?? '');
    $direccion = sanitizar($_POST['direccion'] ?? '');
    $telefono_contacto = sanitizar($_POST['telefono_contacto'] ?? '');
    $email_contacto = sanitizar($_POST['email_contacto'] ?? '');
    
    // Validaciones
    if (empty($nombre_sitio) || empty($mensaje_banner)) {
        $error = 'Los campos obligatorios no pueden estar vacíos';
    } elseif (!empty($email_contacto) && !validarEmail($email_contacto)) {
        $error = 'El email de contacto no es válido';
    } else {
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
                    direccion = ?, telefono_contacto = ?, email_contacto = ?
                    WHERE id = 1
                ");
                $stmt->execute([
                    $nombre_sitio, $color_esquema, $mensaje_banner,
                    $titulo_quienes_somos, $descripcion_quienes_somos,
                    $facebook_url, $youtube_url, $instagram_url,
                    $direccion, $telefono_contacto, $email_contacto
                ]);
            } else {
                // Insertar
                $stmt = $conexion->prepare("
                    INSERT INTO configuracion_sitio 
                    (nombre_sitio, color_esquema, mensaje_banner, titulo_quienes_somos, 
                     descripcion_quienes_somos, facebook_url, youtube_url, instagram_url,
                     direccion, telefono_contacto, email_contacto) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $nombre_sitio, $color_esquema, $mensaje_banner,
                    $titulo_quienes_somos, $descripcion_quienes_somos,
                    $facebook_url, $youtube_url, $instagram_url,
                    $direccion, $telefono_contacto, $email_contacto
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
                
                <form method="POST" action="">
                    <!-- Información General -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Información General
                        </h3>
                        
                        <div class="form-group">
                            <label for="nombre_sitio">Nombre del Sitio *</label>
                            <input type="text" id="nombre_sitio" name="nombre_sitio" required 
                                   value="<?= $config ? htmlspecialchars($config['nombre_sitio']) : '' ?>">
                        </div>
                    </div>
                    
                    <!-- Esquema de Colores -->
                    <div class="form-section">
                        <h3>
                            <i class="fas fa-palette"></i>
                            Esquema de Colores
                        </h3>
                        
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
                            <label for="mensaje_banner">Mensaje del Banner *</label>
                            <input type="text" id="mensaje_banner" name="mensaje_banner" required 
                                   value="<?= $config ? htmlspecialchars($config['mensaje_banner']) : '' ?>">
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
    </script>
</body>
</html>
