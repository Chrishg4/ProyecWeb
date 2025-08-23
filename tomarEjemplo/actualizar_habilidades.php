
<?php
include 'conexion.php';
$id = $_POST['id'];
$habilidad = $_POST['habilidad'];
$conexion->query("UPDATE habilidades SET habilidad='$habilidad' WHERE id=$id");
header("Location: editar_habilidades.php");
exit;
?>