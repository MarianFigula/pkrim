<?php
require __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

$key = $_ENV['JWT_SECRET'];

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token is missing."]);
    exit();
}

$token = str_replace('Bearer ', '', $headers['Authorization']);

$tokenParts = explode('.', $token);
$header = json_decode(base64_decode($tokenParts[0]));


if ($header->alg === 'none') {
    $decoded = json_decode(base64_decode($tokenParts[1]));
}else{

    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid token.", "error" => $e->getMessage()]);    exit();
    }
}
