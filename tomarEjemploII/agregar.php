<?php
include 'config.php';

if ($_POST) {
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
    
    // Manejar subida de imágenes
    $imagen1 = '';
    $imagen2 = '';
    $imagen3 = '';
    $imagen4 = '';
    $imagen5 = '';
    
    // Procesar cada imagen
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

    $query = "INSERT INTO empresas (nombre, direccion, telefono, pagina_web, provincia, 
              servicio1_nombre, servicio1_detalle, servicio2_nombre, servicio2_detalle,
              servicio3_nombre, servicio3_detalle, imagen1, imagen2, imagen3, imagen4, imagen5) 
              VALUES ('$nombre', '$direccion', '$telefono', '$pagina_web', '$provincia',
              '$servicio1_nombre', '$servicio1_detalle', '$servicio2_nombre', '$servicio2_detalle',
              '$servicio3_nombre', '$servicio3_detalle', '$imagen1', '$imagen2', '$imagen3', '$imagen4', '$imagen5')";
    
    if ($conexion->query($query)) {
        header('Location: admin.php?mensaje=agregado');
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>
