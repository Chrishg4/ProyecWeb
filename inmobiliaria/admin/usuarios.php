<?php
require_once '../sesiones.php';
requerirLogin();
requerirAdmin();

$success = '';
$error = '';

// Manejar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion == 'crear') {
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $correo = sanitizar($_POST['correo'] ?? '');
        $email = sanitizar($_POST['email'] ?? '');
        $usuario = sanitizar($_POST['usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $privilegio = $_POST['privilegio'] ?? 'agente';
        
        // Validaciones
        if (empty($nombre) || empty($email) || empty($usuario) || empty($contrasena)) {
            $error = 'Los campos obligatorios no pueden estar vacíos';
        } elseif (!validarEmail($email)) {
            $error = 'El email no es válido';
        } elseif (strlen($contrasena) < 3) {
            $error = 'La contraseña debe tener al menos 3 caracteres';
        } else {
            try {
                // Verificar si el usuario o email ya existen
                $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? OR email = ?");
                $stmt->execute([$usuario, $email]);
                if ($stmt->fetch()) {
                    $error = 'El usuario o email ya están en uso';
                } else {
                    // Crear usuario
                    $stmt = $conexion->prepare("
                        INSERT INTO usuarios (nombre, telefono, correo, email, usuario, contrasena, privilegio) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $nombre, $telefono, $correo, $email, $usuario, 
                        encriptarPassword($contrasena), $privilegio
                    ]);
                    $success = 'Usuario creado correctamente';
                }
            } catch(PDOException $e) {
                $error = 'Error al crear usuario: ' . $e->getMessage();
            }
        }
    } elseif ($accion == 'eliminar') {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id == $_SESSION['user_id']) {
            $error = 'No puedes eliminar tu propio usuario';
        } elseif ($id > 0) {
            try {
                $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Usuario eliminado correctamente';
            } catch(PDOException $e) {
                $error = 'Error al eliminar usuario';
            }
        }
    } elseif ($accion == 'editar') {
        $id = intval($_POST['id'] ?? 0);
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $correo = sanitizar($_POST['correo'] ?? '');
        $email = sanitizar($_POST['email'] ?? '');
        $usuario = sanitizar($_POST['usuario'] ?? '');
        $privilegio = $_POST['privilegio'] ?? 'agente';
        $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
        
        if (empty($nombre) || empty($email) || empty($usuario)) {
            $error = 'Los campos obligatorios no pueden estar vacíos';
        } elseif (!validarEmail($email)) {
            $error = 'El email no es válido';
        } else {
            try {
                // Verificar si el usuario o email ya existen (excluyendo el actual)
                $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE (usuario = ? OR email = ?) AND id != ?");
                $stmt->execute([$usuario, $email, $id]);
                if ($stmt->fetch()) {
                    $error = 'El usuario o email ya están en uso por otro usuario';
                } else {
                    if (!empty($nueva_contrasena)) {
                        // Actualizar con nueva contraseña
                        $stmt = $conexion->prepare("
                            UPDATE usuarios SET nombre = ?, telefono = ?, correo = ?, email = ?, 
                                   usuario = ?, contrasena = ?, privilegio = ? 
                            WHERE id = ?
                        ");
                        $stmt->execute([
                            $nombre, $telefono, $correo, $email, $usuario, 
                            encriptarPassword($nueva_contrasena), $privilegio, $id
                        ]);
                    } else {
                        // Actualizar sin cambiar contraseña
                        $stmt = $conexion->prepare("
                            UPDATE usuarios SET nombre = ?, telefono = ?, correo = ?, email = ?, 
                                   usuario = ?, privilegio = ? 
                            WHERE id = ?
                        ");
                        $stmt->execute([$nombre, $telefono, $correo, $email, $usuario, $privilegio, $id]);
                    }
                    $success = 'Usuario actualizado correctamente';
                }
            } catch(PDOException $e) {
                $error = 'Error al actualizar usuario';
            }
        }
    }
}

// Obtener todos los usuarios
try {
    $stmt = $conexion->prepare("SELECT * FROM usuarios ORDER BY fecha_creacion DESC");
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
} catch(PDOException $e) {
    $usuarios = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - UTH SOLUTIONS</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .create-btn {
            background: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .create-btn:hover {
            background: #45a049;
            color: white;
        }
        
        .users-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: #1a237e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        
        .table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #1a237e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-details h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .user-details p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-admin {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-agente {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #2196F3;
            color: white;
        }
        
        .btn-edit:hover {
            background: #1976D2;
            color: white;
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            background: #1a237e;
            color: white;
            padding: 20px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1a237e;
        }
        
        .submit-btn {
            background: #1a237e;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
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
        
        @media (max-width: 768px) {
            .admin-header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .content-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 10px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                margin: 20px auto;
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-header-content">
                <div>
                    <h1>Gestionar Usuarios</h1>
                    <p>Administra usuarios y agentes del sistema</p>
                </div>
                <a href="dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </header>
        
        <div class="admin-content">
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
            
            <div class="content-header">
                <h2>Lista de Usuarios</h2>
                <button class="create-btn" onclick="openModal('createModal')">
                    <i class="fas fa-plus"></i> Crear Usuario
                </button>
            </div>
            
            <div class="users-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Contacto</th>
                            <th>Privilegio</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                                    </div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($usuario['nombre']) ?></h4>
                                        <p>@<?= htmlspecialchars($usuario['usuario']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($usuario['email']) ?></p>
                                    <?php if ($usuario['telefono']): ?>
                                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($usuario['telefono']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-<?= $usuario['privilegio'] ?>">
                                    <?= ucfirst($usuario['privilegio']) ?>
                                </span>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-small btn-edit" onclick="editUser(<?= htmlspecialchars(json_encode($usuario)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                    <button class="btn-small btn-delete" onclick="deleteUser(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Crear Usuario -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Crear Nuevo Usuario</h3>
                <button class="close" onclick="closeModal('createModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono">
                        </div>
                        
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="email" id="correo" name="correo">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="usuario">Usuario *</label>
                            <input type="text" id="usuario" name="usuario" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contrasena">Contraseña *</label>
                            <input type="password" id="contrasena" name="contrasena" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="privilegio">Privilegio</label>
                            <select id="privilegio" name="privilegio">
                                <option value="agente">Agente de Ventas</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Usuario -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Usuario</h3>
                <button class="close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_nombre">Nombre *</label>
                            <input type="text" id="edit_nombre" name="nombre" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_telefono">Teléfono</label>
                            <input type="tel" id="edit_telefono" name="telefono">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_correo">Correo</label>
                            <input type="email" id="edit_correo" name="correo">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_email">Email *</label>
                            <input type="email" id="edit_email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_usuario">Usuario *</label>
                            <input type="text" id="edit_usuario" name="usuario" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_privilegio">Privilegio</label>
                            <select id="edit_privilegio" name="privilegio">
                                <option value="agente">Agente de Ventas</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="nueva_contrasena">Nueva Contraseña (dejar vacío para mantener actual)</label>
                            <input type="password" id="nueva_contrasena" name="nueva_contrasena">
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Formulario para eliminar -->
    <form id="deleteForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" id="delete_id" name="id">
    </form>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function editUser(usuario) {
            document.getElementById('edit_id').value = usuario.id;
            document.getElementById('edit_nombre').value = usuario.nombre;
            document.getElementById('edit_telefono').value = usuario.telefono || '';
            document.getElementById('edit_correo').value = usuario.correo || '';
            document.getElementById('edit_email').value = usuario.email;
            document.getElementById('edit_usuario').value = usuario.usuario;
            document.getElementById('edit_privilegio').value = usuario.privilegio;
            openModal('editModal');
        }
        
        function deleteUser(id, nombre) {
            if (confirm('¿Estás seguro de que quieres eliminar al usuario "' + nombre + '"?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
