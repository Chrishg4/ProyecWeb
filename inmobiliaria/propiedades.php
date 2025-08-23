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
                    <li><a href="index.php#quienes-somos">QUIENES SOMOS</a></li>
                    <li><a href="propiedades.php?tipo=alquiler">ALQUILERES</a></li>
                    <li><a href="propiedades.php?tipo=venta">VENTAS</a></li>
                    <li><a href="index.php#contacto">CONTÁCTENOS</a></li>
                </ul>
            </nav>
            
            <div class="header-right">
                <div class="search-container">
                    <form method="GET" action="index.php" style="display: flex;">
                        <input type="text" name="buscar" class="search-input" placeholder="Buscar propiedades...">
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
