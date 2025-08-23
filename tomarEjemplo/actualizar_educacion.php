
<?php
include 'conexion.php';
$id = $_POST['id'];
$titulo = $_POST['titulo'];
$institucion = $_POST['institucion'];
$fecha = $_POST['fecha'];
$conexion->query("UPDATE educacion SET titulo='$titulo', institucion='$institucion', fecha='$fecha' WHERE id=$id");
header("Location: editar_educacion.php");
?>