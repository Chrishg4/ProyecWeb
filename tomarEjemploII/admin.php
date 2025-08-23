<?php
require_once 'config.php';

// Obtener todas las empresas para administración
try {
    $query = "SELECT * FROM empresas ORDER BY fecha_registro DESC";
    $result = $conexion->query($query);
    $empresas = $result->fetch_all(MYSQLI_ASSOC);
} catch(Exception $e) {
    $empresas = [];
    $error = "Error al cargar las empresas: " . $e->getMessage();
}

// Manejar mensajes de éxito/error
$mensaje = '';
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'agregado':
            $mensaje = '<div class="success">Empresa agregada exitosamente</div>';
            break;
        case 'actualizado':
            $mensaje = '<div class="success">Empresa actualizada exitosamente</div>';
            break;
        case 'eliminado':
            $mensaje = '<div class="success">Empresa eliminada exitosamente</div>';
            break;
        case 'error':
            $mensaje = '<div class="error">Ocurrió un error al procesar la solicitud</div>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Servicios Costa Rica</title>
        <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <a href="index.php">Ver sitio público</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php echo $mensaje; ?>

        <!-- Formulario para agregar empresa (siempre visible) -->
        <section class="formulario-section">
            <h2>Agregar Nueva Empresa</h2>
            <form method="POST" action="agregar.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre de la empresa *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección *</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono">
                    </div>
                    <div class="form-group">
                        <label for="pagina_web">Página web</label>
                        <input type="url" id="pagina_web" name="pagina_web">
                    </div>
                </div>

                <div class="form-group">
                    <label for="provincia">Provincia *</label>
                    <select id="provincia" name="provincia" required>
                        <option value="">Seleccionar provincia</option>
                        <option value="San José">San José</option>
                        <option value="Alajuela">Alajuela</option>
                        <option value="Cartago">Cartago</option>
                        <option value="Heredia">Heredia</option>
                        <option value="Guanacaste">Guanacaste</option>
                        <option value="Puntarenas">Puntarenas</option>
                        <option value="Limón">Limón</option>
                    </select>
                </div>

                <!-- Servicios -->
                <h3>Servicios Ofrecidos</h3>
                
                <div class="form-group">
                    <label for="servicio1_nombre">Servicio 1 - Nombre</label>
                    <input type="text" id="servicio1_nombre" name="servicio1_nombre">
                </div>
                <div class="form-group">
                    <label for="servicio1_detalle">Servicio 1 - Detalle</label>
                    <textarea id="servicio1_detalle" name="servicio1_detalle" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="servicio2_nombre">Servicio 2 - Nombre</label>
                    <input type="text" id="servicio2_nombre" name="servicio2_nombre">
                </div>
                <div class="form-group">
                    <label for="servicio2_detalle">Servicio 2 - Detalle</label>
                    <textarea id="servicio2_detalle" name="servicio2_detalle" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="servicio3_nombre">Servicio 3 - Nombre</label>
                    <input type="text" id="servicio3_nombre" name="servicio3_nombre">
                </div>
                <div class="form-group">
                    <label for="servicio3_detalle">Servicio 3 - Detalle</label>
                    <textarea id="servicio3_detalle" name="servicio3_detalle" rows="3"></textarea>
                </div>

                <!-- Galería -->
                <h3>Galería de Imágenes (5 imágenes)</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="imagen1">Imagen 1 (principal)</label>
                        <input type="file" id="imagen1" name="imagen1" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="imagen2">Imagen 2</label>
                        <input type="file" id="imagen2" name="imagen2" accept="image/*">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="imagen3">Imagen 3</label>
                        <input type="file" id="imagen3" name="imagen3" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="imagen4">Imagen 4</label>
                        <input type="file" id="imagen4" name="imagen4" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagen5">Imagen 5</label>
                    <input type="file" id="imagen5" name="imagen5" accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar empresa</button>
                </div>
            </form>
        </section>

        <!-- Lista de empresas -->
        <section class="empresas-admin">
            <h2>Empresas registradas (<?php echo count($empresas); ?>)</h2>
            
            <?php if (empty($empresas)): ?>
                <div class="no-empresas">
                    <h3>No hay empresas registradas</h3>
                    <p>Agrega la primera empresa usando el botón "Agregar empresa"</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="empresas-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Provincia</th>
                                <th>Teléfono</th>
                                <th>Página Web</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($empresas as $empresa): ?>
                                <tr>
                                    <td><?php echo $empresa['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($empresa['nombre']); ?></strong>
                                        <br><small><?php echo htmlspecialchars($empresa['direccion']); ?></small>
                                    </td>
                                    <td><span class="provincia-tag"><?php echo htmlspecialchars($empresa['provincia']); ?></span></td>
                                    <td><?php echo htmlspecialchars($empresa['telefono']); ?></td>
                                    <td>
                                        <?php if (!empty($empresa['pagina_web'])): ?>
                                            <a href="<?php echo htmlspecialchars($empresa['pagina_web']); ?>" target="_blank">
                                                <?php echo htmlspecialchars($empresa['pagina_web']); ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="estado <?php echo $empresa['activo'] ? 'activo' : 'inactivo'; ?>">
                                            <?php echo $empresa['activo'] ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td class="acciones">
                                        <a href="editar.php?id=<?php echo $empresa['id']; ?>" class="btn-edit" title="Editar">✏️ Editar</a>
                                        <a href="eliminar.php?id=<?php echo $empresa['id']; ?>" 
                                           onclick="return confirm('¿Estás seguro de eliminar <?php echo htmlspecialchars($empresa['nombre']); ?>?')"
                                           class="btn-delete" title="Eliminar">🗑️ Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
