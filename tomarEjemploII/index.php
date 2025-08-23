<?php
require_once 'config.php';

// Obtener filtro de provincia si existe
$filtro_provincia = $_GET['provincia'] ?? 'Todos';

// Obtener todas las empresas activas con filtro opcional
try {
    if ($filtro_provincia === 'Todos') {
        $query = "SELECT * FROM empresas WHERE activo = 1 ORDER BY fecha_registro DESC";
        $result = $conexion->query($query);
    } else {
        $query = "SELECT * FROM empresas WHERE activo = 1 AND provincia = ? ORDER BY fecha_registro DESC";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $filtro_provincia);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    $empresas = $result->fetch_all(MYSQLI_ASSOC);
} catch(Exception $e) {
    $empresas = [];
    $error = "Error al cargar las empresas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Costa Rica</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Servicios Costa Rica</h1>
                <a href="admin.php" class="btn-admin">Administrar</a>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Filtros por provincia -->
        <nav class="provincias-nav">
            <a href="index.php?provincia=Todos" class="<?php echo $filtro_provincia === 'Todos' ? 'active' : ''; ?>">Todos</a>
            <a href="index.php?provincia=Guanacaste" class="<?php echo $filtro_provincia === 'Guanacaste' ? 'active' : ''; ?>">Guanacaste</a>
            <a href="index.php?provincia=Puntarenas" class="<?php echo $filtro_provincia === 'Puntarenas' ? 'active' : ''; ?>">Puntarenas</a>
            <a href="index.php?provincia=Heredia" class="<?php echo $filtro_provincia === 'Heredia' ? 'active' : ''; ?>">Heredia</a>
            <a href="index.php?provincia=Cartago" class="<?php echo $filtro_provincia === 'Cartago' ? 'active' : ''; ?>">Cartago</a>
            <a href="index.php?provincia=San José" class="<?php echo $filtro_provincia === 'San José' ? 'active' : ''; ?>">San José</a>
            <a href="index.php?provincia=Limón" class="<?php echo $filtro_provincia === 'Limón' ? 'active' : ''; ?>">Limón</a>
            <a href="index.php?provincia=Alajuela" class="<?php echo $filtro_provincia === 'Alajuela' ? 'active' : ''; ?>">Alajuela</a>
        </nav>

        <?php if (isset($error)): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Grid de empresas -->
        <section class="empresas-grid">
            <?php if (empty($empresas)): ?>
                <div class="no-empresas">
                    <h3>No hay empresas registradas</h3>
                    <p>No se encontraron empresas para esta provincia</p>
                </div>
            <?php else: ?>
                <?php foreach ($empresas as $empresa): ?>
                    <div class="empresa-card">
                        <div class="empresa-image">
                            <img src="images/<?php echo htmlspecialchars($empresa['imagen1']); ?>" 
                                 alt="<?php echo htmlspecialchars($empresa['nombre']); ?>"
                                 onerror="this.src='images/default.jpg'">
                        </div>
                        <div class="empresa-content">
                            <h3><?php echo htmlspecialchars($empresa['nombre']); ?></h3>
                            <p class="direccion">
                                <strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion']); ?>
                            </p>
                            <p class="telefono">
                                <strong>Teléfono:</strong> <?php echo htmlspecialchars($empresa['telefono']); ?>
                            </p>
                            <div class="empresa-actions">
                                <a href="detalle.php?id=<?php echo $empresa['id']; ?>" class="btn-ver-mas">
                                    Ver más
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
