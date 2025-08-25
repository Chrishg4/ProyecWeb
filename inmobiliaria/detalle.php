<?php
require_once 'sesiones.php';
require_once 'conexion.php';

// Obtener ID de la propiedad
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// Obtener configuración del sitio
try {
    $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch();
} catch(PDOException $e) {
    $config = null;
}

// Obtener información de la propiedad
try {
    $stmt = $conexion->prepare("
        SELECT p.*, u.nombre as agente_nombre, u.telefono as agente_telefono, u.email as agente_email 
        FROM propiedades p 
        LEFT JOIN usuarios u ON p.agente_id = u.id 
        WHERE p.id = ? AND p.estado = 'activa'
    ");
    $stmt->execute([$id]);
    $propiedad = $stmt->fetch();
} catch(PDOException $e) {
    $propiedad = null;
}

if (!$propiedad) {
    header('Location: index.php');
    exit();
}

// Obtener imágenes adicionales
try {
    $stmt = $conexion->prepare("SELECT * FROM imagenes_propiedades WHERE propiedad_id = ? ORDER BY orden");
    $stmt->execute([$id]);
    $imagenes_adicionales = $stmt->fetchAll();
} catch(PDOException $e) {
    $imagenes_adicionales = [];
}

// Obtener propiedades similares
try {
    $stmt = $conexion->prepare("
        SELECT * FROM propiedades 
        WHERE tipo = ? AND id != ? AND estado = 'activa' 
        ORDER BY fecha_creacion DESC 
        LIMIT 3
    ");
    $stmt->execute([$propiedad['tipo'], $id]);
    $propiedades_similares = $stmt->fetchAll();
} catch(PDOException $e) {
    $propiedades_similares = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($propiedad['titulo']) ?> - <?= $config ? htmlspecialchars($config['nombre_sitio']) : 'UTH SOLUTIONS REAL STATE' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .property-header {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%);
            color: white;
            padding: 40px 0;
        }
        
        .property-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .property-title-section h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .property-meta {
            display: flex;
            gap: 20px;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        
        .property-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .property-main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .property-gallery {
            margin-bottom: 30px;
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            background-size: cover;
            background-position: center;
            border-radius: 15px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .property-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .property-badge.destacada {
            background: #FF9800;
        }
        
        .property-badge.alquiler {
            background: #2196F3;
        }
        
        .thumbnail-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }
        
        .thumbnail {
            height: 80px;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
            border: 3px solid transparent;
        }
        
        .thumbnail:hover,
        .thumbnail.active {
            transform: scale(1.05);
            border-color: #1a237e;
        }
        
        .property-info {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .property-info h2 {
            color: #1a237e;
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .property-description {
            color: #666;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        
        .property-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .feature-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .feature-icon {
            font-size: 24px;
            color: #1a237e;
            margin-bottom: 10px;
        }
        
        .feature-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .feature-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .property-sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        
        .price-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .price-amount {
            font-size: 48px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        
        .price-type {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .contact-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .btn-contact {
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #1a237e;
            color: white;
        }
        
        .btn-primary:hover {
            background: #303f9f;
            color: white;
        }
        
        .btn-secondary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #45a049;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            color: #1a237e;
            border: 2px solid #1a237e;
        }
        
        .btn-outline:hover {
            background: #1a237e;
            color: white;
        }
        
        .agent-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .agent-avatar {
            width: 80px;
            height: 80px;
            background: #1a237e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 15px;
        }
        
        .agent-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .agent-title {
            color: #666;
            margin-bottom: 15px;
        }
        
        .agent-contact {
            font-size: 14px;
            color: #666;
        }
        
        .similar-properties {
            margin-top: 60px;
        }
        
        .section-title {
            font-size: 32px;
            color: #1a237e;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
        }
        
        .card-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        
        .card-content {
            padding: 20px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 10px;
        }
        
        .card-price {
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        
        .card-location {
            color: #666;
            font-size: 14px;
        }
        
        .map-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 30px 0;
        }
        
        .map-placeholder {
            height: 300px;
            background: #f0f0f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 18px;
        }
        
        @media (max-width: 768px) {
            .property-header-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .property-main {
                grid-template-columns: 1fr;
            }
            
            .property-sidebar {
                position: static;
            }
            
            .contact-buttons {
                flex-direction: row;
            }
            
            .property-features {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .properties-grid {
                grid-template-columns: 1fr;
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

    <!-- Property Header -->
    <section class="property-header">
        <div class="property-header-content">
            <div class="property-title-section">
                <h1><?= htmlspecialchars($propiedad['titulo']) ?></h1>
                <div class="property-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($propiedad['ubicacion']) ?></span>
                    <span><i class="fas fa-tag"></i> <?= ucfirst($propiedad['tipo']) ?></span>
                    <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($propiedad['fecha_creacion'])) ?></span>
                </div>
            </div>
            <a href="propiedades.php?tipo=<?= $propiedad['tipo'] ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver a <?= ucfirst($propiedad['tipo']) ?>
            </a>
        </div>
    </section>

    <!-- Property Content -->
    <div class="property-content">
        <div class="property-main">
            <div class="property-left">
                <!-- Gallery -->
                <div class="property-gallery">
                    <div class="main-image" id="mainImage" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');">
                        <div class="property-badge <?= $propiedad['destacada'] ? 'destacada' : $propiedad['tipo'] ?>">
                            <?php if ($propiedad['destacada']): ?>
                                Destacada
                            <?php else: ?>
                                <?= ucfirst($propiedad['tipo']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($imagenes_adicionales)): ?>
                    <div class="thumbnail-gallery">
                        <div class="thumbnail active" data-image="images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');"></div>
                        <?php foreach ($imagenes_adicionales as $imagen): ?>
                        <div class="thumbnail" data-image="images/<?= htmlspecialchars($imagen['imagen']) ?>" style="background-image: url('images/<?= htmlspecialchars($imagen['imagen']) ?>');"></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Property Info -->
                <div class="property-info">
                    <h2>Descripción</h2>
                    <div class="property-description">
                        <?= nl2br(htmlspecialchars($propiedad['descripcion_larga'] ?: $propiedad['descripcion_breve'])) ?>
                    </div>
                    
                    <div class="property-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="feature-label">Precio</div>
                            <div class="feature-value">$<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="feature-label">Tipo</div>
                            <div class="feature-value"><?= ucfirst($propiedad['tipo']) ?></div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="feature-label">Ubicación</div>
                            <div class="feature-value"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="feature-label">Estado</div>
                            <div class="feature-value"><?= $propiedad['destacada'] ? 'Destacada' : 'Disponible' ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Map -->
                <?php if ($propiedad['mapa']): ?>
                <div class="map-section">
                    <h2>Ubicación</h2>
                    <div class="map-placeholder">
                        <i class="fas fa-map"></i> Mapa de ubicación
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="property-sidebar">
                <!-- Price Card -->
                <div class="price-card">
                    <div class="price-amount">$<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                    <div class="price-type">
                        <?= $propiedad['tipo'] == 'venta' ? 'Precio de Venta' : 'Precio Mensual' ?>
                    </div>
                    
                    <div class="contact-buttons">
                        <a href="tel:<?= $config['telefono_contacto'] ?? '8800-2030' ?>" class="btn-contact btn-primary">
                            <i class="fas fa-phone"></i> Llamar Ahora
                        </a>
                        <a href="mailto:<?= $config['email_contacto'] ?? 'info@uthrealestate.com' ?>" class="btn-contact btn-secondary">
                            <i class="fas fa-envelope"></i> Enviar Email
                        </a>
                        <a href="https://wa.me/506<?= str_replace('-', '', $config['telefono_contacto'] ?? '88002030') ?>" class="btn-contact btn-outline" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
                
                <!-- Agent Card -->
                <?php if ($propiedad['agente_nombre']): ?>
                <div class="agent-card">
                    <div class="agent-avatar">
                        <?= strtoupper(substr($propiedad['agente_nombre'], 0, 1)) ?>
                    </div>
                    <div class="agent-name"><?= htmlspecialchars($propiedad['agente_nombre']) ?></div>
                    <div class="agent-title">Agente de Ventas</div>
                    <div class="agent-contact">
                        <?php if ($propiedad['agente_telefono']): ?>
                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($propiedad['agente_telefono']) ?></p>
                        <?php endif; ?>
                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($propiedad['agente_email']) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Similar Properties -->
        <?php if (!empty($propiedades_similares)): ?>
        <div class="similar-properties">
            <h2 class="section-title">Propiedades Similares</h2>
            <div class="properties-grid">
                <?php foreach ($propiedades_similares as $similar): ?>
                <div class="property-card">
                    <div class="card-image" style="background-image: url('images/<?= $similar['imagen_destacada'] ? htmlspecialchars($similar['imagen_destacada']) : 'default-house.jpg' ?>');"></div>
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($similar['titulo']) ?></h3>
                        <div class="card-price">$<?= number_format($similar['precio'], 0, '.', ',') ?></div>
                        <div class="card-location">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($similar['ubicacion']) ?>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="detalle.php?id=<?= $similar['id'] ?>" class="btn-contact btn-primary" style="width: 100%; text-align: center;">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

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
                        <form method="post" action="#contacto">
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
                }
            });
        });
    </script>
</body>
</html>
