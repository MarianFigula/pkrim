<?php
/**
 * Description:
 * This endpoint allows retrieving review information by specifying the review ID, user ID, user email, or art ID as query parameters.
 * If neither is provided, all reviews are returned (admin only).
 * 
 * Method: GET
 * URL: /api/review/read.php
 * 
 * Query Parameters:
 * - id (optional): int - The unique ID of the review to retrieve.
 * - user_id (optional): int - The ID of the user whose reviews are to be retrieved.
 * - user_email (optional): string - The email address of the user whose reviews are to be retrieved.
 * - art_id (optional): int - The ID of the art whose reviews are to be retrieved.
 * 
 * Response Codes:
 * - 200 OK: Review(s) successfully retrieved.
 * - 403 Forbidden: Admin privileges are required to view all reviews.
 * - 404 Not Found: Review with the specified criteria does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only GET is allowed).
 * 
 * Notes:
 * - If none of the query parameters are provided, all reviews are returned, which requires admin privileges.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600"); // Cache the preflight response for 1 hour
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once '../../classes/Review.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$review = new Review($db);
$user = new User($db);
$art = new Art($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit();
}
try {
    if (isset($_GET["admin_all"]) && isset($_GET["user_id"]) && $decoded->role == "S"){
        $userId = $_GET["user_id"];
        $review->setUserId($userId);
        $stmt = $review->getReviewsByUserId();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $reviews,
        ]);
        exit();
    }
    // Fetch reviews by authenticated user's ID - userove reviews
    if (isset($decoded->email)) {
        $user_email = $decoded->email;
        $user->setEmail($user_email);
        $stmt = $user->getUserByEmail();
        $user_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_row) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "message" => "User not found."
            ]);
            exit();
        }

        $review->setUserId($user_row['id']);
        $stmt = $review->getReviewsByUserId();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $reviews,
            "user" => $user_row
        ]);
        exit();
    }

    // Fetch reviews by art ID
    if (isset($_GET['art_id'])) {
        $art_id = intval($_GET['art_id']);
        $art->setId($art_id);
        $stmt = $art->getArtById();
        $art_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$art_row) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "message" => "Art not found."
            ]);
            exit();
        }

        $review->setArtId($art_id);
        $stmt = $review->getReviewsByArtId();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($reviews)) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "message" => "No reviews found for the specified art."
            ]);
            exit();
        }

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $reviews,
            "som tu review read get artid" => true
        ]);
        exit();
    }


    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Invalid query parameters."
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}
