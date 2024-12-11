<?php
/**
 * Description:
 * This endpoint allows deleting multiple credit cards by their IDs. 
 * The IDs must be provided in an array within the request body, and the request 
 * method must be DELETE.
 * 
 * Method: DELETE
 * URL: /api/credit_card/delete.php
 * 
 * Request Body:
 * {
 *   "ids": [1, 2, 3] // Array of credit card IDs to delete (must be valid positive integers)
 * }
 * 
 * Response Codes:
 * - 200 OK: Credit cards were successfully deleted.
 * - 400 Bad Request: No valid IDs provided.
 * - 500 Internal Server Error: Failed to delete credit cards.
 * - 405 Method Not Allowed: Invalid request method (only DELETE is allowed).
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/CreditCard.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$creditCard = new CreditCard($db);

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->ids) || !is_array($data->ids)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No valid IDs provided"]);
    exit;
}

try {
    // Pass the raw IDs array to the model and let it handle the deletion
    if (!$creditCard->deleteCardsByIds($data->ids)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "No valid IDs provided or deletion failed"]);
        exit;
    }

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Credit cards were successfully deleted"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to delete credit cards: " . $e->getMessage()]);
}
exit;
?>
