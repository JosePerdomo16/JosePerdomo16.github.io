<?php
// config_correo.php - Configuración para envío de correos con PHPMailer

// ============================================
// 1. INCLUIR PHPMailer
// ============================================

// Ruta correcta a PHPMailer
$phpmailer_path = __DIR__ . '/PHPMailer/src/';

if (file_exists($phpmailer_path . 'Exception.php') && 
    file_exists($phpmailer_path . 'PHPMailer.php') && 
    file_exists($phpmailer_path . 'SMTP.php')) {
    
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';
    
    $phpmailer_disponible = true;
} else {
    $phpmailer_disponible = false;
}

// ============================================
// 2. CONFIGURACIÓN DE CORREO
// ============================================

$config_correo = [
    'host' => 'smtp.gmail.com',
    'puerto' => 587,
    'usuario' => 'robotixlab552@gmail.com',
    'password' => 'mpko ingy pdra hddt',
    'nombre' => 'RobotiX Lab',
    'email' => 'robotixlab552@gmail.com',
    'academia_email' => 'robotixlab552@gmail.com',
    'academia_nombre' => 'RobotiX Lab - Academia'
];

// ============================================
// 3. FUNCIÓN PARA ENVIAR CORREOS
// ============================================

function enviarCorreo($destinatario_email, $destinatario_nombre, $asunto, $mensaje_html, $config) {
    global $phpmailer_disponible;
    
    if (!$phpmailer_disponible) {
        return [
            'exito' => false, 
            'mensaje' => 'PHPMailer no está instalado correctamente. Verifica la carpeta PHPMailer/src/'
        ];
    }
    
    try {
        // Crear instancia de PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['usuario'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['puerto'];
        $mail->CharSet    = 'UTF-8';
        
        // Remitente
        $mail->setFrom($config['email'], $config['nombre']);
        
        // Destinatario
        $mail->addAddress($destinatario_email, $destinatario_nombre);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje_html;
        $mail->AltBody = strip_tags($mensaje_html);
        
        // Enviar
        $mail->send();
        
        return [
            'exito' => true, 
            'mensaje' => 'Correo enviado exitosamente a ' . $destinatario_email
        ];
        
    } catch (Exception $e) {
        return [
            'exito' => false, 
            'mensaje' => 'Error al enviar: ' . $mail->ErrorInfo
        ];
    }
}
?>