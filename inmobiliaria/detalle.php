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

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
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
.header-navbar-realstate {
    background: #0a0d15;
    width: 100%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 0;
}
.navbar-realstate {
    max-width: 1700px;
    margin: 0 auto;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
  
    box-sizing: border-box;
}
.navbar-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    margin-top: 12px;
    width: 170px;
}
.navbar-logo-img {
    height: 78px;
    margin-bottom: 0;
    display: block;
    margin-left: auto;
    margin-right: auto;
}
.navbar-social {
    display: flex;
    gap: 10px;
    margin-top: 0;
}
.navbar-social a {
    background: #fff;
    color: #181b23;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    border: 2px solid #e0e0e0;
    transition: box-shadow 0.2s, border 0.2s, background 0.2s, color 0.2s;
}
.navbar-social a:nth-child(1) { color: #3b5998; }
.navbar-social a:nth-child(2) { color: #e53935; }
.navbar-social a:nth-child(3) { color: #c13584; }
.navbar-social a:hover { background: #ffd700; color: #181b23; border: 2px solid #ffd700; }

.navbar-right-block {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    flex: 1;
    margin-top: 10px;
}
.navbar-top {
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    width: 100%;
    gap: 24px;
}
.perfil-icon .perfil-circulo {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffd700;
    border: 2px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    overflow: hidden;
    position: relative;
}
.perfil-icon img {
    width: 38px;   /* Más grande que antes */
    height: 38px;
    object-fit: contain;
    border-radius: 50%;
    background: transparent;
    padding: 0;
    margin: 0;
    box-shadow: none;
    border: none;
    position: relative;
    z-index: 1;
}
.navbar-menu-realstate {
    width: 100%;
    display: flex;
    justify-content: flex-end;
    margin-top: -25px;    /* Más pegado arriba */
}
.navbar-menu-realstate ul {
    display: flex;
    gap: 0;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}
.navbar-menu-realstate ul li {
    display: flex;
    align-items: center;
}
.navbar-menu-realstate ul li a {
    color: #ffd700;
    font-weight: bold;
    font-size: 1.13rem;
    letter-spacing: 1px;
    text-decoration: none;
    transition: color 0.2s;
    padding: 8px 12px;
    border-bottom: none;
}
.navbar-menu-realstate ul li a:hover {
    color: #fff;
}
.menu-sep {
    color: #ffd700 !important;
    font-weight: bold;
    font-size: 1.3rem;
    padding: 0 2px;
    user-select: none;
}
.navbar-search {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 20px;
    padding: 2px 16px;
    margin-top: 1px;    /* Más pegado arriba */
    margin-bottom: 8px; /* Más espacio abajo */
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    width: 260px;
    justify-content: flex-end;
}
.navbar-search input {
    background: transparent;
    border: none;
    color: #181b23;
    outline: none;
    padding: 8px 8px;
    font-size: 1.08rem;
    width: 180px;
}
.navbar-search button {
    background: none;
    border: none;
    color: #181b23;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 4px;
}
@media (max-width: 1100px) {
    .navbar-realstate { flex-direction: column; height: auto; padding: 12px; }
    .navbar-right-block { align-items: flex-end; }
    .navbar-menu-realstate ul { gap: 10px; }
}

        
        .property-header {
    width: 100%;
    margin: 0 auto 30px auto;
    padding-top: 30px;
}
.property-header-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
.property-title-section h1 {
    font-size: 2.5rem;
    color: #1a237e;
    font-weight: bold;
    margin-bottom: 10px;
    text-align: center;
}
.property-meta {
    display: flex;
    gap: 20px;
    font-size: 15px;
    opacity: 0.9;
    justify-content: center;
    color: #333;
}
.back-btn {
    background: #1a237e;
    color: #fff;
    padding: 10px 28px;
    border: none;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 30px;
    transition: background 0.3s, color 0.3s;
    display: inline-flex;
    align-items: center;
    text-align: center;
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    justify-content: center;
}
.back-btn:hover {
    transform: scale(1.07);
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
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        .property-card {
            max-width: 350px;
            width: 100%;
            margin: 0 10px 30px 10px;
        }
        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            max-width: 350px;
            width: 100%;
            margin: 0 auto;
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
        .footer-realstate {
    background: #ffd700;
    color: #181b23;
    padding: 0;
    margin-top: 0;
    font-family: inherit;
    border-top: 4px solid #23273a;
}
.footer-content-realstate {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 32px 24px 18px 24px;
    gap: 32px;
}
.footer-info {
    flex: 1.5;
    display: flex;
    flex-direction: column;
    justify-content: center;      /* Centra verticalmente */
    gap: 28px;                    /* Más espacio entre líneas */
    font-size: 1.25rem;           /* Más grande */
    min-height: 220px;            /* Asegura altura */
    padding-top: 24px;            /* Baja el bloque */
    padding-bottom: 24px;
}

.footer-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    font-size: 1.18rem;           /* Más grande */
}

.footer-item i {
    font-size: 1.7em;             /* Íconos más grandes */
    margin-top: 2px;
    color: #181b23;
}
.footer-center {
    flex: 1.5;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 18px;
    min-width: 320px;
}

.footer-logo {
    width: 90px;
    margin-bottom: 8px;
}

.footer-title {
    font-size: 1.45rem;
    font-weight: bold;
    text-align: center;
    color: #181b23;
    margin-bottom: 10px;
    letter-spacing: 2px;
}

.footer-social-icons {
    display: flex;
    gap: 22px;
    margin-top: 12px;
    justify-content: center;
}

.footer-social {
    background: #23273a;
    color: #ffd700;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2em;
    transition: background 0.2s, color 0.2s;
    border: 2px solid #fff;
}
.footer-social.facebook:hover { background: #3b5998; color: #fff; }
.footer-social.youtube:hover { background: #e53935; color: #fff; }
.footer-social.instagram:hover { background: #8a3ab9; color: #fff; }

.footer-form {
    flex: 1.2;
    background: #f5eaea;
    color: #1a237e;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.10);
    padding: 22px 28px 18px 28px;
    min-width: 260px;
    max-width: 320px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
}
.footer-form-title {
    font-size: 1.1rem;
    font-weight: bold;
    text-align: center;
    color: #1a237e;
    margin-bottom: 12px;
}
.footer-form form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.footer-form label {
    font-weight: bold;
    color: #1a237e;
    margin-bottom: 2px;
    font-size: 0.98rem;
}
.footer-form input,
.footer-form textarea {
    width: 100%;
    padding: 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 0.98rem;
    margin-bottom: 4px;
    resize: none;
}
.footer-form textarea {
    min-height: 48px;
    max-height: 90px;
}
.footer-form button {
    background: #23273a;
    color: #ffd700;
    border: none;
    border-radius: 4px;
    padding: 8px 0;
    font-weight: bold;
    width: 100%;
    margin-top: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.footer-form button:hover {
    background: #ffd700;
    color: #23273a;
    border: 1px solid #23273a;
}
.footer-copyright {
    background: #23273a;
    color: #fff;
    text-align: center;
    padding: 10px 0;
    width: 100vw;
    margin-left: 50%;
    transform: translateX(-50%);
    border-radius: 0;
    font-size: 1rem;
    letter-spacing: 1px;
    font-family: inherit;
}
@media (max-width: 900px) {
    .footer-content-realstate {
        flex-direction: column;
        align-items: center;
        gap: 24px;
        padding: 24px 8px 12px 8px;
    }
    .footer-form {
        min-width: 0;
        max-width: 100%;
        width: 100%;
    }
}
    </style>
</head>
<body class="<?= $config ? 'color-' . $config['color_esquema'] : 'color-azul' ?>">
        <!-- Header -->
      <?php
// Coloca esto antes del header para definir el logo
$logo_navbar = 'images/logo.png';
if ($config && isset($config['logo_navbar']) && $config['logo_navbar'] && file_exists('uploads/logos/' . $config['logo_navbar'])) {
    $logo_navbar = 'uploads/logos/' . $config['logo_navbar'];
}
?>
<header class="header-navbar-realstate">
    <div class="navbar-realstate">
        <!-- IZQUIERDA: Logo y redes en columna -->
       <div class="navbar-left">
    <img src="<?= $logo_navbar ?>" alt="UTH Solutions Logo" class="navbar-logo-img">
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
    </div>
</div>
        <!-- DERECHA: Menú y perfil -->
        <div class="navbar-right-block">
    <div class="navbar-top">
            <a href="login.php" class="login-icon" style="display: flex; align-items: center;">
                <img src="images/perfilLogo.png" alt="Perfil" style="width: 90px; margin-top:-10px; height: 90px;  padding: 1px;">
            </a>
    </div>
    <nav class="navbar-menu-realstate">
        <ul>
            <li><a href="#inicio">INICIO</a></li>
            <li><span class="menu-sep">|</span></li>
            <li><a href="#quienes-somos">QUIENES SOMOS</a></li>
            <li><span class="menu-sep">|</span></li>
            <li><a href="#alquileres">ALQUILERES</a></li>
            <li><span class="menu-sep">|</span></li>
            <li><a href="#ventas">VENTAS</a></li>
            <li><span class="menu-sep">|</span></li>
            <li><a href="#contactenos">CONTACTENOS</a></li>
        </ul>
    </nav>
    <form method="GET" action="index.php" class="navbar-search">
        <input type="text" name="buscar" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
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
        </div>
            <a href="propiedades.php?tipo=<?= $propiedad['tipo'] ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver a <?= ucfirst($propiedad['tipo']) ?>
            </a>
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
       <footer class="footer-realstate">
    <div class="footer-content-realstate">
        <!-- Columna izquierda: datos -->
        <div class="footer-info">
            <div class="footer-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><b>Dirección:</b> Cañas Guanacaste, 100 mts Este<br>Parque de Cañas</span>
            </div>
            <div class="footer-item">
                <i class="fas fa-phone-alt"></i>
                <span><b>Teléfono:</b> 8890-2030</span>
            </div>
            <div class="footer-item">
                <i class="fas fa-envelope"></i>
                <span><b>Email:</b> info@utnrealstate.com</span>
            </div>
        </div>
        <!-- Columna centro: logo y redes -->
        <div class="footer-center">
            <?php
                $logo_footer = 'images/logo.png';
                if (file_exists('uploads/logos/logo_footer_1756111853.png')) {
                    $logo_footer = 'uploads/logos/logo_footer_1756111853.png';
                }
            ?>
            <img src="<?= $logo_footer ?>" alt="Logo" class="footer-logo">
            <div class="footer-title">UTN SOLUTIONS<br>REAL STATE</div>
            <div class="footer-social-icons">
                <a href="<?= $config['facebook_url'] ?? '#' ?>" target="_blank" class="footer-social facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= $config['youtube_url'] ?? '#' ?>" target="_blank" class="footer-social youtube"><i class="fab fa-youtube"></i></a>
                <a href="<?= $config['instagram_url'] ?? '#' ?>" target="_blank" class="footer-social instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <!-- Columna derecha: formulario -->
        <div class="footer-form">
            <div class="footer-form-title">Contactanos</div>
            <form method="post" action="contacto_footer.php">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
                <label>Email:</label>
                <input type="email" name="email" required>
                <label>Teléfono:</label>
                <input type="tel" name="telefono" required>
                <label>Mensaje:</label>
                <textarea name="mensaje" required></textarea>
                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>
    <div class="footer-copyright" style="background-color: #1a237e; padding: 10px 0;">
        <b>Derechos Reservados 2024</b>
    </div>
</footer>
        
</body>
</html>
