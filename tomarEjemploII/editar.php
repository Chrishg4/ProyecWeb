<?php
include 'config.php';

// Si es GET, mostrar formulario de editar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $empresa = $conexion->query("SELECT * FROM empresas WHERE id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Empresa</title>
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Editar Empresa</h1>
            <nav>
                <a href="admin.php">Volver al panel</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <section class="formulario-section">
            <h2>Editar: <?php echo $empresa['nombre']; ?></h2>
            <form method="POST" action="editar.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $empresa['id']; ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre de la empresa *</label>
                    <input type="text" name="nombre" value="<?php echo $empresa['nombre']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección *</label>
                    <input type="text" name="direccion" value="<?php echo $empresa['direccion']; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" value="<?php echo $empresa['telefono']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="pagina_web">Página web</label>
                        <input type="url" name="pagina_web" value="<?php echo $empresa['pagina_web']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="provincia">Provincia *</label>
                    <select name="provincia" required>
                        <option value="San José" <?php if($empresa['provincia']=='San José') echo 'selected'; ?>>San José</option>
                        <option value="Alajuela" <?php if($empresa['provincia']=='Alajuela') echo 'selected'; ?>>Alajuela</option>
                        <option value="Cartago" <?php if($empresa['provincia']=='Cartago') echo 'selected'; ?>>Cartago</option>
                        <option value="Heredia" <?php if($empresa['provincia']=='Heredia') echo 'selected'; ?>>Heredia</option>
                        <option value="Guanacaste" <?php if($empresa['provincia']=='Guanacaste') echo 'selected'; ?>>Guanacaste</option>
                        <option value="Puntarenas" <?php if($empresa['provincia']=='Puntarenas') echo 'selected'; ?>>Puntarenas</option>
                        <option value="Limón" <?php if($empresa['provincia']=='Limón') echo 'selected'; ?>>Limón</option>
                    </select>
                </div>

                <h3>Servicios Ofrecidos</h3>
                
                <div class="form-group">
                    <label>Servicio 1 - Nombre</label>
                    <input type="text" name="servicio1_nombre" value="<?php echo $empresa['servicio1_nombre']; ?>">
                </div>
                <div class="form-group">
                    <label>Servicio 1 - Detalle</label>
                    <textarea name="servicio1_detalle" rows="3"><?php echo $empresa['servicio1_detalle']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Servicio 2 - Nombre</label>
                    <input type="text" name="servicio2_nombre" value="<?php echo $empresa['servicio2_nombre']; ?>">
                </div>
                <div class="form-group">
                    <label>Servicio 2 - Detalle</label>
                    <textarea name="servicio2_detalle" rows="3"><?php echo $empresa['servicio2_detalle']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Servicio 3 - Nombre</label>
                    <input type="text" name="servicio3_nombre" value="<?php echo $empresa['servicio3_nombre']; ?>">
                </div>
                <div class="form-group">
                    <label>Servicio 3 - Detalle</label>
                    <textarea name="servicio3_detalle" rows="3"><?php echo $empresa['servicio3_detalle']; ?></textarea>
                </div>

                <h3>Galería de Imágenes</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Imagen 1 (principal)</label>
                        <input type="file" name="imagen1" accept="image/*">
                        <small>Actual: <?php echo $empresa['imagen1']; ?></small>
                    </div>
                    <div class="form-group">
                        <label>Imagen 2</label>
                        <input type="file" name="imagen2" accept="image/*">
                        <small>Actual: <?php echo $empresa['imagen2']; ?></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Imagen 3</label>
                        <input type="file" name="imagen3" accept="image/*">
                        <small>Actual: <?php echo $empresa['imagen3']; ?></small>
                    </div>
                    <div class="form-group">
                        <label>Imagen 4</label>
                        <input type="file" name="imagen4" accept="image/*">
                        <small>Actual: <?php echo $empresa['imagen4']; ?></small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Imagen 5</label>
                    <input type="file" name="imagen5" accept="image/*">
                    <small>Actual: <?php echo $empresa['imagen5']; ?></small>
                </div>

                <div class="form-actions">
                    <a href="admin.php" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Actualizar empresa</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
<?php
    exit;
}

// Si es POST, procesar actualización
if ($_POST) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $pagina_web = $_POST['pagina_web'];
    $provincia = $_POST['provincia'];
    
    $servicio1_nombre = $_POST['servicio1_nombre'];
    $servicio1_detalle = $_POST['servicio1_detalle'];
    $servicio2_nombre = $_POST['servicio2_nombre'];
    $servicio2_detalle = $_POST['servicio2_detalle'];
    $servicio3_nombre = $_POST['servicio3_nombre'];
    $servicio3_detalle = $_POST['servicio3_detalle'];
    
    // Obtener imágenes actuales
    $empresa_actual = $conexion->query("SELECT * FROM empresas WHERE id = $id")->fetch_assoc();
    $imagen1 = $empresa_actual['imagen1'];
    $imagen2 = $empresa_actual['imagen2'];
    $imagen3 = $empresa_actual['imagen3'];
    $imagen4 = $empresa_actual['imagen4'];
    $imagen5 = $empresa_actual['imagen5'];
    
    // Procesar nuevas imágenes si se subieron
    for ($i = 1; $i <= 5; $i++) {
        if (isset($_FILES['imagen'.$i]) && $_FILES['imagen'.$i]['error'] == 0) {
            $nombre_archivo = $_FILES['imagen'.$i]['name'];
            $ruta_temporal = $_FILES['imagen'.$i]['tmp_name'];
            $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
            $nuevo_nombre = 'empresa_' . time() . '_' . $i . '.' . $extension;
            
            if (move_uploaded_file($ruta_temporal, 'images/' . $nuevo_nombre)) {
                ${'imagen'.$i} = $nuevo_nombre;
            }
        }
    }

    $query = "UPDATE empresas SET 
              nombre = '$nombre', direccion = '$direccion', telefono = '$telefono', 
              pagina_web = '$pagina_web', provincia = '$provincia',
              servicio1_nombre = '$servicio1_nombre', servicio1_detalle = '$servicio1_detalle',
              servicio2_nombre = '$servicio2_nombre', servicio2_detalle = '$servicio2_detalle',
              servicio3_nombre = '$servicio3_nombre', servicio3_detalle = '$servicio3_detalle',
              imagen1 = '$imagen1', imagen2 = '$imagen2', imagen3 = '$imagen3', 
              imagen4 = '$imagen4', imagen5 = '$imagen5'
              WHERE id = $id";
    
    if ($conexion->query($query)) {
        header('Location: admin.php?mensaje=actualizado');
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>
?>