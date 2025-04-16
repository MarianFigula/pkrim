<?php
/**
 * Description:
 * This endpoint allows partial updates to an artwork's information.
 * The art ID is required to identify the artwork, and at least one of 
 * the following parameters must be provided to update: title, description, or price.
 * 
 * Method: PUT
 * URL: /api/art/update.php
 * 
 * Request Body:
 * {
 *   "id": 1,                   // Required: The unique ID of the artwork to update (integer).
 *   "title": "New Title",      // Optional: The new title for the artwork (string).
 *   "description": "New Description", // Optional: The new description for the artwork (string).
 *   "price": 100               // Optional: The new price for the artwork (integer or null).
 *
 * }
 * 
 * Response Codes:
 * - 200 OK: Artwork successfully updated.
 * - 400 Bad Request: Missing required parameters (id) or invalid data.
 * - 404 Not Found: Artwork with the specified ID does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only PUT is allowed).
 * - 500 Internal Server Error: Failed to update the artwork due to server error.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$art = new Art($db);

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Only PUT is allowed."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !filter_var($data->id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "A valid artwork ID is required to update artwork information."
    ]);
    exit();
}

$title = $data->title ?? null;
$description = $data->description ?? null;
$price = $data->price ?? null;

if (empty($title) && empty($description) && $price === null) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "At least one parameter ('title', 'description', or 'price') is required to update the artwork."
    ]);
    exit();
}


$art->setId($data->id);

$stmt = $art->getArtById();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Artwork not found."
    ]);
    exit();
}

if ($row['user_id'] !== $decoded->id && $decoded->role !== 'S') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "You do not have permission to update this artwork."
    ]);
    exit();
}

try {
    if (!empty($title)) {
        $art->setTitle($title);
    }
    if (!empty($description)) {
        $art->setDescription($description);
    }
    if ($price !== null) {
        $art->setPrice($price);
    }

    if ($art->updateArtById()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Artwork successfully updated."
        ]);
    } else {
        throw new Exception("Failed to update artwork.");
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