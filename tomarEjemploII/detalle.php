<?php
require_once 'config.php';

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Obtener los datos de la empresa
try {
    $query = "SELECT * FROM empresas WHERE id = ? AND activo = 1";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empresa = $result->fetch_assoc();
    
    if (!$empresa) {
        header('Location: index.php');
        exit;
    }
} catch(Exception $e) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($empresa['nombre']); ?> - Servicios Costa Rica</title>
    <link rel="stylesheet" href="styles/detalle.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="btn-regresar">Regresar</a>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Información principal de la empresa -->
        <section class="empresa-info">
            <div class="empresa-header">
                <div class="empresa-image-main">
                    <img src="images/<?php echo htmlspecialchars($empresa['imagen1']); ?>" 
                         alt="<?php echo htmlspecialchars($empresa['nombre']); ?>"
                         onerror="this.src='images/default.jpg'">
                </div>
                <div class="empresa-details">
                    <h1><?php echo htmlspecialchars($empresa['nombre']); ?></h1>
                    <p class="direccion">
                        <strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion']); ?>
                    </p>
                    <p class="telefono">
                        <strong>Teléfono:</strong> <?php echo htmlspecialchars($empresa['telefono']); ?>
                    </p>
                    <?php if (!empty($empresa['pagina_web'])): ?>
                        <p class="sitio-web">
                            <strong>Página web:</strong> 
                            <a href="<?php echo htmlspecialchars($empresa['pagina_web']); ?>" target="_blank">
                                <?php echo htmlspecialchars($empresa['pagina_web']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Servicios -->
        <section class="servicios">
            <h2>Servicios</h2>
            
            <?php if (!empty($empresa['servicio1_nombre'])): ?>
                <div class="servicio">
                    <h3><?php echo htmlspecialchars($empresa['servicio1_nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($empresa['servicio1_detalle']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($empresa['servicio2_nombre'])): ?>
                <div class="servicio">
                    <h3><?php echo htmlspecialchars($empresa['servicio2_nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($empresa['servicio2_detalle']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($empresa['servicio3_nombre'])): ?>
                <div class="servicio">
                    <h3><?php echo htmlspecialchars($empresa['servicio3_nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($empresa['servicio3_detalle']); ?></p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Galería -->
        <section class="galeria">
            <h2>Galería</h2>
            <div class="galeria-grid">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if (!empty($empresa["imagen$i"])): ?>
                        <div class="galeria-item">
                            <img src="images/<?php echo htmlspecialchars($empresa["imagen$i"]); ?>" 
                                 alt="Galería <?php echo htmlspecialchars($empresa['nombre']); ?>"
                                 onerror="this.src='images/default.jpg'">
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </section>
    </main>
</body>
</html>
