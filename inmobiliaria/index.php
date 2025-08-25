<?php
require_once 'sesiones.php';
require_once 'conexion.php';

// Manejar mensajes de contacto
$mensaje_contacto = '';
$error_contacto = '';
if (isset($_GET['mensaje_enviado']) && $_GET['mensaje_enviado'] == '1') {
    $mensaje_contacto = $_GET['mensaje'] ?? 'Tu mensaje ha sido enviado correctamente. Te contactaremos pronto.';
}
if (isset($_GET['error_contacto'])) {
    $error_contacto = $_GET['error_contacto'];
}

// Obtener configuración del sitio
try {
    $stmt = $conexion->prepare("SELECT * FROM configuracion_sitio WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch();
} catch(PDOException $e) {
    $config = null;
}

// Obtener propiedades destacadas (últimas 3)
try {
    $stmt = $conexion->prepare("SELECT * FROM propiedades WHERE destacada = 1 AND estado = 'activa' ORDER BY fecha_creacion DESC LIMIT 3");
    $stmt->execute();
    $destacadas = $stmt->fetchAll();
} catch(PDOException $e) {
    $destacadas = [];
}

// Obtener propiedades en venta (últimas 3)
try {
    $stmt = $conexion->prepare("SELECT * FROM propiedades WHERE tipo = 'venta' AND estado = 'activa' ORDER BY fecha_creacion DESC LIMIT 3");
    $stmt->execute();
    $ventas = $stmt->fetchAll();
} catch(PDOException $e) {
    $ventas = [];
}

// Obtener propiedades en alquiler (últimas 3)
try {
    $stmt = $conexion->prepare("SELECT * FROM propiedades WHERE tipo = 'alquiler' AND estado = 'activa' ORDER BY fecha_creacion DESC LIMIT 3");
    $stmt->execute();
    $alquileres = $stmt->fetchAll();
} catch(PDOException $e) {
    $alquileres = [];
}

// Manejar búsqueda
$busqueda = '';
$resultados_busqueda = [];
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $busqueda = sanitizar($_GET['buscar']);
    try {
        $stmt = $conexion->prepare("SELECT * FROM propiedades WHERE (descripcion_breve LIKE ? OR descripcion_larga LIKE ? OR titulo LIKE ?) AND estado = 'activa' ORDER BY fecha_creacion DESC");
        $termino = "%$busqueda%";
        $stmt->execute([$termino, $termino, $termino]);
        $resultados_busqueda = $stmt->fetchAll();
    } catch(PDOException $e) {
        $resultados_busqueda = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($config && $config['nombre_sitio']) ? htmlspecialchars($config['nombre_sitio']) : 'UTH SOLUTIONS REAL STATE' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS dinámico para esquema de colores -->
    <style>
        :root {
            <?php
            $esquema = 'azul'; // por defecto
            if ($config && isset($config['color_esquema']) && !empty($config['color_esquema'])) {
                $esquema = $config['color_esquema'];
            }
            
            switch($esquema) {
                case 'amarillo':
                    echo '--color-primario: #ffd700;';
                    echo '--color-secundario: #333333;';
                    echo '--color-hover: #ffed4e;';
                    break;
                case 'gris':
                    echo '--color-primario: #6b7280;';
                    echo '--color-secundario: #374151;';
                    echo '--color-hover: #9ca3af;';
                    break;
                case 'blanco_gris':
                    echo '--color-primario: #ffffff;';
                    echo '--color-secundario: #f5f5f5;';
                    echo '--color-hover: #e5e5e5;';
                    break;
                case 'azul':
                default:
                    echo '--color-primario: #1a237e;';
                    echo '--color-secundario: #303f9f;';
                    echo '--color-hover: #3f51b5;';
                    break;
            }
            ?>
        }
        
        .navbar {
            background-color: var(--color-primario) !important;
        }
        
        .btn-primary {
            background-color: var(--color-primario) !important;
            border-color: var(--color-primario) !important;
        }
        
        .btn-primary:hover {
            background-color: var(--color-hover) !important;
            border-color: var(--color-hover) !important;
        }
        
        .section-title {
            color: var(--color-primario) !important;
        }
        
        .footer {
            background-color: var(--color-primario) !important;
        }
        
        .property-card:hover {
            border-color: var(--color-primario) !important;
        }
        
        .price {
            color: var(--color-primario) !important;
        }
        
        .navbar-brand {
            color: white !important;
        }
        
        .navbar ul li a:hover {
            color: var(--color-hover) !important;
        }
        
        .ver-detalles {
            background-color: var(--color-primario) !important;
            border-color: var(--color-primario) !important;
        }
        
        .ver-detalles:hover {
            background-color: var(--color-hover) !important;
            border-color: var(--color-hover) !important;
        }
        
        .banner {
            background-color: var(--color-primario);
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            
            <?php 
            // Imagen de fondo personalizable
            if ($config && isset($config['banner_imagen']) && $config['banner_imagen'] && file_exists('uploads/banners/' . $config['banner_imagen'])): 
            ?>
                background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('uploads/banners/<?= htmlspecialchars($config['banner_imagen']) ?>');
            <?php else: ?>
                /* Imagen por defecto - puedes cambiar esta URL por una imagen real */
                background-image: linear-gradient(rgba(26, 35, 126, 0.8), rgba(26, 35, 126, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="1200" height="600" fill="%23303f9f"/><rect width="1200" height="600" fill="url(%23grid)"/><circle cx="600" cy="300" r="150" fill="rgba(255,255,255,0.1)"/><polygon points="600,200 550,350 650,350" fill="rgba(255,215,0,0.3)"/></svg>');
            <?php endif; ?>
            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .banner-content {
            z-index: 2;
            position: relative;
            max-width: 800px;
            padding: 40px 20px;
        }
        
        .banner h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .banner h2 {
                font-size: 1.8rem;
            }
            .banner {
                min-height: 400px;
            }
        }
    </style>
</head>
<body class="<?= ($config && isset($config['color_esquema'])) ? 'color-' . $config['color_esquema'] : 'color-azul' ?>">
    
    <!-- Mensajes de contacto -->
    <?php if ($mensaje_contacto): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; text-align: center; position: relative;">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensaje_contacto) ?>
            <button onclick="this.parentElement.style.display='none'" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #155724; font-size: 18px; cursor: pointer;">×</button>
        </div>
    <?php endif; ?>
    
    <?php if ($error_contacto): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; text-align: center; position: relative;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_contacto) ?>
            <button onclick="this.parentElement.style.display='none'" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #721c24; font-size: 18px; cursor: pointer;">×</button>
        </div>
    <?php endif; ?>
    
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
                        <li><a href="#inicio" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">INICIO</a></li>
                        <li><a href="#quienes-somos" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">QUIENES SOMOS</a></li>
                        <li><a href="#alquileres" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">ALQUILERES</a></li>
                        <li><a href="#ventas" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">VENTAS</a></li>
                        <li><a href="#contactenos" style="color: #ffd700; font-weight: 600; font-size: 1.08rem; letter-spacing: 1px; text-decoration: none; transition: color 0.2s;">CONTÁCTENOS</a></li>
                    </ul>
                </nav>
                <div class="header-right" style="display: flex; align-items: center; gap: 18px;">
                    <form method="GET" action="index.php" style="display: flex; align-items: center; background: #181b23; border-radius: 20px; padding: 2px 10px;">
                        <input type="text" name="buscar" class="search-input" placeholder="Buscar propiedades..." value="<?= htmlspecialchars($busqueda) ?>" style="background: transparent; border: none; color: #fff; outline: none; padding: 6px 8px; font-size: 1rem; width: 120px;">
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

    <?php if (!empty($busqueda)): ?>
    <!-- Resultados de búsqueda -->
    <section class="search-results" style="padding: 40px 0; background: #f8f9fa;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 30px; color: #1a237e;">
                Resultados para: "<?= htmlspecialchars($busqueda) ?>"
            </h2>
            
            <?php if (empty($resultados_busqueda)): ?>
                <p style="text-align: center; font-size: 18px; color: #666;">
                    No se encontraron propiedades que coincidan con tu búsqueda.
                </p>
            <?php else: ?>
                <div class="properties-grid">
                    <?php foreach ($resultados_busqueda as $propiedad): ?>
                        <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                            <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                            <div class="property-content">
                                <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                                <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                                <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                                <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
                                <div style="margin-top: 15px;">
                                    <a href="detalle.php?id=<?= $propiedad['id'] ?>" class="btn" style="padding: 10px 20px; font-size: 14px;">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php else: ?>
    
    <!-- Banner -->
    <section class="banner" id="inicio">
        <div class="banner-content">
            <h2><?= ($config && $config['mensaje_banner']) ? htmlspecialchars($config['mensaje_banner']) : 'PERMÍTENOS AYUDARTE A CUMPLIR TUS SUEÑOS' ?></h2>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="quienes-somos">
        <div class="container">
            <div class="about-content" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="about-text" style="flex: 1;">
                    <h2><?= ($config && $config['titulo_quienes_somos']) ? htmlspecialchars($config['titulo_quienes_somos']) : 'QUIENES SOMOS' ?></h2>
                    <p><?= ($config && $config['descripcion_quienes_somos']) ? nl2br(htmlspecialchars($config['descripcion_quienes_somos'])) : 'Curabitur congue eleifend orci, sit mollit tristram nec. Phasellus vestibulum nibh nisl. Donec eu viverdut nisl. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi pretium erat et orci vehicula, id fringilla lorem tempus. Pellentesque ex libero, luctus quis mauris congue sed vitae rutrum tellus.' ?></p>
                </div>
                <div class="about-image" style="flex: 1; display: flex; justify-content: flex-end;">
                    <?php if ($config && isset($config['imagen_quienes_somos']) && $config['imagen_quienes_somos'] && file_exists('images/' . $config['imagen_quienes_somos'])): ?>
                        <img src="images/<?= htmlspecialchars($config['imagen_quienes_somos']) ?>" alt="Equipo" style="width: 320px; height: 220px; object-fit: cover; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                    <?php else: ?>
                        <div style="background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%); color: white; padding: 40px; border-radius: 16px; text-align: center; min-width: 320px; min-height: 220px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                            <i class="fas fa-users fa-3x"></i>
                            <span style="margin-top: 20px;">Imagen de equipo</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Propiedades Destacadas -->
    <section class="properties-section destacadas">
        <div class="container">
            <h2 class="section-title">PROPIEDADES DESTACADAS</h2>
            <div class="properties-grid">
                <?php foreach ($destacadas as $propiedad): ?>
                    <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                        <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                        <div class="property-content">
                            <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                            <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
                            <div style="margin-top: 15px;">
                                <a href="detalle.php?id=<?= $propiedad['id'] ?>" class="btn" style="padding: 10px 20px; font-size: 14px;">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="ver-mas-btn">
                <a href="propiedades.php?destacadas=1" class="btn">VER MAS...</a>
            </div>
        </div>
    </section>

    <!-- Propiedades en Venta -->
    <section class="properties-section ventas" id="ventas">
        <div class="container">
            <h2 class="section-title">PROPIEDADES EN VENTA</h2>
            <div class="properties-grid">
                <?php foreach ($ventas as $propiedad): ?>
                    <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                        <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                        <div class="property-content">
                            <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                            <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
                            <div style="margin-top: 15px;">
                                <a href="detalle.php?id=<?= $propiedad['id'] ?>" class="btn" style="padding: 10px 20px; font-size: 14px;">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="ver-mas-btn">
                <a href="propiedades.php?tipo=venta" class="btn">VER MAS...</a>
            </div>
        </div>
    </section>

    <!-- Propiedades en Alquiler -->
    <section class="properties-section alquiler" id="alquileres">
        <div class="container">
            <h2 class="section-title">PROPIEDADES EN ALQUILER</h2>
            <div class="properties-grid">
                <?php foreach ($alquileres as $propiedad): ?>
                    <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                        <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                        <div class="property-content">
                            <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                            <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
                            <div style="margin-top: 15px;">
                                <a href="detalle.php?id=<?= $propiedad['id'] ?>" class="btn" style="padding: 10px 20px; font-size: 14px;">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="ver-mas-btn">
                <a href="propiedades.php?tipo=alquiler" class="btn">VER MAS...</a>
            </div>
        </div>
    </section>
    
    <?php endif; ?>

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

    <script>
        // Smooth scrolling para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
