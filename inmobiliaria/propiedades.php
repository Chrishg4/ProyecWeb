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
// Definir variable de búsqueda para evitar warning
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?> - <?= $config ? htmlspecialchars($config['nombre_sitio']) : 'UTH SOLUTIONS REAL STATE' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
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
    <form method="GET" action="propiedades.php" class="navbar-search">
        <input type="text" name="buscar" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
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

