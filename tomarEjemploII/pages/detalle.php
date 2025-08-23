<?php
require_once '../config.php';

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$id = (int)$_GET['id'];

// Obtener los datos de la empresa
try {
    $pdo = conectarDB();
    $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        header('Location: ../index.php');
        exit;
    }
} catch(PDOException $e) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($empresa['nombre']); ?> - Servicios Costa Rica</title>
    <link rel="stylesheet" href="../styles/detalle.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="../index.php" class="btn-back">← Volver al directorio</a>
                <a href="admin.php">Administración</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="empresa-detalle">
            <div class="empresa-header">
                <div class="empresa-image-large">
                    <img src="../images/<?php echo htmlspecialchars($empresa['imagen']); ?>" 
                         alt="<?php echo htmlspecialchars($empresa['nombre']); ?>"
                         onerror="this.src='../images/default.jpg'">
                </div>
                <div class="empresa-info">
                    <h1><?php echo htmlspecialchars($empresa['nombre']); ?></h1>
                    <span class="categoria-badge"><?php echo htmlspecialchars($empresa['categoria']); ?></span>
                    <p class="descripcion"><?php echo nl2br(htmlspecialchars($empresa['descripcion'])); ?></p>
                </div>
            </div>

            <div class="contacto-section">
                <h2>Información de contacto</h2>
                <div class="contacto-grid">
                    <?php if (!empty($empresa['telefono'])): ?>
                        <div class="contacto-item">
                            <strong>📞 Teléfono:</strong>
                            <a href="tel:<?php echo htmlspecialchars($empresa['telefono']); ?>">
                                <?php echo htmlspecialchars($empresa['telefono']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($empresa['email'])): ?>
                        <div class="contacto-item">
                            <strong>✉️ Email:</strong>
                            <a href="mailto:<?php echo htmlspecialchars($empresa['email']); ?>">
                                <?php echo htmlspecialchars($empresa['email']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($empresa['direccion'])): ?>
                        <div class="contacto-item">
                            <strong>📍 Dirección:</strong>
                            <span><?php echo htmlspecialchars($empresa['direccion']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($empresa['sitio_web'])): ?>
                        <div class="contacto-item">
                            <strong>🌐 Sitio web:</strong>
                            <a href="<?php echo htmlspecialchars($empresa['sitio_web']); ?>" target="_blank">
                                <?php echo htmlspecialchars($empresa['sitio_web']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="actions">
                <a href="../index.php" class="btn-secondary">Volver al directorio</a>
                <a href="mailto:<?php echo htmlspecialchars($empresa['email']); ?>" class="btn-primary">
                    Contactar empresa
                </a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Servicios Costa Rica. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
