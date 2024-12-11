<?php
/**
 * Description:
 * This endpoint allows deleting multiple artworks by their IDs.
 * The IDs must be provided in an array within the request body, and the request 
 * method must be DELETE.
 * 
 * Method: DELETE
 * URL: /api/art/delete.php
 * 
 * Request Body:
 * {
 *   "ids": [1, 2, 3] // Array of artwork IDs to delete (must be valid positive integers)
 * }
 * 
 * Response Codes:
 * - 200 OK: Artworks were successfully deleted.
 * - 400 Bad Request: No valid IDs provided.
 * - 500 Internal Server Error: Failed to delete artworks.
 * - 405 Method Not Allowed: Invalid request method (only DELETE is allowed).
 */

 // IMPORTANT - current endpoints don't utilize deleteArtById
 // IMPORTANT   at all, since they are already passing in arrays

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$art = new Art($db);

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Only DELETE is allowed."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->ids) || !is_array($data->ids) || empty($data->ids)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "No valid IDs provided."
    ]);
    exit;
}

try {
    // Attempt to delete the artworks by passing the array of IDs
    if (!$art->deleteArtsByIds($data->ids)) {
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
        "message" => "Artworks were successfully deleted."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete artworks: " . $e->getMessage()
    ]);
}
exit;
?>
