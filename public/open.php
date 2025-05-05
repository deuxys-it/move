<?php
$logPath = __DIR__ . '/../src/aberturas.log';

if (isset($_GET['id'])) {
    $trackingId = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['id']);
    $logEntry = date('Y-m-d H:i:s') . " | Abertura do ID: " . $trackingId . PHP_EOL;

    file_put_contents($logPath, $logEntry, FILE_APPEND);
}

// Retorna pixel transparente
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
