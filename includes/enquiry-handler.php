<?php
declare(strict_types=1);
require_once __DIR__ . '/enquiry-service.php';
enquirySession();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST'); http_response_code(405);
    echo json_encode(['ok'=>false, 'message'=>'Please use the date request form.']); exit;
}
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 16384) {
    http_response_code(413); echo json_encode(['ok'=>false,'message'=>'Your request is too long. Please shorten the message.']); exit;
}
$enquiryConfig = require __DIR__ . '/../config/enquiry.php';
[$status, $response] = enquirySubmit($_POST, $_SESSION, $enquiryConfig, $property['name'], 'enquirySend');
http_response_code($status);
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
