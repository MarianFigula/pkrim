<?php
/**
 * Description:
 * This endpoint allows retrieving multiple art details by their IDs.
 * Method: POST
 * URL: /api/art/artDetails.php
 *
 * Body Parameters:
 * - art_ids (required): array - List of art IDs to retrieve.
 *
 * Response Codes:
 * - 200 OK: Art(s) successfully retrieved.
 * - 400 Bad Request: Missing or invalid art IDs.
 * - 404 Not Found: One or more artworks not found.
 *
 */

header("Content-Type: application/json");
include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$art = new Art($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Only POST is permitted."
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['art_ids']) || !is_array($data['art_ids'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Art IDs are required and must be an array."
    ]);
    exit();
}

$artIds = implode(",", array_map('intval', $data['art_ids']));

$stmt = $art->getArtByIds($artIds);
$arts = $stmt->fetchAll(PDO::FETCH_ASSOC);


http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $arts
]);
