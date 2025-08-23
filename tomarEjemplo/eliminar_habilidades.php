
<?php
include 'conexion.php';
$id = $_GET['id'];
$conexion->query("DELETE FROM habilidades WHERE id=$id");
header("Location: editar_habilidades.php");
exit;
?>