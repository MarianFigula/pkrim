<?php
require __DIR__ . '/../vendor/autoload.php'; // Load JWT library

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1)); // Go two levels up
$dotenv->load();

// Retrieve the secret key from .env
$key = $_ENV['JWT_SECRET'];

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token is missing."]);
    exit();
}

$token = str_replace('Bearer ', '', $headers['Authorization']);

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    // The token is valid. Use $decoded->id or $decoded->role as needed
    /* 
    Example if not admin in the proper endpoint:
        if ($decoded->role !== 'admin') {
            http_response_code(403); // Forbidden
            echo json_encode(["message" => "Access denied"]);
            exit();
        }
    */
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Invalid token.", "error" => $e->getMessage()]);    exit();
}
