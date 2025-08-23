<?php
require_once '../config.php';

// Obtener todas las empresas para administración
try {
    $pdo = conectarDB();
    $stmt = $pdo->query("SELECT * FROM empresas ORDER BY fecha_registro DESC");
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
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
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🔧 Panel de Administración</h1>
            <nav>
                <a href="../index.php">Ver sitio público</a>
                <a href="#" onclick="mostrarFormulario()">➕ Agregar empresa</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php echo $mensaje; ?>

        <!-- Formulario para agregar/editar empresa -->
        <div id="formulario-empresa" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="cerrarFormulario()">&times;</span>
                <h2 id="titulo-formulario">Agregar Nueva Empresa</h2>
                <form id="form-empresa" action="procesar.php" method="POST">
                    <input type="hidden" id="empresa-id" name="id" value="">
                    <input type="hidden" id="accion" name="accion" value="agregar">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre de la empresa *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoría *</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="Tecnología">Tecnología</option>
                            <option value="Turismo">Turismo</option>
                            <option value="Legal">Legal</option>
                            <option value="Diseño">Diseño</option>
                            <option value="Reparaciones">Reparaciones</option>
                            <option value="Salud">Salud</option>
                            <option value="Educación">Educación</option>
                            <option value="Construcción">Construcción</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sitio_web">Sitio web</label>
                            <input type="url" id="sitio_web" name="sitio_web">
                        </div>
                        <div class="form-group">
                            <label for="imagen">Imagen (nombre del archivo)</label>
                            <input type="text" id="imagen" name="imagen" placeholder="ej: empresa.jpg">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" onclick="cerrarFormulario()" class="btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar empresa</button>
                    </div>
                </form>
            </div>
        </div>

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
                                <th>Categoría</th>
                                <th>Email</th>
                                <th>Teléfono</th>
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
                                        <br><small><?php echo substr(htmlspecialchars($empresa['descripcion']), 0, 50) . '...'; ?></small>
                                    </td>
                                    <td><span class="categoria-tag"><?php echo htmlspecialchars($empresa['categoria']); ?></span></td>
                                    <td><?php echo htmlspecialchars($empresa['email']); ?></td>
                                    <td><?php echo htmlspecialchars($empresa['telefono']); ?></td>
                                    <td>
                                        <span class="estado <?php echo $empresa['activo'] ? 'activo' : 'inactivo'; ?>">
                                            <?php echo $empresa['activo'] ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td class="acciones">
                                        <button onclick="editarEmpresa(<?php echo htmlspecialchars(json_encode($empresa)); ?>)" 
                                                class="btn-edit" title="Editar">✏️</button>
                                        <button onclick="eliminarEmpresa(<?php echo $empresa['id']; ?>, '<?php echo htmlspecialchars($empresa['nombre']); ?>')" 
                                                class="btn-delete" title="Eliminar">🗑️</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function mostrarFormulario() {
            document.getElementById('formulario-empresa').style.display = 'block';
            document.getElementById('titulo-formulario').textContent = 'Agregar Nueva Empresa';
            document.getElementById('form-empresa').reset();
            document.getElementById('empresa-id').value = '';
            document.getElementById('accion').value = 'agregar';
        }

        function cerrarFormulario() {
            document.getElementById('formulario-empresa').style.display = 'none';
        }

        function editarEmpresa(empresa) {
            document.getElementById('formulario-empresa').style.display = 'block';
            document.getElementById('titulo-formulario').textContent = 'Editar Empresa';
            document.getElementById('empresa-id').value = empresa.id;
            document.getElementById('accion').value = 'actualizar';
            document.getElementById('nombre').value = empresa.nombre;
            document.getElementById('categoria').value = empresa.categoria;
            document.getElementById('descripcion').value = empresa.descripcion;
            document.getElementById('telefono').value = empresa.telefono;
            document.getElementById('email').value = empresa.email;
            document.getElementById('direccion').value = empresa.direccion;
            document.getElementById('sitio_web').value = empresa.sitio_web;
            document.getElementById('imagen').value = empresa.imagen;
        }

        function eliminarEmpresa(id, nombre) {
            if (confirm('¿Estás seguro de que deseas eliminar la empresa "' + nombre + '"?')) {
                window.location.href = 'procesar.php?accion=eliminar&id=' + id;
            }
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('formulario-empresa');
            if (event.target == modal) {
                cerrarFormulario();
            }
        }
    </script>
</body>
</html>
