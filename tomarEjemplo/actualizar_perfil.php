
<?php
include 'conexion.php';
$descripcion = $_POST['descripcion'];
$existe = $conexion->query("SELECT id FROM perfil LIMIT 1")->fetch_assoc();
if ($existe) {
    $conexion->query("UPDATE perfil SET descripcion='$descripcion' WHERE id=".$existe['id']);
} else {
    $conexion->query("INSERT INTO perfil (descripcion) VALUES ('$descripcion')");
}
header("Location: admin.php");
exit;
?>