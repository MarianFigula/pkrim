<?php
/**
 * Description:
 * This endpoint allows creating a new user with required details like username, email, password, and optional security questions.
 * The user data must be provided in JSON format within the request body, and the request method must be POST.
 * 
 * Method: POST
 * URL: /api/user/create.php
 * 
 * Request Body:
 * {
 *   "username": "exampleUser",          (string)
 *   "email": "user@example.com",        (string)
 *   "password": "password123",          (string)
 *   "security_question": "Your pet's name?", (string)
 *   "security_answer": "Fluffy",        (string).
 *   "role": "U"                         either 'U' (User) or 'A' (Admin). Defaults to 'U'.
 * }
 * 
 * Response Codes:
 * - 201 Created: User was successfully created.
 * - 400 Bad Request: Invalid input provided or missing required fields.
 * - 500 Internal Server Error: Failed to create user due to server error.
 * 
 * Notes:
 * - The username, email, and password are mandatory fields.
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/User.php';
include_once '../../classes/Cart.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$cart = new Cart($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "POST"){
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

// NOTE: but role may remain U by default
try {
    $user->setUsername($data['username']);
    $user->setEmail($data['email']);
    $user->setPassword($data['password']);
    $user->setSecurityQuestion($data['security_question'] ?? 'a');
    $user->setSecurityAnswer($data['security_answer'] ?? 'b');
    $user->setRole($data['role'] ?? 'U');

    if ($user->createUser()) {
        // todo - ziskat id noveho usera a vytvorit cart

        http_response_code(201); // Created
        echo json_encode(["success" => true, "message" => "User created successfully."]);
    } else {
        throw new Exception("Failed to create user.");
    }

} catch (InvalidArgumentException $e) {
    // Handle validation errors
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} catch (Exception $e) {
    // Handle other errors (e.g., database issues)
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
