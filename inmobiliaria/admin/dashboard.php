<?php
require_once '../sesiones.php';
requerirLogin();
requerirAdmin();

$usuario_actual = obtenerUsuarioActual();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - UTH SOLUTIONS</title>
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
        
        .admin-title h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .admin-title p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #ffd700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a237e;
            font-weight: bold;
            font-size: 18px;
        }
        
        .user-details h3 {
            font-size: 16px;
            margin-bottom: 2px;
        }
        
        .user-details p {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .logout-btn {
            background: #f44336;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #d32f2f;
        }
        
        .admin-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            text-align: center;
        }
        
        .welcome-section h2 {
            color: #1a237e;
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .welcome-section p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .admin-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .option-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .option-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1a237e, #303f9f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 35px;
            color: white;
        }
        
        .option-card h3 {
            color: #1a237e;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        .option-card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .option-btn {
            background: #1a237e;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
            display: inline-block;
        }
        
        .option-btn:hover {
            background: #303f9f;
            color: white;
        }
        
        .stats-section {
            margin-bottom: 40px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .admin-header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .admin-options {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-header-content">
                <div class="admin-title">
                    <h1>Panel Administrativo</h1>
                    <p>UTH SOLUTIONS REAL STATE</p>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($usuario_actual['nombre'], 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <h3><?= htmlspecialchars($usuario_actual['nombre']) ?></h3>
                        <p><?= ucfirst($usuario_actual['privilegio']) ?></p>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </div>
        </header>
        
        <div class="admin-content">
            <div class="welcome-section">
                <h2>¡Bienvenido, <?= htmlspecialchars($usuario_actual['nombre']) ?>!</h2>
                <p>Desde aquí puedes administrar todos los aspectos de tu sitio web inmobiliario.</p>
            </div>
            
            <?php
            // Obtener estadísticas
            try {
                $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE estado = 'activa'");
                $stmt->execute();
                $total_propiedades = $stmt->fetch()['total'];
                
                $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE tipo = 'venta' AND estado = 'activa'");
                $stmt->execute();
                $total_ventas = $stmt->fetch()['total'];
                
                $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE tipo = 'alquiler' AND estado = 'activa'");
                $stmt->execute();
                $total_alquileres = $stmt->fetch()['total'];
                
                $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM usuarios WHERE privilegio = 'agente'");
                $stmt->execute();
                $total_agentes = $stmt->fetch()['total'];
            } catch(PDOException $e) {
                $total_propiedades = $total_ventas = $total_alquileres = $total_agentes = 0;
            }
            ?>
            
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_propiedades ?></div>
                        <div class="stat-label">Propiedades Activas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_ventas ?></div>
                        <div class="stat-label">En Venta</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_alquileres ?></div>
                        <div class="stat-label">En Alquiler</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_agentes ?></div>
                        <div class="stat-label">Agentes Activos</div>
                    </div>
                </div>
            </div>
            
            <div class="admin-options">
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>Personalizar Página</h3>
                    <p>Configura los colores, imágenes, mensajes y toda la apariencia de tu sitio web.</p>
                    <a href="personalizar.php" class="option-btn">Personalizar</a>
                </div>
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Gestionar Usuarios</h3>
                    <p>Crea, edita y administra usuarios administradores y agentes de ventas.</p>
                    <a href="usuarios.php" class="option-btn">Gestionar</a>
                </div>
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Gestionar Propiedades</h3>
                    <p>Administra todas las propiedades, edita información y gestiona el inventario.</p>
                    <a href="propiedades.php" class="option-btn">Gestionar</a>
                </div>
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h3>Mi Perfil</h3>
                    <p>Actualiza tu información personal, cambia contraseña y gestiona tu cuenta.</p>
                    <a href="perfil.php" class="option-btn">Editar Perfil</a>
                </div>
                
                
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Ver Sitio Web</h3>
                    <p>Visita el sitio web público para ver cómo se ve para los visitantes.</p>
                    <a href="../index.php" class="option-btn" target="_blank">Ver Sitio</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
