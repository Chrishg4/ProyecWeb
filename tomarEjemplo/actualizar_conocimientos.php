
<?php
include 'conexion.php';
$id = $_POST['id'];
$conocimiento = $_POST['conocimiento'];
$conexion->query("UPDATE conocimientos SET conocimiento='$conocimiento' WHERE id=$id");
header("Location: editar_habilidades.php");
exit;
?>