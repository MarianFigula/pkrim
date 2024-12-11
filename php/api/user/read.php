<?php
/**
 * Description:
 * This endpoint allows retrieving user information by specifying the user ID or email as query parameters.
 * If neither is provided, all users are returned (admin only).
 * 
 * Method: GET
 * URL: /api/user/read.php
 * 
 * Query Parameters:
 * - id (optional): int - The unique ID of the user to retrieve.
 * - email (optional): string - The email address of the user to retrieve.
 * 
 * Response Codes:
 * - 200 OK: User(s) successfully retrieved.
 * - 403 Forbidden: Admin privileges are required to view all users.
 * - 404 Not Found: User with the specified ID or email does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only GET is allowed).
 * 
 * Notes:
 * - If neither 'id' nor 'email' is provided, all users are returned, which requires admin privileges.

 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600"); // Cache the preflight response for 1 hour
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "GET"){
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"]);
    exit();
}

try {
    if (isset($_GET['email'])) {
        $user->setEmail($_GET['email']);
        $stmt = $user->getUserByEmail();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "message" => "User not found."
            ]);
            exit();
        }

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $row
        ]);
        exit();
    }

    $userId = $decoded->id;
    // Fetch user details by ID
    if ($userId) {
        $user->setId($userId);
        $stmt = $user->getUserById();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "message" => "User not found."
            ]);
            exit();
        }

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $row
        ]);
        exit();
    }

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
    exit();
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}

