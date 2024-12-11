<?php

/**
 * Description:
 * This endpoint allows a user to reset their password by providing their email, new password, repeated password, and security answer.
 * 
 * Method: POST
 * URL: /api/user/forgotPassword.php
 * 
 * Request Body:
 * {
 *   "email": "user@example.com",          // Required: The email address of the user (string).
 *   "password": "newPassword123",        // Required: The new password for the user (string).
 *   "repeated_password": "newPassword123", // Required: The repeated password for confirmation (string).
 *   "securityAnswer": "Fluffy"           // Required: The answer to the security question (string).
 * }
 * 
 * Response Codes:
 * - 200 OK: Password reset successfully.
 * - 400 Bad Request: Missing or invalid input data.
 * - 401 Unauthorized: Invalid security answer.
 * - 404 Not Found: User with the specified email does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only POST is allowed).
 * - 500 Internal Server Error: Failed to reset the password due to a server error.
 * 
 * Notes:
 * - Ensure the request is made using the POST HTTP method.
 * - This endpoint expects JSON data in the request body.
 */

header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';
include_once '../../config/Database.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$key = $_ENV['JWT_SECRET'];

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents("php://input"));

// REVIEW: PUT?
if ($method !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit();
}

if ($data->password !== $data->repeated_password) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."
    ]);
    exit();
}

// Set the email to search for the user
$user->setEmail($data->email);
$stmt = $user->getUserByEmail();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$row) {
    http_response_code(404); // Not Found
    echo json_encode([
        "success" => false,
        "message" => "User not found."
    ]);
    exit();
}

// Verify security answer
// SECURITY: maybe an unsafe comparison for timed-attacks?
if (!$user->verifySecurityAnswer($data->security_answer, $row['security_answer'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid security answer."
    ]);
    exit();
}

// Update the password
$id = $row["id"];
$role = $row["role"];
$user->setId($id);
$user->setPassword($data->password);
$user->setRole($role);

$result = $user->updateUserPassword();

if ($result) {
    $payload = [
        "id" => $user->getId(),
        "email" => $user->getEmail(),
        "role" => $user->getRole(),
        "exp" => time() + 3600000
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Password reset successfully.",
        "token" => $jwt
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to reset password."
    ]);
}

