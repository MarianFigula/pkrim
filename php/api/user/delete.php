<?php
/**
 * Description:
 * This endpoint allows deleting multiple users by their IDs.
 * The IDs must be provided as query parameters.
 *
 * Method: DELETE
 * URL: /api/user/delete.php?action=delete&ids=1,2,3
 *
 * Query Parameters:
 * - action=delete (required)
 * - ids=1,2,3 (comma-separated list of user IDs, required)
 *
 * Response Codes:
 * - 200 OK: Users were successfully deleted.
 * - 400 Bad Request: Missing or invalid IDs.
 * - 500 Internal Server Error: Failed to delete reviews.
 * - 405 Method Not Allowed: Invalid request method.
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Only DELETE is allowed."
    ]);
    exit;
}

if (empty($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing or invalid user IDs."
    ]);
    exit;
}

$ids = explode(',', $_GET['user_id']);

if (empty($ids)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "No valid user IDs provided."
    ]);
    exit;
}

try {
    if (!$user->deleteUsersByIds($ids)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "No valid IDs provided or deletion failed"]);
        exit;
    }

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Users were successfully deleted"]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Failed to delete users: " . $e->getMessage()]);
}
exit;

