<?php
require_once 'sesiones.php';

$error = '';
$success = '';

// Si ya está logueado, redirigir
if (estaLogueado()) {
    if (esAdministrador()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: admin/agente.php');
    }
    exit();
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = sanitizar($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($usuario) || empty($password)) {
        $error = 'Por favor complete todos los campos';
    } else {
        if (iniciarSesion($usuario, $password)) {
            if (esAdministrador()) {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: admin/agente.php');
            }
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UTH SOLUTIONS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-home"  style="margin-right: 40px;"></i> UTN SOLUTIONS
                </div>
                <h2>Iniciar Sesión</h2>
                <p>Accede al panel administrativo</p>
            </div>
            
           
            
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
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="usuario" name="usuario" required value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
            
            
        </div>
    </div>
</body>
</html>
