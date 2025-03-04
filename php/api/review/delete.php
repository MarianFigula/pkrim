<?php
/**
 * Description:
 * This endpoint allows deleting multiple reviews by their IDs.
 * The IDs must be provided as query parameters.
 *
 * Method: DELETE
 * URL: /api/review/delete.php?action=delete&ids=1,2,3
 *
 * Query Parameters:
 * - action=delete (required)
 * - ids=1,2,3 (comma-separated list of review IDs, required)
 *
 * Response Codes:
 * - 200 OK: Reviews were successfully deleted.
 * - 400 Bad Request: Missing or invalid IDs.
 * - 500 Internal Server Error: Failed to delete reviews.
 * - 405 Method Not Allowed: Invalid request method.
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Review.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$review = new Review($db);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Only DELETE is allowed."
    ]);
    exit;
}

// Validate "action" query param
if (!isset($_GET['action']) || $_GET['action'] !== 'delete') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing action parameter."
    ]);
    exit;
}

// Validate "ids" query param
if (empty($_GET['review_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing or invalid review IDs."
    ]);
    exit;
}

// Convert comma-separated list to an array
$ids = explode(',', $_GET['review_id']);

if (empty($ids)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "No valid review IDs provided."
    ]);
    exit;
}

try {
    // Attempt to delete reviews by their IDs
    if (!$review->deleteReviewsByIds($ids)) {
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
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete reviews: " . $e->getMessage()
    ]);
}

