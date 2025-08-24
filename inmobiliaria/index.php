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
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <?php 
                $logo_navbar = 'images/logo.png'; // por defecto
                if ($config && $config['logo_navbar'] && file_exists('uploads/logos/' . $config['logo_navbar'])) {
                    $logo_navbar = 'uploads/logos/' . $config['logo_navbar'];
                }
                ?>
                <img src="<?= $logo_navbar ?>" alt="UTH Solutions Logo" class="logo-img">
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <li><a href="#inicio">INICIO</a></li>
                    <li><a href="#quienes-somos">QUIENES SOMOS</a></li>
                    <li><a href="#alquileres">ALQUILERES</a></li>
                    <li><a href="#ventas">VENTAS</a></li>
                    <li><a href="#contactenos">CONTÁCTENOS</a></li>
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
            <div class="about-content">
                <div class="about-text">
                    <h2><?= ($config && $config['titulo_quienes_somos']) ? htmlspecialchars($config['titulo_quienes_somos']) : 'QUIENES SOMOS' ?></h2>
                    <p><?= ($config && $config['descripcion_quienes_somos']) ? nl2br(htmlspecialchars($config['descripcion_quienes_somos'])) : 'Curabitur congue eleifend orci, sit mollit tristram nec. Phasellus vestibulum nibh nisl. Donec eu viverdut nisl. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi pretium erat et orci vehicula, id fringilla lorem tempus. Pellentesque ex libero, luctus quis mauris congue sed vitae rutrum tellus.' ?></p>
                </div>
                <div class="about-image">
                    <?php if ($config && isset($config['imagen_quienes_somos']) && $config['imagen_quienes_somos'] && file_exists('images/' . $config['imagen_quienes_somos'])): ?>
                        <img src="images/<?= htmlspecialchars($config['imagen_quienes_somos']) ?>" alt="Equipo">
                    <?php else: ?>
                        <div style="background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%); color: white; padding: 40px; border-radius: 10px; text-align: center; min-height: 300px; display: flex; flex-direction: column; justify-content: center;">
                            <i class="fas fa-users" style="font-size: 64px; margin-bottom: 20px; opacity: 0.8;"></i>
                            <h3 style="margin-bottom: 10px; font-size: 24px;">Nuestro Equipo</h3>
                            <p style="opacity: 0.9; margin: 0;">Profesionales dedicados a ayudarte</p>
                            <small style="opacity: 0.7; margin-top: 15px;">Puedes subir una imagen desde el panel de administración</small>
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
    <footer class="footer" id="contactenos">
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
                    <div class="footer-logo">
                        <?php 
                        $logo_footer = 'images/logo.png'; // por defecto
                        if ($config && $config['logo_footer'] && file_exists('uploads/logos/' . $config['logo_footer'])) {
                            $logo_footer = 'uploads/logos/' . $config['logo_footer'];
                        }
                        ?>
                        <img src="<?= $logo_footer ?>" alt="UTH Solutions Logo" class="footer-logo-img" style="width: 90px; height: 90px;">
                    </div>
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
