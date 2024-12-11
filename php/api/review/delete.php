<?php
/**
 * Description:
 * This endpoint allows deleting multiple reviews by their IDs.
 * The IDs must be provided in an array within the request body, and the request method must be DELETE.
 * 
 * Method: DELETE
 * URL: /api/review/delete.php
 * 
 * Request Body:
 * {
 *   "ids": [1, 2, 3] // Array of review IDs to delete (must be valid positive integers)
 * }
 * 
 * Response Codes:
 * - 200 OK: Reviews were successfully deleted.
 * - 400 Bad Request: No valid IDs provided.
 * - 500 Internal Server Error: Failed to delete reviews.
 * - 405 Method Not Allowed: Invalid request method (only DELETE is allowed).
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Review.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$review = new Review($db);

// Ensure the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

// Validate the IDs parameter
if (!isset($data->ids) || !is_array($data->ids) || empty($data->ids)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "No valid IDs provided."
    ]);
    exit;
}

try {
    // Attempt to delete reviews by their IDs
    if (!$review->deleteReviewsByIds($data->ids)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "No valid IDs provided or deletion failed."
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Reviews were successfully deleted."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete reviews: " . $e->getMessage()
    ]);
}
exit; 
?>
