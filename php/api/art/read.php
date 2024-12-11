<?php
/**
 * Description:
 * This endpoint allows retrieving art information by specifying the art ID or user ID as query parameters.
 * If neither is provided, all art records are returned.
 * 
 * Method: GET
 * URL: /api/art/read.php
 * 
 * Query Parameters:
 * - id (optional): int - The unique ID of the art to retrieve.
 * - user_id (optional): int - The user ID to retrieve artworks created by the specified user.
 * 
 * Response Codes:
 * - 200 OK: Art(s) successfully retrieved.
 * - 404 Not Found: Art with the specified ID or user ID does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only GET is allowed).
 * 
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600"); // Cache the preflight response for 1 hour
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$art = new Art($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Only GET is permitted."
    ]);
    exit();
}
try {
    if (isset($_GET["admin_all"]) && isset($_GET["user_id"]) && $decoded->role == "S"){
        $userId = $_GET["user_id"];
        $art->setUserId($userId);
        $stmt = $art->getArtsByUserId();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $reviews,
        ]);
        exit();
    }
    // Fetch art by ID
    if (isset($_GET['id'])) {
        $art_id = intval($_GET['id']);
        $art->setId($art_id);
        $stmt = $art->getArtById();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "message" => "Art not found."
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

    if (isset($_GET["all"])){
        $stmt = $art->getArtWithReviewsAndUser();
        $arts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $arts
        ]);
        exit();
    }

    $userId = $decoded->id;
    // Fetch arts by authenticated user's ID
    if ($decoded->id) {
        $user_id = $userId;
        $art->setUserId($user_id);
        $stmt = $art->getArtsByUserId();
        $arts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "data" => $arts
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