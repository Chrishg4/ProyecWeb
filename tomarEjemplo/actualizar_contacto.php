<?php
include 'conexion.php';

// Recibe los datos del formulario
$nombre = $_POST['nombre'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$web = $_POST['web'];
$titulo = $_POST['titulo'];

// Manejo de la imagen
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nombre = 'fotos/perfil_' . time() . '.' . $ext;
    // Crea la carpeta si no existe
    if (!is_dir('fotos')) {
        mkdir('fotos', 0777, true);
    }
    move_uploaded_file($_FILES['foto']['tmp_name'], $foto_nombre);
    $foto = $foto_nombre;
}

// Verifica si ya existe un registro
$existe = $conexion->query("SELECT id FROM contacto LIMIT 1")->fetch_assoc();

if ($existe) {
    // Actualiza el registro existente
    $sql = "UPDATE contacto SET 
        nombre='$nombre',
        direccion='$direccion',
        telefono='$telefono',
        email='$email',
        web='$web',
        titulo='$titulo'";
    if ($foto) {
        $sql .= ", foto='$foto'";
    }
    $sql .= " WHERE id=" . $existe['id'];
    $conexion->query($sql);
} else {
    // Inserta un nuevo registro
    $campos = "nombre, direccion, telefono, email, web, titulo";
    $valores = "'$nombre', '$direccion', '$telefono', '$email', '$web', '$titulo'";
    if ($foto) {
        $campos .= ", foto";
        $valores .= ", '$foto'";
    }
    $conexion->query("INSERT INTO contacto ($campos) VALUES ($valores)");
}

header("Location: admin.php");
exit;
?>