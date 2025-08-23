
<?php
include 'conexion.php';
$id = $_POST['id'];
$idioma = $_POST['idioma'];
$conexion->query("UPDATE idiomas SET idioma='$idioma' WHERE id=$id");
header("Location: editar_idiomas.php");
exit;
?>