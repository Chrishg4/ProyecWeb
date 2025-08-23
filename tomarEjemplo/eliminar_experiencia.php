
<?php
include 'conexion.php';
$id = $_GET['id'];
$conexion->query("DELETE FROM experiencia WHERE id=$id");
header("Location: editar_experiencia.php");
exit;
?>