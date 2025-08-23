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
    <title><?= $config ? htmlspecialchars($config['nombre_sitio']) : 'UTH SOLUTIONS REAL STATE' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?= $config ? 'color-' . $config['color_esquema'] : 'color-azul' ?>">
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="logo-text">
                    <h1><?= $config ? htmlspecialchars($config['nombre_sitio']) : 'UTH SOLUTIONS' ?></h1>
                    <p>REAL STATE</p>
                </div>
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php">INICIO</a></li>
                    <li><a href="#quienes-somos">QUIENES SOMOS</a></li>
                    <li><a href="propiedades.php?tipo=alquiler">ALQUILERES</a></li>
                    <li><a href="propiedades.php?tipo=venta">VENTAS</a></li>
                    <li><a href="#contacto">CONTÁCTENOS</a></li>
                </ul>
            </nav>
            
            <div class="header-right">
                <div class="search-container">
                    <form method="GET" action="index.php" style="display: flex;">
                        <input type="text" name="buscar" class="search-input" placeholder="Buscar propiedades..." value="<?= htmlspecialchars($busqueda) ?>">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <div class="social-icons">
                    <a href="<?= $config['facebook_url'] ?? '#' ?>" class="facebook" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="<?= $config['youtube_url'] ?? '#' ?>" class="youtube" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="<?= $config['instagram_url'] ?? '#' ?>" class="instagram" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="login.php" class="login-icon">
                        <i class="fas fa-user"></i>
                        <span class="notification-badge">4</span>
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
                        <div class="property-card">
                            <div class="property-image" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');"></div>
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
    <section class="banner">
        <div class="banner-content">
            <h2><?= $config ? htmlspecialchars($config['mensaje_banner']) : 'PERMÍTENOS AYUDARTE A CUMPLIR TUS SUEÑOS' ?></h2>
            <div class="banner-house">
                <div class="house-3d"></div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="quienes-somos">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2><?= $config ? htmlspecialchars($config['titulo_quienes_somos']) : 'QUIENES SOMOS' ?></h2>
                    <p><?= $config ? nl2br(htmlspecialchars($config['descripcion_quienes_somos'])) : 'Curabitur congue eleifend orci, sit mollit tristram nec. Phasellus vestibulum nibh nisl. Donec eu viverdut nisl. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi pretium erat et orci vehicula, id fringilla lorem tempus. Pellentesque ex libero, luctus quis mauris congue sed vitae rutrum tellus.' ?></p>
                </div>
                <div class="about-image">
                    <img src="images/<?= $config && $config['imagen_quienes_somos'] ? htmlspecialchars($config['imagen_quienes_somos']) : 'team-member.jpg' ?>" alt="Equipo">
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
                    <div class="property-card">
                        <div class="property-image" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');"></div>
                        <div class="property-content">
                            <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                            <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
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
    <section class="properties-section ventas">
        <div class="container">
            <h2 class="section-title">PROPIEDADES EN VENTA</h2>
            <div class="properties-grid">
                <?php foreach ($ventas as $propiedad): ?>
                    <div class="property-card">
                        <div class="property-image" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');"></div>
                        <div class="property-content">
                            <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                            <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
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
    <section class="properties-section alquiler">
        <div class="container">
            <h2 class="section-title">PROPIEDADES EN ALQUILER</h2>
            <div class="properties-grid">
                <?php foreach ($alquileres as $propiedad): ?>
                    <div class="property-card">
                        <div class="property-image" style="background-image: url('images/<?= $propiedad['imagen_destacada'] ? htmlspecialchars($propiedad['imagen_destacada']) : 'default-house.jpg' ?>');"></div>
                        <div class="property-content">
                            <h3 class="property-title"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
                            <div class="property-location"><?= htmlspecialchars($propiedad['ubicacion']) ?></div>
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
    <footer class="footer" id="contacto">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Dirección</h3>
                    <p><?= $config ? htmlspecialchars($config['direccion']) : 'Calles Guanacaste, 100 mts Este' ?></p>
                    <h3 style="margin-top: 20px;">Teléfono</h3>
                    <p><?= $config ? htmlspecialchars($config['telefono_contacto']) : '8800-2030' ?></p>
                    <h3 style="margin-top: 20px;">Email</h3>
                    <p><?= $config ? htmlspecialchars($config['email_contacto']) : 'info@uthrealestate.com' ?></p>
                </div>
                
                <div class="footer-section">
                    <h3>UTH SOLUTIONS</h3>
                    <p>REAL STATE</p>
                    <div class="footer-social">
                        <a href="<?= $config['facebook_url'] ?? '#' ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= $config['youtube_url'] ?? '#' ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="<?= $config['instagram_url'] ?? '#' ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Contáctenos</h3>
                    <form class="contact-form" action="contacto.php" method="POST">
                        <input type="text" name="nombre" placeholder="Nombre:" required>
                        <input type="email" name="email" placeholder="Email:" required>
                        <input type="tel" name="telefono" placeholder="Teléfono:" required>
                        <textarea name="mensaje" placeholder="Mensaje:" rows="4" required></textarea>
                        <button type="submit">Enviar</button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>Derechos Reservados 2024</p>
            </div>
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
