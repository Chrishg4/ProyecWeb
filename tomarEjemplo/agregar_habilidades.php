
<?php
include 'conexion.php';
$habilidad = $_POST['habilidad'];
$conexion->query("INSERT INTO habilidades (habilidad) VALUES ('$habilidad')");
header("Location: editar_habilidades.php");
exit;
?>