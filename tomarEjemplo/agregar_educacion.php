
<?php
include 'conexion.php';
$titulo = $_POST['titulo'];
$institucion = $_POST['institucion'];
$fecha = $_POST['fecha'];
$conexion->query("INSERT INTO educacion (titulo, institucion, fecha) VALUES ('$titulo', '$institucion', '$fecha')");
header("Location: editar_educacion.php");
?>