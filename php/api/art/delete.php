<?php
/**
 * Description:
 * This endpoint allows deleting multiple artworks by their IDs.
 * The IDs must be provided as query parameters (`art_id[]=1&art_id[]=2&art_id[]=3`),
 * and the request method must be DELETE.
 *
 * Method: DELETE
 * URL: /api/art/delete.php?action=delete&art_id[]=1&art_id[]=2&art_id[]=3
 *
 * Response Codes:
 * - 200 OK: Artworks were successfully deleted.
 * - 400 Bad Request: No valid IDs provided.
 * - 500 Internal Server Error: Failed to delete artworks.
 * - 405 Method Not Allowed: Invalid request method (only DELETE is allowed).
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$art = new Art($db);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Only DELETE is allowed."
    ]);
    exit;
}

if ($_GET['action'] !== 'delete') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing or invalid 'action' parameter."
    ]);
    exit;
}

if (empty($_GET['art_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "No valid 'art_id' provided."
    ]);
    exit;
}

$art_ids = explode(',', $_GET['art_id']);

if (empty($art_ids)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing art IDs."
    ]);
    exit;
}

try {
    if (!$art->deleteArtsByIds($art_ids)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Deletion failed or no matching records found."
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Artworks were successfully deleted."
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete artworks: " . $e->getMessage()
    ]);
}


