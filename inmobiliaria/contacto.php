<?php
require_once 'conexion.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $mensaje = sanitizar($_POST['mensaje'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $error = 'Por favor complete todos los campos obligatorios';
    } elseif (!validarEmail($email)) {
        $error = 'El email no es válido';
    } else {
        // Aquí podrías enviar el email real
        // Por ahora solo simularemos el éxito
        $success = 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.';
        
        // Log del mensaje para revisar en desarrollo
        error_log("Contacto recibido: $nombre - $email - $mensaje");
    }
}

// Redirección con mensaje
if ($success) {
    header('Location: index.php?mensaje_enviado=1');
    exit();
}

if ($error) {
    header('Location: index.php?error_contacto=' . urlencode($error));
    exit();
}
?>
