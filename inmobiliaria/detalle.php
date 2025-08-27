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
                        <strong>Descripción corta:</strong><br>
                        <?= nl2br(htmlspecialchars($propiedad['descripcion_breve'])) ?>
                        <br><br>
                        <strong>Descripción completa:</strong><br>
                        <?= nl2br(htmlspecialchars($propiedad['descripcion_larga'])) ?>
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
                
                <!-- Mapa de Google Maps debajo del agente (solo en sidebar) -->
                
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
                <!-- Google Maps Card SIEMPRE visible debajo del agente -->
                <div class="map-section" style="margin-top: 30px;">
                    <h2>Ubicación</h2>
                    <div class="map-card" style="background: #fff; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 16px; margin-bottom: 24px; display: flex; flex-direction: column; align-items: center;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15696.4077398779!2d-85.0960567!3d10.41347835!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses!2scr!4v1756255848026!5m2!1ses!2scr" width="100%" height="350" style="border:0; border-radius: 12px; min-width: 280px; max-width: 600px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
        </div>
            <a href="propiedades.php?tipo=<?= $propiedad['tipo'] ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver a <?= ucfirst($propiedad['tipo']) ?>
            </a>
        </div>
    <!-- Se eliminó la sección de propiedades similares -->
    </div>

  
        
</body>
</html>
