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
    <footer class="footer">
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
        // Gallery functionality
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.addEventListener('click', function() {
                const imageUrl = this.dataset.image;
                document.getElementById('mainImage').style.backgroundImage = `url('${imageUrl}')`;
                
                // Update active state
                document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Smooth scrolling
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
