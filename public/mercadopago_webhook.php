<?php

// Habilita o registro de logs para debugging
$logPath = 'mercadopago_webhooks.txt';

try {
    // Obtém o payload enviado pelo MercadoPago
    $payload = file_get_contents('php://input');
    $headers = getallheaders();
    $timestamp = date('Y-m-d H:i:s');

    $event = json_decode($payload);
    // Check mandatory parameters
    if (!isset($event->type, $event->data) || !ctype_digit($event->data->id)) {
        http_response_code(400);
        return;
    } 
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    // Log do erro
    $errorLog = sprintf("[%s] Error: %s\n\n", date('Y-m-d H:i:s'), $e->getMessage());
    file_put_contents($logPath, $errorLog, FILE_APPEND);

    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}
