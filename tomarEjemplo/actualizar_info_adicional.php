
<?php
include 'conexion.php';
$id = $_POST['id'];
$dato = $_POST['dato'];
if ($dato != "") {
    $conexion->query("UPDATE info_adicional SET dato='$dato' WHERE id=$id");
}
header("Location: editar_info_adicional.php");
exit;
?>