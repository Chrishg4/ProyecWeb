
<?php
include 'conexion.php';
$id = $_POST['id'];
$puesto = $_POST['puesto'];
$empresa = $_POST['empresa'];
$fecha = $_POST['fecha'];
$descripcion = $_POST['descripcion'];
$conexion->query("UPDATE experiencia SET puesto='$puesto', empresa='$empresa', fecha='$fecha', descripcion='$descripcion' WHERE id=$id");
header("Location: editar_experiencia.php");
exit;
?>