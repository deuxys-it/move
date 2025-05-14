<?php
// Teste de envio de e-mail SMTP com PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'olucasquadros@gmail.com';
    $mail->Password   = 'vuwe eryv uyet tqqg'; // Sem aspas
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    // Remetente e destinatário
    $mail->setFrom('olucasquadros@gmail.com', 'Teste Morya');
    $mail->addAddress('olucasquadros@gmail.com', 'Lucas Quadros');

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Teste de SMTP - Morya';
    $mail->Body    = '<b>Este é um teste de envio SMTP pelo sistema Morya.</b>';
    $mail->AltBody = 'Este é um teste de envio SMTP pelo sistema Morya.';

    $mail->send();
    echo '<div style="color:green;font-size:1.2em;">E-mail enviado com sucesso!</div>';
} catch (Exception $e) {
    echo '<div style="color:red;font-size:1.2em;">Erro ao enviar: ' . htmlspecialchars($mail->ErrorInfo) . '</div>';
} 