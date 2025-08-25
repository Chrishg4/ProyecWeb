<?php
require_once '../sesiones.php';
requerirLogin();

$usuario_actual = obtenerUsuarioActual();
$es_admin = esAdministrador();
$mensaje = '';
$error = '';

// Obtener datos del usuario actual
try {
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        $error = 'Usuario no encontrado';
    }
} catch(PDOException $e) {
    $error = 'Error al obtener los datos del usuario';
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error && $usuario) {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    
    if (empty($nombre) || empty($email)) {
        $error = 'El nombre y email son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido';
    } elseif (!empty($password_nueva) && $password_nueva !== $password_confirmar) {
        $error = 'Las contraseñas no coinciden';
    } elseif (!empty($password_nueva) && strlen($password_nueva) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        try {
            // Verificar si el email ya está en uso por otro usuario
            $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $error = 'El email ya está en uso por otro usuario';
            } else {
                // Actualizar datos del usuario
                if (!empty($password_nueva)) {
                    // Actualizar con nueva contraseña
                    $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                    $stmt = $conexion->prepare("
                        UPDATE usuarios SET 
                        nombre = ?, email = ?, telefono = ?, password = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$nombre, $email, $telefono, $password_hash, $_SESSION['user_id']]);
                } else {
                    // Actualizar sin cambiar contraseña
                    $stmt = $conexion->prepare("
                        UPDATE usuarios SET 
                        nombre = ?, email = ?, telefono = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$nombre, $email, $telefono, $_SESSION['user_id']]);
                }
                
                $mensaje = 'Perfil actualizado correctamente';
                
                // Recargar datos del usuario
                $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $usuario = $stmt->fetch();
            }
        } catch(PDOException $e) {
            $error = 'Error al actualizar el perfil: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - UTH SOLUTIONS</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, <?= $es_admin ? '#1a237e' : '#4CAF50' ?> 0%, <?= $es_admin ? '#303f9f' : '#66BB6A' ?> 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 30px;
        }
        
        .privilege-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        .privilege-admin {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .privilege-agente {
            background: #e8f5e8;
            color: #4caf50;
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
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: <?= $es_admin ? '#1a237e' : '#4CAF50' ?>;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .password-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .password-section h4 {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 16px;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, <?= $es_admin ? '#1a237e' : '#4CAF50' ?> 0%, <?= $es_admin ? '#303f9f' : '#66BB6A' ?> 100%);
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
        
        .info-note {
            background: #e1f5fe;
            color: #01579b;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
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
                    <i class="fas fa-user-edit"></i>
                    Mi Perfil
                </h1>
                <a href="<?= $es_admin ? 'dashboard.php' : 'agente.php' ?>" class="back-btn">
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
            
            <?php if (!$error && $usuario): ?>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
                    <div class="privilege-badge <?= $es_admin ? 'privilege-admin' : 'privilege-agente' ?>">
                        <?= $es_admin ? 'Administrador' : 'Agente de Ventas' ?>
                    </div>
                </div>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo *</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                    </div>
                    
                    <div class="password-section">
                        <h4><i class="fas fa-lock"></i> Cambiar Contraseña</h4>
                        <div class="info-note">
                            <i class="fas fa-info-circle"></i>
                            Deja estos campos vacíos si no deseas cambiar tu contraseña.
                        </div>
                        
                        <div class="form-row" style="margin-top: 15px;">
                            <div class="form-group">
                                <label for="password_nueva">Nueva Contraseña</label>
                                <input type="password" id="password_nueva" name="password_nueva" minlength="6">
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmar">Confirmar Contraseña</label>
                                <input type="password" id="password_confirmar" name="password_confirmar" minlength="6">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Actualizar Perfil
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Validar que las contraseñas coincidan
        document.getElementById('password_confirmar').addEventListener('input', function() {
            const nueva = document.getElementById('password_nueva').value;
            const confirmar = this.value;
            
            if (nueva && confirmar && nueva !== confirmar) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#ddd';
            }
        });
    </script>
</body>
</html>
