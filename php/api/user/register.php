<?php
/**
 * Description:
 * This endpoint allows a new user to register by providing necessary details such as username, email, password, and security information.
 * If the registration is successful, a session is started.
 * 
 * Method: POST
 * URL: /api/user/register.php
 * 
 * Request Body:
 * {
 *   "username": "newUser",             // Required: The username of the new user (string).
 *   "email": "user@example.com",       // Required: The email address of the new user (string).
 *   "password": "password123",         // Required: The password for the new user (string).
 *   "repeatedPassword": "password123", // Required: The repeated password for confirmation (string).
 *   "securityQuestion": "Your pet's name?", // Required: Security question for password recovery (string).
 *   "securityAnswer": "Fluffy"         // Required: Answer to the security question (string).
 * }
 * 
 * Response Codes:
 * - 201 Created: User successfully created.
 * - 400 Bad Request: Missing or invalid input data.
 * - 500 Internal Server Error: Failed to create the user due to a server error.
 * - 405 Method Not Allowed: Invalid request method used (only POST is allowed).
 * 
 * Notes:
 * - Upon successful registration, session variables are set to maintain the user's session.
 */

header("Content-Type: application/json");

require __DIR__ . '/../../vendor/autoload.php';
include_once '../../config/Database.php';
include_once '../../classes/Cart.php';
include_once '../../classes/User.php';
include_once '../../config/cors.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$key = $_ENV['JWT_SECRET'];

$database = new Database();
$db = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);
    exit();
}

if ($data->password !== $data->repeated_password) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."
    ]);
    exit();
}

try {
    $user->setUsername($data->username);
    $user->setEmail($data->email);
    $user->setPassword($data->password);
    $user->setSecurityQuestion($data->security_question);
    $user->setSecurityAnswer($data->security_answer);
    $user->setRole('U');

    $userId = $user->createUser();

    if ($userId) {
        // Create the cart for the new user
        $cart = new Cart($db);
        $cart->setUserId($userId);

        if ($cart->createCart()) {
            // Generate the JWT token for the user
            $payload = [
                "id" => $userId,
                "email" => $user->getEmail(),
                "role" => 'U',
                "exp" => time() + 3600000
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "User created successfully.",
                "token" => $jwt // Include the token in the response
            ]);
        } else {
            throw new Exception("Unable to create user and cart.");
        }
    } else {
        throw new Exception("Unable to create user.");
    }
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}