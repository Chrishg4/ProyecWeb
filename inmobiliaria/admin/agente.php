<?php
require_once '../sesiones.php';
requerirLogin();

// Verificar que es agente (no administrador)
if (!esAgente()) {
    header('Location: dashboard.php');
    exit();
}

$usuario_actual = obtenerUsuarioActual();

// Obtener estadísticas del agente
try {
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE agente_id = ? AND estado = 'activa'");
    $stmt->execute([$_SESSION['user_id']]);
    $total_propiedades = $stmt->fetch()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE agente_id = ? AND tipo = 'venta' AND estado = 'activa'");
    $stmt->execute([$_SESSION['user_id']]);
    $total_ventas = $stmt->fetch()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE agente_id = ? AND tipo = 'alquiler' AND estado = 'activa'");
    $stmt->execute([$_SESSION['user_id']]);
    $total_alquileres = $stmt->fetch()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM propiedades WHERE agente_id = ? AND destacada = 1 AND estado = 'activa'");
    $stmt->execute([$_SESSION['user_id']]);
    $total_destacadas = $stmt->fetch()['total'];
} catch(PDOException $e) {
    $total_propiedades = $total_ventas = $total_alquileres = $total_destacadas = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Agente - UTH SOLUTIONS</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
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
            color: #4CAF50;
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
            color: #4CAF50;
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
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 35px;
            color: white;
        }
        
        .option-card h3 {
            color: #4CAF50;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        .option-card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .option-btn {
            background: #4CAF50;
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
            background: #45a049;
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
            color: #4CAF50;
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
                    <h1>Panel de Agente</h1>
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
                <p>Gestiona tus propiedades y actualiza tu información personal desde este panel.</p>
            </div>
            
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_propiedades ?></div>
                        <div class="stat-label">Mis Propiedades</div>
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
                        <div class="stat-number"><?= $total_destacadas ?></div>
                        <div class="stat-label">Destacadas</div>
                    </div>
                </div>
            </div>
            
            <div class="admin-options">
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Mis Propiedades</h3>
                    <p>Gestiona todas tus propiedades, agrega nuevas, edita o elimina las existentes.</p>
                    <a href="propiedades.php" class="option-btn">Gestionar</a>
                </div>
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3>Agregar Propiedad</h3>
                    <p>Agrega una nueva propiedad al sistema con toda la información necesaria.</p>
                    <a href="agregar_propiedad.php" class="option-btn">Agregar</a>
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
                    <p>Visita el sitio web público para ver cómo se ven tus propiedades.</p>
                    <a href="../index.php" class="option-btn">Ver Sitio</a>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>
