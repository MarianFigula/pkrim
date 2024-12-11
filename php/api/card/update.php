<?php
/**
 * Description:
 * This endpoint allows partial updates to a credit card's information.
 * The credit card ID is required to identify the card, and the expiration date can be updated.
 * 
 * Method: PUT
 * URL: /api/credit_card/update.php
 * 
 * Request Body:
 * {
 *   "id": 1,                          // Required: The unique ID of the credit card to update (integer).
 *   "expiration_date": "2026-05-31"   // Optional: The new expiration date for the credit card (string in YYYY-MM-DD format).
 * }
 * 
 * Response Codes:
 * - 200 OK: Credit card successfully updated.
 * - 400 Bad Request: Missing required parameters (id) or invalid data.
 * - 404 Not Found: Credit card with the specified ID does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only PUT is allowed).
 * - 500 Internal Server Error: Failed to update the credit card due to server error.
 * 
 * Notes:
 * - The credit card ID is mandatory, and at least one of the optional fields must be provided for an update.
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/CreditCard.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$creditCard = new CreditCard($db);

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
        "message" => "Valid credit card ID is required to update credit card information."
    ]);
    exit;
}

// Check for at least one parameter to update
$expiration_date = isset($data->expiration_date) ? $data->expiration_date : null;

if (empty($expiration_date)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "At least one parameter ('expiration_date') is required to update the credit card."
    ]);
    exit;
}

// Set the credit card ID
$creditCard->setId($data->id);

// Check if the credit card exists
$stmt = $creditCard->getCardById();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Credit card not found."
    ]);
    exit();
}

try {
    if (!empty($expiration_date)) {
        $creditCard->setExpirationDate($expiration_date);
    }

    if ($creditCard->updateCard()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Credit card successfully updated."
        ]);
    } else {
        throw new Exception("Failed to update credit card.");
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
?>
