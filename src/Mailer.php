<?php
namespace Morya;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public static function send($to, $subject, $body, $anexos_individuais = [], $anexos_globais = [], $smtp = null) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        if ($smtp === null && isset($_SESSION['user'])) {
            $smtp = [
                'host' => $_SESSION['user']['smtp_host'] ?? $_ENV['MAIL_HOST'],
                'port' => $_SESSION['user']['smtp_port'] ?? $_ENV['MAIL_PORT'],
                'user' => $_SESSION['user']['smtp_user'] ?? $_ENV['MAIL_USERNAME'],
                'pass' => $_SESSION['user']['smtp_pass'] ?? $_ENV['MAIL_PASSWORD'],
                'secure' => $_SESSION['user']['smtp_secure'] ?? $_ENV['MAIL_ENCRYPTION'],
                'from' => $_SESSION['user']['email'] ?? $_ENV['MAIL_FROM_ADDRESS'],
                'nome' => $_SESSION['user']['nome'] ?? 'Morya',
            ];
        }

        $success = true;

        // Caminho do storage.json
        $storagePath = __DIR__ . '/storage.json';

        // Carrega o storage existente ou inicia como array
        $storageData = file_exists($storagePath) ? json_decode(file_get_contents($storagePath), true) : [];
        if (!is_array($storageData)) {
            $storageData = [];
        }

        foreach ($to as $index => $email) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = $smtp['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp['user'];
                $mail->Password   = $smtp['pass'];
                $mail->SMTPSecure = ($smtp['secure'] !== 'null' && $smtp['secure'] !== '') ? $smtp['secure'] : false;
                $mail->Port       = $smtp['port'];

                $mail->setFrom($smtp['from'], $smtp['nome']);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress(trim($email));
                }

                // Anexo individual
                $anexoIndividualNome = '';
                if (isset($anexos_individuais['tmp_name'][$index]) && $anexos_individuais['error'][$index] === UPLOAD_ERR_OK) {
                    $mail->addAttachment($anexos_individuais['tmp_name'][$index], $anexos_individuais['name'][$index]);
                    $anexoIndividualNome = $anexos_individuais['name'][$index];
                }

                // Anexos globais
                if (!empty($anexos_globais['tmp_name'])) {
                    foreach ($anexos_globais['tmp_name'] as $i => $tmp) {
                        if ($anexos_globais['error'][$i] === UPLOAD_ERR_OK) {
                            $mail->addAttachment($tmp, $anexos_globais['name'][$i]);
                        }
                    }
                }

                $titulo = is_array($subject) ? $subject[$index] : $subject;
                $corpo = is_array($body) ? $body[$index] : $body;

                $mail->isHTML(true);
                $mail->Subject = $titulo;
                // Adiciona assinatura como imagem, se existir
                $assinatura = isset($_SESSION['user']['assinatura']) && $_SESSION['user']['assinatura'] ? $_SESSION['user']['assinatura'] : '';
                if ($assinatura) {
                    $assinaturaPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($assinatura, '/');
                    if (file_exists($assinaturaPath)) {
                        $cid = md5($assinaturaPath);
                        $mail->addEmbeddedImage($assinaturaPath, $cid);
                        $corpo .= '<br><br><img src="cid:' . $cid . '" style="max-width:320px; max-height:120px;">';
                    }
                }
                $mail->Body = $corpo;
                $mail->AltBody = strip_tags($corpo);

                $mail->send();

                // ✅ Gravação no storage.json após envio
                $storageData[] = [
                    'title' => $titulo,
                    'destinatarios' => $email,
                    'status' => 'ENVIADO',
                    'materias' => $anexoIndividualNome ?: 'Sem anexo individual',
                    'timestamp' => date('Y-m-d H:i:s')
                ];

            } catch (Exception $e) {
                echo "Erro ao enviar: {$e->getMessage()}";
                error_log("Erro PHPMailer ({$email}): " . $mail->ErrorInfo);
                $success = false;

                // Mesmo que dê erro, grava com status de erro
                $storageData[] = [
                    'title' => is_array($subject) ? $subject[$index] : $subject,
                    'destinatarios' => $email,
                    'status' => 'ERRO: ' . $mail->ErrorInfo,
                    'materias' => $anexoIndividualNome ?: 'Sem anexo individual',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }

        // Salva o storage atualizado
        file_put_contents($storagePath, json_encode($storageData, JSON_PRETTY_PRINT));

        return $success;
    }
}
