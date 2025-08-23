
<?php
include 'conexion.php';
$puesto = $_POST['puesto'];
$empresa = $_POST['empresa'];
$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];
$conexion->query("INSERT INTO experiencia (puesto, empresa, fecha, descripcion) VALUES ('$puesto', '$empresa', '$fecha', '$descripcion')");
header("Location: editar_experiencia.php");
exit;
?>