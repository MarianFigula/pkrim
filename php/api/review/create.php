<?php
/**
 * Description:
 * This endpoint allows creating a new review by specifying user email, art ID, review text, and rating.
 * The user email and art ID are required to identify the user and artwork, and review text and rating are required for the review itself.
 * 
 * Method: POST
 * URL: /api/review/create.php
 * 
 * Request Body:
 * {
 *   "email": "user@example.com", // Required: The email of the user creating the review (string).
 *   "art_id": 1,                 // Required: The ID of the artwork being reviewed (integer).
 *   "review_text": "Great art!", // Required: The text of the review (string).
 *   "rating": 5                  // Required: The rating for the artwork (integer between 1 and 5).
 * }
 * 
 * Response Codes:
 * - 201 Created: Review successfully created.
 * - 400 Bad Request: Missing required parameters or invalid data.
 * - 404 Not Found: User or artwork with the specified identifiers does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only POST is allowed).
 * - 500 Internal Server Error: Failed to create the review due to server error.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Review.php';
include_once '../../classes/Art.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$review = new Review($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->art_id) || empty($data->review_text) || empty($data->rating)) {
    http_response_code(200);
    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);
    exit();
}

$art_id = intval($data->art_id);
$review_text = $data->review_text;
$rating = intval($data->rating);

try {
    $reviewer_user_id = $decoded->id;

    $art = new Art($db);
    $art->setId($art_id);

    $artStmt = $art->getArtById();
    $artRow = $artStmt->fetch(PDO::FETCH_ASSOC);

    if (!$artRow) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Art not found."
        ]);
        exit();
    }

    $art_creator_user_id = $artRow["user_id"];

    $existingReviewQuery = "SELECT id FROM review WHERE user_id = :user_id AND art_id = :art_id LIMIT 1";
    $stmt = $db->prepare($existingReviewQuery);
    $stmt->execute([
        ':user_id' => $reviewer_user_id,
        ':art_id' => $art_id,
    ]);

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            "success" => false,
            "message" => "You have already reviewed this artwork."
        ]);
        exit();
    }

    $review->setUserId($reviewer_user_id);
    $review->setArtId($art_id);
    $review->setReviewText($review_text);
    $review->setRating($rating);

    if ($review->createReview()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Review successfully created."
        ]);
    } else {
        throw new Exception("Failed to create review.");
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
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}
?>
