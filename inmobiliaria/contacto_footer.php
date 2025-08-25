<?php
// contacto_footer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $mensaje  = trim($_POST['mensaje']);

    if (empty($nombre) || empty($email) || empty($telefono) || empty($mensaje)) {
        die("Todos los campos son obligatorios.");
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'vematcr@gmail.com'; // Cambia esto
        $mail->Password   = 'bpvp mobs oizd epcv'; // Cambia esto
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('vematcr@gmail.com', 'UTN Solutions Real State');
        $mail->addAddress('chrishg2004@gmail.com');
        $mail->isHTML(true);
        $mail->Subject = "Mensaje de contacto desde la web";
        $mail->Body    = "
            <h3>Has recibido un nuevo mensaje de contacto</h3>
            <p><b>Nombre:</b> {$nombre}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Teléfono:</b> {$telefono}</p>
            <p><b>Mensaje:</b><br>{$mensaje}</p>
        ";
        $mail->send();
        echo '<script>alert("Mensaje enviado correctamente. ¡Gracias por contactarnos!"); window.history.back();</script>';
    } catch (Exception $e) {
        echo '<h2 style="color:red">Error PHPMailer: '.htmlspecialchars($mail->ErrorInfo).'</h2>';
        echo '<script>alert("Error al enviar el mensaje: '.htmlspecialchars($mail->ErrorInfo).'"); window.history.back();</script>';
    }
    exit;
}
?>

curl.cainfo="C:/xampp/php/extras/ssl/cacert.pem"
openssl.cafile="C:/xampp/php/extras/ssl/cacert.pem"

<?php phpinfo(); ?>
