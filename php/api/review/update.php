<?php
/**
 * Description:
 * This endpoint allows updating review information by specifying the review ID and providing new values for review text or rating.
 * The review ID is required to identify the review, and at least one of the following parameters must be provided to update either the review text or the rating.
 * 
 * Method: PUT
 * URL: /api/review/update.php
 * 
 * Request Body:
 * {
 *   "id": 1,                  // Required: The unique ID of the review to update (integer).
 *   "review_text": "new text", // Optional: The new review text (string).
 *   "rating": 5                // Optional: The new rating for the review (integer between 1 and 5).
 * }
 * 
 * Response Codes:
 * - 200 OK: Review successfully updated.
 * - 400 Bad Request: Missing required parameters (id) or invalid data.
 * - 404 Not Found: Review with the specified ID does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only PUT is allowed).
 * - 500 Internal Server Error: Failed to update the review due to server error.
 * 
 * Notes:
 * - The review ID is mandatory, and at least one of 'review_text' or 'rating' must be provided for an update.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600"); // Cache the preflight response for 1 hour
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Review.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$review = new Review($db);

// Ensure the request method is PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !filter_var($data->id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Valid review ID is required to update review information."
    ]);
    exit;
}

// Check for at least one parameter to update
$review_text = isset($data->review_text) ? $data->review_text : null;
$rating = isset($data->rating) ? $data->rating : null;

if (empty($review_text) && empty($rating)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "At least one parameter ('review_text' or 'rating') is required to update the review."
    ]);
    exit;
}

// Set the review ID
$review->setId($data->id);

// Check if the review exists
$stmt = $review->getReviewById();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Review not found."
    ]);
    exit();
}

// Verify ownership of the review
if ($row['user_id'] !== $decoded->id && $decoded->role !== 'S') { // Allow admins to update any review
    http_response_code(403); // Forbidden
    echo json_encode([
        "success" => false,
        "message" => "You do not have permission to update this review."
    ]);
    exit();
}

try {
    // Set fields to update
    if ($review_text !== null) {
        $review->setReviewText($review_text);
    }
    if ($rating !== null) {
        $review->setRating($rating);
    }

    // Update the review
    if ($review->updateReviewById()) {
        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "message" => "Review successfully updated."
        ]);
    } else {
        throw new Exception("Failed to update review.");
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
