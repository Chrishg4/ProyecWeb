
<?php
include 'conexion.php';
$id = $_GET['id'];
$conexion->query("DELETE FROM info_adicional WHERE id=$id");
header("Location: editar_info_adicional.php");
exit;
?>