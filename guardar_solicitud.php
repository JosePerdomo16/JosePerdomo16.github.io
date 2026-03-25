<?php
// guardar_solicitud.php - Versión completa con envío de correos
header("Content-Type: application/json; charset=UTF-8");

// Mostrar todos los errores (solo para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivos necesarios
require_once 'conexion.php';
require_once 'config_correo.php';

try {
    // Verificar que es una solicitud POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Usa POST.');
    }
    
    // Obtener los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $programa = trim($_POST['programa'] ?? '');
    
    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre es obligatorio');
    }
    
    if (empty($email)) {
        throw new Exception('El correo electrónico es obligatorio');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El correo electrónico no es válido');
    }
    
    if (empty($programa)) {
        throw new Exception('Debes seleccionar un programa');
    }
    
    // Guardar en la base de datos
    $sql = "INSERT INTO solicitudes (nombre, email, telefono, programa) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->bind_param("ssss", $nombre, $email, $telefono, $programa);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al guardar: ' . $stmt->error);
    }
    
    $id = $stmt->insert_id;
    
    // ========== CORREO PARA EL CLIENTE ==========
    $asunto_cliente = "🤖 ¡Bienvenido a RobotiX Lab! - Confirmación de solicitud";
    
    $mensaje_cliente = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Bienvenido a RobotiX Lab</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #FF7A18, #FFC247); padding: 30px; text-align: center; }
            .header h1 { color: #1A1E2D; margin: 0; font-size: 28px; }
            .content { padding: 30px; }
            .info-box { background: #f9f9f9; border-left: 4px solid #FF7A18; padding: 15px; margin: 20px 0; border-radius: 8px; }
            .btn { display: inline-block; background: #FF7A18; color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; margin: 20px 0; }
            .footer { background: #1A1E2D; color: white; padding: 20px; text-align: center; font-size: 12px; }
            .highlight { color: #FF7A18; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🤖 RobotiX Lab</h1>
                <p style='color: #1A1E2D; margin: 5px 0 0;'>¡Aprende a construir el futuro!</p>
            </div>
            <div class='content'>
                <h2>¡Hola $nombre! 👋</h2>
                <p>¡Gracias por contactar a <span class='highlight'>RobotiX Lab</span>!</p>
                <p>Hemos recibido tu solicitud de información y estamos muy emocionados de que formes parte de nuestra comunidad de jóvenes creadores.</p>
                
                <div class='info-box'>
                    <p><strong>📋 Detalles de tu solicitud:</strong></p>
                    <p>👤 <strong>Nombre:</strong> $nombre</p>
                    <p>📧 <strong>Correo:</strong> $email</p>
                    <p>📱 <strong>Teléfono:</strong> " . ($telefono ?: 'No especificado') . "</p>
                    <p>🤖 <strong>Programa:</strong> $programa</p>
                    <p>🆔 <strong>ID de solicitud:</strong> #$id</p>
                </div>
                
                <p><strong>📞 ¿Qué sigue?</strong></p>
                <p>Uno de nuestros asesores se comunicará contigo en las próximas <strong>24 horas</strong> para:</p>
                <ul>
                    <li>Responder todas tus preguntas</li>
                    <li>Confirmar el programa más adecuado</li>
                    <li>Coordinar una visita al laboratorio</li>
                </ul>
                
                <div style='background: #FFF3E0; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>⚡ ¿Necesitas información urgente?</strong><br>
                    Contáctanos directamente al <strong>+57 317 176 5919</strong> o por WhatsApp.</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='https://wa.me/573171765919' class='btn' style='color: white; text-decoration: none;'>📱 Contáctanos por WhatsApp</a>
                </p>
            </div>
            <div class='footer'>
                <p>RobotiX Lab - Neiva, Huila, Colombia</p>
                <p>📍 Primera academia de robótica inclusiva del sur colombiano</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // ========== CORREO PARA LA ACADEMIA ==========
    $asunto_academia = "📋 NUEVA SOLICITUD - RobotiX Lab #$id";
    
    $mensaje_academia = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Nueva Solicitud - RobotiX Lab</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #FF7A18, #FFC247); padding: 20px; text-align: center; }
            .content { padding: 30px; }
            .alert-box { background: #FFF3E0; border-left: 4px solid #FF7A18; padding: 15px; margin: 20px 0; border-radius: 8px; }
            .info-detail { background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin: 0; color: #1A1E2D;'>📋 NUEVA SOLICITUD DE INFORMACIÓN</h2>
            </div>
            <div class='content'>
                <div class='alert-box'>
                    <p><strong>⚠️ ¡Nuevo registro en el sistema!</strong></p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                    <p><strong>ID Solicitud:</strong> #$id</p>
                </div>
                
                <h3>👤 Datos del interesado:</h3>
                <div class='info-detail'>
                    <p><strong>Nombre completo:</strong> $nombre</p>
                    <p><strong>Correo electrónico:</strong> $email</p>
                    <p><strong>Teléfono/WhatsApp:</strong> " . ($telefono ?: 'No especificado') . "</p>
                    <p><strong>Programa de interés:</strong> $programa</p>
                </div>
                
                <h3>📌 Acciones recomendadas:</h3>
                <ul>
                    <li>✅ Contactar al interesado en las próximas 24 horas</li>
                    <li>✅ Confirmar el programa y horarios disponibles</li>
                    <li>✅ Enviar información detallada del curso</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Enviar correo al cliente
    $envio_cliente = enviarCorreo($email, $nombre, $asunto_cliente, $mensaje_cliente, $config_correo);
    
    // Enviar correo a la academia
    $envio_academia = enviarCorreo(
        $config_correo['academia_email'], 
        $config_correo['academia_nombre'], 
        $asunto_academia, 
        $mensaje_academia, 
        $config_correo
    );
    
    // Respuesta exitosa
    echo json_encode([
        'exito' => true,
        'mensaje' => '¡Solicitud guardada correctamente! Revisa tu correo para confirmación.',
        'id' => $id,
        'correos' => [
            'cliente' => $envio_cliente,
            'academia' => $envio_academia
        ]
    ]);
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>