<?php
require __DIR__ . '/vendor/autoload.php';

use Morya\Mailer;

if (class_exists('Morya\\Mailer')) {
    echo '<div style="color:green;font-size:1.2em;">Classe Mailer encontrada!</div>';
} else {
    echo '<div style="color:red;font-size:1.2em;">Classe Mailer <b>NÃO</b> encontrada!</div>';
} 