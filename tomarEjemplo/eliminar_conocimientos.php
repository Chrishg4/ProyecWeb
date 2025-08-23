
<?php
include 'conexion.php';
$id = $_GET['id'];
$conexion->query("DELETE FROM conocimientos WHERE id=$id");
header("Location: editar_habilidades.php");
exit;
?>