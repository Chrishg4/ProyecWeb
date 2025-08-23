<?php
require_once 'conexion.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $mensaje_usuario = sanitizar($_POST['mensaje'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($mensaje_usuario)) {
        $error = 'Por favor complete todos los campos obligatorios';
    } elseif (!validarEmail($email)) {
        $error = 'El email no es válido';
    } else {
        // Guardar contacto en logs
        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }
        
        // Crear archivo individual para cada contacto
        $archivo_contacto = 'logs/contacto_' . date('Y-m-d_H-i-s') . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $nombre) . '.txt';
        $contenido_contacto = "CONTACTO UTH SOLUTIONS\n";
        $contenido_contacto .= "===================\n\n";
        $contenido_contacto .= "Nombre: $nombre\n";
        $contenido_contacto .= "Email: $email\n";
        $contenido_contacto .= "Teléfono: $telefono\n";
        $contenido_contacto .= "Fecha: " . date('d/m/Y H:i:s') . "\n\n";
        $contenido_contacto .= "Mensaje:\n$mensaje_usuario\n\n";
        $contenido_contacto .= "Nota: Contactar por email o teléfono\n";
        
        file_put_contents($archivo_contacto, $contenido_contacto);
        
        // También guardar en log general
        $log_general = "[" . date('Y-m-d H:i:s') . "] CONTACTO RECIBIDO\n";
        $log_general .= "Nombre: $nombre | Email: $email | Tel: $telefono\n";
        $log_general .= "Archivo: $archivo_contacto\n";
        $log_general .= "-------------------\n\n";
        
        file_put_contents('logs/contactos.log', $log_general, FILE_APPEND | LOCK_EX);
        
        $success = 'Tu mensaje ha sido recibido correctamente. Te contactaremos pronto.';
    }
}

// Redirección con mensaje
if ($success) {
    header('Location: index.php?mensaje_enviado=1&mensaje=' . urlencode($success));
    exit();
}

if ($error) {
    header('Location: index.php?error_contacto=' . urlencode($error));
    exit();
}
?>
