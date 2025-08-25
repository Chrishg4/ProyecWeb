<?php
require_once 'sesiones.php';
require_once 'conexion.php';

// Obtener configuración del sitio
try {
    $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch();
} catch(PDOException $e) {
    $config = null;
}

// Obtener parámetros de filtro
$tipo = sanitizar($_GET['tipo'] ?? '');
$destacadas = isset($_GET['destacadas']) ? 1 : 0;

// Construir consulta
$where_conditions = ["estado = 'activa'"];
$params = [];

if ($destacadas) {
    $where_conditions[] = "destacada = ?";
    $params[] = 1;
    $titulo_pagina = "Propiedades Destacadas";
} elseif ($tipo == 'venta') {
    $where_conditions[] = "tipo = ?";
    $params[] = 'venta';
    $titulo_pagina = "Propiedades en Venta";
} elseif ($tipo == 'alquiler') {
    $where_conditions[] = "tipo = ?";
    $params[] = 'alquiler';
    $titulo_pagina = "Propiedades en Alquiler";
} else {
    $titulo_pagina = "Todas las Propiedades";
}

$where_clause = implode(' AND ', $where_conditions);

// Obtener propiedades
try {
    $stmt = $conexion->prepare("SELECT * FROM propiedades WHERE $where_clause ORDER BY fecha_creacion DESC");
    $stmt->execute($params);
    $propiedades = $stmt->fetchAll();
} catch(PDOException $e) {
    $propiedades = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?> - <?= $config ? htmlspecialchars($config['nombre_sitio']) : 'UTH SOLUTIONS REAL STATE' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .properties-header {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .properties-header h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .properties-header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .properties-content {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .property-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .property-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #4CAF50;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .property-badge.destacada {
            background: #FF9800;
        }
        
        .property-badge.alquiler {
            background: #2196F3;
        }
        
        .property-content {
            padding: 25px;
        }
        
        .property-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1a237e;
        }
        
        .property-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .property-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .property-price {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
        }
        
        .property-type {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .property-location {
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-view {
            flex: 1;
            padding: 12px;
            background: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn-view:hover {
            background: #303f9f;
            color: white;
        }
        
        .btn-contact {
            padding: 12px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn-contact:hover {
            background: #45a049;
            color: white;
        }
        
        .no-properties {
            text-align: center;
            padding: 60px 20px;
        }
        
        .no-properties i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .no-properties h3 {
            font-size: 24px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .no-properties p {
            color: #888;
            font-size: 16px;
        }
        
        .back-home {
            text-align: center;
            margin-top: 40px;
        }
        
        .back-home a {
            background: #1a237e;
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .back-home a:hover {
            background: #303f9f;
            color: white;
        }
        
        @media (max-width: 768px) {
            .properties-header h1 {
                font-size: 32px;
            }
            
            .properties-grid {
                grid-template-columns: 1fr;
            }
            
            .property-details {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .property-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="<?= $config ? 'color-' . $config['color_esquema'] : 'color-azul' ?>">
        <!-- Header -->
        <header class="header" style="background: #02050e; width: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="header-container" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; height: 70px;">
                <div class="logo" style="display: flex; align-items: center; height: 60px;">
                    <?php 
                        $logo_navbar = 'images/logo.png'; // por defecto
                        if ($config && $config['logo_navbar'] && file_exists('uploads/logos/' . $config['logo_navbar'])) {
                                $logo_navbar = 'uploads/logos/' . $config['logo_navbar'];
                        }
                    ?>
                    <img src="<?= $logo_navbar ?>" alt="UTH Solutions Logo" class="logo-img" style="height: 48px; width: auto; margin-right: 18px;">
                </div>
                <nav style="flex: 1;">
                    <ul class="nav-menu" style="display: flex; gap: 32px; list-style: none; margin: 0; padding: 0; justify-content: center; align-items: center;">
                        <li><a href="index.php" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">INICIO</a></li>
                        <li><a href="index.php#quienes-somos" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">QUIENES SOMOS</a></li>
                        <li><a href="propiedades.php?tipo=alquiler" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">ALQUILERES</a></li>
                        <li><a href="propiedades.php?tipo=venta" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">VENTAS</a></li>
                        <li><a href="index.php#contacto" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">CONTÁCTENOS</a></li>
                    </ul>
                </nav>
                <div class="header-right" style="display: flex; align-items: center; gap: 18px;">
                    <form method="GET" action="index.php" style="display: flex; align-items: center; background: #181b23; border-radius: 20px; padding: 2px 10px;">
                        <input type="text" name="buscar" class="search-input" placeholder="Buscar propiedades..." style="background: transparent; border: none; color: #fff; outline: none; padding: 6px 8px; font-size: 1rem; width: 120px;">
                        <button type="submit" class="search-btn" style="background: none; border: none; color: #ffd700; font-size: 1.1rem; cursor: pointer; padding: 0 4px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div class="social-icons" style="display: flex; align-items: center; gap: 12px;">
                        <a href="<?= $config['facebook_url'] ?? '#' ?>" class="facebook" target="_blank" style="color: #ffd700; font-size: 1.2rem;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?= $config['youtube_url'] ?? '#' ?>" class="youtube" target="_blank" style="color: #ffd700; font-size: 1.2rem;">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="<?= $config['instagram_url'] ?? '#' ?>" class="instagram" target="_blank" style="color: #ffd700; font-size: 1.2rem;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="login.php" class="login-icon" style="color: #ffd700; font-size: 1.2rem; position: relative;">
                            <i class="fas fa-user"></i>
                            <span class="notification-badge" style="position: absolute; top: -7px; right: -10px; background: #e53935; color: #fff; border-radius: 50%; font-size: 0.7rem; padding: 2px 5px; min-width: 18px; text-align: center;">4</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

    <!-- Properties Header -->
    <section class="properties-header">
        <div class="container">
            <h1><?= $titulo_pagina ?></h1>
            <p>Encuentra la propiedad perfecta para ti</p>
        </div>
    </section>

    <!-- Properties Content -->
    <section class="properties-content">
        <div class="container">
            <?php if (empty($propiedades)): ?>
                <div class="no-properties">
                    <i class="fas fa-home"></i>
                    <h3>No hay propiedades disponibles</h3>
                    <p>Actualmente no tenemos propiedades que coincidan con tu búsqueda.</p>
                </div>
            <?php else: ?>
                <div class="properties-grid">
                    <?php foreach ($propiedades as $propiedad): ?>
                        <div class="property-card">
                            <div class="property-image" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');">
                                <div class="property-badge <?= $propiedad['destacada'] ? 'destacada' : $propiedad['tipo'] ?>">
                                    <?php if ($propiedad['destacada']): ?>
                                        Destacada
                                    <?php else: ?>
                                        <?= ucfirst($propiedad['tipo']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="property-content">
                                <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                                <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                                
                                <div class="property-details">
                                    <div class="property-price">$<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                                    <div class="property-type"><?= ucfirst($propiedad['tipo']) ?></div>
                                </div>
                                
                                <div class="property-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($propiedad['ubicacion']) ?>
                                </div>
                                
                                <div class="property-actions">
                                    <a href="detalle.php?id=<?= $propiedad['id'] ?>" class="btn-view">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                    <a href="#contacto" class="btn-contact">
                                        <i class="fas fa-phone"></i> Contactar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="back-home">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background: #ffd731; color: #02050e; padding: 0; margin-top: 0;">
        <div style="background: #1a237e; width: 100vw; height: 24px; margin-left: 50%; transform: translateX(-50%); border-radius: 0 0 12px 12px;"></div>
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0;">
            <div class="footer-content" style="display: flex; align-items: flex-start; justify-content: space-between; padding: 32px 0 0 0; gap: 32px;">
                <div style="flex: 1.2; display: flex; flex-direction: column; justify-content: flex-start; gap: 18px;">
                    <div style="font-size: 1.1rem; color: #181b23; margin-bottom: 10px;">
                        <span style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px;"><i class="fas fa-map-marker-alt" style="font-size: 1.3em;"></i> <span><b>Dirección;</b> Cañas Guanacaste, 100 mts Este<br>Parque de Cañas</span></span>
                        <span style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;"><i class="fas fa-phone-alt" style="font-size: 1.2em;"></i> <span><b>Teléfono:</b> 8890-2030</span></span>
                        <span style="display: flex; align-items: center; gap: 10px;"><i class="fas fa-envelope" style="font-size: 1.2em;"></i> <span><b>Email:</b> info@utnrealstate.com</span></span>
                    </div>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; gap: 10px;">
                    <div style="margin-bottom: 10px;">
                        <img src="images/default_logo.png" alt="Logo" style="height: 54px; margin-bottom: 8px;">
                    </div>
                    <div style="font-weight: bold; text-align: center; color: #fff; font-size: 1.1rem; letter-spacing: 1px; text-shadow: 0 1px 2px #000; margin-bottom: 10px;">
                        UTN SOLUTIONS<br>REAL STATE
                    </div>
                    <div style="display: flex; gap: 18px; justify-content: center; align-items: center; margin-top: 8px;">
                        <a href="#" style="background: #3b5998; color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.5em;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="background: #e53935; color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.5em;"><i class="fab fa-youtube"></i></a>
                        <a href="#" style="background: #8a3ab9; color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.5em;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div style="flex: 1.2; display: flex; justify-content: flex-end;">
                    <div style="background: #e5d6d6; color: #1a237e; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.10); padding: 22px 28px 18px 28px; min-width: 320px; max-width: 350px;">
                        <div style="font-size: 1.2rem; font-weight: bold; text-align: center; color: #1a237e; margin-bottom: 12px;">Contactanos</div>
                        <form method="post" action="contacto_footer.php">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="font-weight: bold; color: #1a237e;">Nombre:</label>
                                <input type="text" name="nombre" style="width: 100%; padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                                <label style="font-weight: bold; color: #1a237e;">Email:</label>
                                <input type="email" name="email" style="width: 100%; padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                                <label style="font-weight: bold; color: #1a237e;">Teléfono:</label>
                                <input type="tel" name="telefono" style="width: 100%; padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                                <label style="font-weight: bold; color: #1a237e;">Mensaje:</label>
                                <textarea name="mensaje" style="width: 100%; padding: 6px; border-radius: 4px; border: 1px solid #ccc;"></textarea>
                            </div>
                            <button type="submit" style="background: #1a237e; color: #ffd700; border: none; border-radius: 4px; padding: 8px 0; font-weight: bold; width: 100%; margin-top: 12px; font-size: 1rem;">Envaír</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div style="background: #1a237e; color: #fff; text-align: center; padding: 10px 0; width: 100vw; margin-left: 50%; transform: translateX(-50%); border-radius: 0; font-size: 1rem; letter-spacing: 1px;">
            Derechos Reservados 2024
        </div>
    </footer>
