<?php

$params = [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
];

$localParams = include __DIR__ . '/params-local.php';

if (!empty($localParams)) {
    $params = array_replace($localParams, $params);
}

return $params;
