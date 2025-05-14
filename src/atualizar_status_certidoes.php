<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Configurações do Google Drive
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/drive-credentials.json');
$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(Google_Service_Drive::DRIVE_READONLY);
$service = new Google_Service_Drive($client);

// ID da pasta principal do Drive
$driveFolderId = '1U5LLWnnNnQFL_0O6xB123dALOJPEUFWl';

// Tipos de certidão
$tipos = [
    'certidao_federal' => 'federal',
    'certidao_estadual' => 'estadual',
    'certidao_municipal' => 'municipal',
    'certidao_trabalhista' => 'trabalhista',
    'certidao_fgts' => 'fgts',
    'certidao_sicaf' => 'sicaf',
];

// Busca todos os fornecedores
$fornecedores = $pdo->query('SELECT id, nome FROM fornecedores')->fetchAll();

foreach ($fornecedores as $fornecedor) {
    $status = [];
    foreach ($tipos as $campo => $tipo) {
        // Busca arquivos no Drive com nome do fornecedor e tipo da certidão
        $query = sprintf(
            "'%s' in parents and name contains '%s' and name contains '%s' and trashed = false",
            $driveFolderId,
            addslashes($fornecedor['nome']),
            $tipo
        );
        $results = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
            'pageSize' => 1
        ]);
        $status[$campo] = (count($results->files) > 0) ? 1 : 0;
    }
    // Atualiza ou insere na tabela status_certidoes
    $existe = $pdo->prepare('SELECT id FROM status_certidoes WHERE fornecedor_id = ?');
    $existe->execute([$fornecedor['id']]);
    if ($existe->fetch()) {
        $stmt = $pdo->prepare('UPDATE status_certidoes SET certidao_federal=?, certidao_estadual=?, certidao_municipal=?, certidao_trabalhista=?, certidao_fgts=?, certidao_sicaf=?, ultima_atualizacao=NOW() WHERE fornecedor_id=?');
        $stmt->execute([
            $status['certidao_federal'],
            $status['certidao_estadual'],
            $status['certidao_municipal'],
            $status['certidao_trabalhista'],
            $status['certidao_fgts'],
            $status['certidao_sicaf'],
            $fornecedor['id']
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO status_certidoes (fornecedor_id, certidao_federal, certidao_estadual, certidao_municipal, certidao_trabalhista, certidao_fgts, certidao_sicaf, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $fornecedor['id'],
            $status['certidao_federal'],
            $status['certidao_estadual'],
            $status['certidao_municipal'],
            $status['certidao_trabalhista'],
            $status['certidao_fgts'],
            $status['certidao_sicaf']
        ]);
    }
    echo "Fornecedor: {$fornecedor['nome']} - Status atualizado\n";
}

echo "\nStatus das certidões atualizado com sucesso!\n"; 