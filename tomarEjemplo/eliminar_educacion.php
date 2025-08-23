
<?php
include 'conexion.php';
$id = $_GET['id'];
$conexion->query("DELETE FROM educacion WHERE id=$id");
header("Location: editar_educacion.php");
?>