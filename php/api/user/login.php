<?php
/**
 * Description:
 * This endpoint allows a user to log in by providing their email and password.
 * If the credentials are correct, a session is started.
 * 
 * Method: POST
 * URL: /api/user/login.php
 * 
 * Request Body:
 * {
 *   "email": "user@example.com",   // Required: (string)
 *   "password": "password123"      // Required: (string)
 * }
 * 
 * Response Codes:
 * - 200 OK: Login successful.
 * - 400 Bad Request: Missing required fields (email or password).
 * - 401 Unauthorized: Invalid email or password.
 * - 404 Not Found: User with the specified email does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only POST is allowed).
 * 
 * Notes:
 * - Upon successful login, session variables are set to maintain the user's session.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Max-Age: 3600");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
header("Content-Type: application/json");

require __DIR__ . '/../../vendor/autoload.php';
include_once '../../config/Database.php';
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

if ($method !== "POST") {
    http_response_code(405); 
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

try {
    $user->setEmail($data->email);

    $user->setPassword($data->password);

    $stmt = $user->verifyUserLogin();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$row){
        http_response_code(200);
        echo json_encode([
            "success" => false,
            "message" => "Wrong email or password."
        ]);
        exit();
    }
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Something failed.",
    ]);
    exit();
}

$payload = [
    "id" => $row['id'],
    "email" => $row['email'],
    "role" => $row['role'],
    "exp" => time() + 3600000
];
$jwt = JWT::encode($payload, $key, 'HS256');

http_response_code(200);
echo json_encode([
    "success" => true,
    "token" => $jwt
]);