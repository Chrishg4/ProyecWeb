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
if ($config && isset($config['banner_imagen']) && $config['banner_imagen'] && file_exists('uploads/banners/' . $config['banner_imagen'])) { 
    echo "    background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('uploads/banners/" . htmlspecialchars($config['banner_imagen']) . "');";
} else {
    echo "    background-image: linear-gradient(rgba(26, 35, 126, 0.8), rgba(26, 35, 126, 0.8)), url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 1200 600\"><defs><pattern id=\"grid\" width=\"40\" height=\"40\" patternUnits=\"userSpaceOnUse\"><path d=\"M 40 0 L 0 0 0 40\" fill=\"none\" stroke=\"rgba(255,255,255,0.1)\" stroke-width=\"1\"/></pattern></defs><rect width=\"1200\" height=\"600\" fill=\"%23303f9f\"/><rect width=\"1200\" height=\"600\" fill=\"url(%23grid)\"/><circle cx=\"600\" cy=\"300\" r=\"150\" fill=\"rgba(255,255,255,0.1)\"/><polygon points=\"600,200 550,350 650,350\" fill=\"rgba(255,215,0,0.3)\"/></svg>');";
}
?>
        }
{
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
    <div class="footer-copyright">
        <b>Derechos Reservados 2024</b>
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
