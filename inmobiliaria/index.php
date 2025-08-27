<?php
require_once 'sesiones.php';
require_once 'conexion.php';
require_once 'includes/configuracion_funciones.php';

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
            if ($config && !empty($config['color_primario']) && !empty($config['color_secundario'])) {
                echo '--color-primario: ' . htmlspecialchars($config['color_primario']) . ';';
                echo '--color-secundario: ' . htmlspecialchars($config['color_secundario']) . ';';
                echo '--color-hover: ' . htmlspecialchars($config['color_secundario']) . ';';
            } else {
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
            }
            ?>
        }
        
        .navbar {
            background-color: var(--color-primario) !important;
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
        <a href="<?= $config['facebook_url'] ?? '#' ?>" class="facebook" target="_blank" style="color: #fff; font-size: 1.2rem;">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="<?= $config['youtube_url'] ?? '#' ?>" class="youtube" target="_blank" style="color: #fff; font-size: 1.2rem;">
            <i class="fab fa-youtube"></i>
        </a>
        <a href="<?= $config['instagram_url'] ?? '#' ?>" class="instagram" target="_blank" style="color: #fff; font-size: 1.2rem;">
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
                        <img src="images/<?= htmlspecialchars($config['imagen_quienes_somos']) ?>" alt="Equipo" style="width: 520px; height: 320px; object-fit: cover; border: none; box-shadow: none; border-radius: 0;">
                    <?php else: ?>
                        <div style="background: #eaeaea; color: #333; padding: 40px; border: none; text-align: center; min-width: 520px; min-height: 320px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: none; border-radius: 0;">
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
            <h2 class="section-title" style="color: #fff;">PROPIEDADES DESTACADAS</h2>
            <div class="properties-grid">
                <?php foreach ($destacadas as $propiedad): ?>
                    <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                        <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                        <div class="property-content">
                            <h3 class="property-title" style="color: #fff;"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description" style="color: #fff;"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price" style="text-align: center; color: #ffd700;">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>

                        
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
            <h2 class="section-title" style="color: #0e2341;">PROPIEDADES EN VENTA</h2>
            <div class="properties-grid">
                <?php foreach ($ventas as $propiedad): ?>
                    <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                        <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                        <div class="property-content">
                            <h3 class="property-title" style="color: #0e2341;"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description" style="color: #666;"> <?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price" style="text-align: center; color: #1a237e;">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>

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
            <h2 class="section-title" style="color: #fff;">PROPIEDADES EN ALQUILER</h2>
            <div class="properties-grid">
                <?php foreach ($alquileres as $propiedad): ?>
                    <div class="property-card" onclick="window.location.href='detalle.php?id=<?= $propiedad['id'] ?>'" style="cursor: pointer;">
                        <div class="property-image" <?php if ($propiedad['imagen_destacada'] && file_exists('images/' . $propiedad['imagen_destacada'])): ?>style="background-image: url('images/<?= htmlspecialchars($propiedad['imagen_destacada']) ?>');"<?php endif; ?>></div>
                        <div class="property-content">
                            <h3 class="property-title" style="color: #fff;"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="property-description" style="color: #f8e5e5ff;"><?= htmlspecialchars($propiedad['descripcion_breve']) ?></p>
                            <div class="property-price" style="text-align: center; color: #ffd700;">Precio: $<?= number_format($propiedad['precio'], 0, '.', ',') ?></div>
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
                $logo_footer = obtenerLogoFooter($config);
            ?>
            <img src="<?= $logo_footer ?>" alt="Logo" class="footer-logo">
            
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
