<?php
/**
 * Description:
 * This endpoint allows creating a new credit card entry for a user with required details like user_id, card_number, expiration_date, and CVC.
 * The credit card data must be provided in JSON format within the request body, and the request method must be POST.
 * 
 * Method: POST
 * URL: /api/credit_card/create.php
 * 
 * Request Body:
 * {
 *   "user_id": 1,                      (int, required)
 *   "card_number": "4946511278435961", (string, required)
 *   "expiration_date": "2027-12-31",   (string in YYYY-MM-DD format, required)
 *   "cvc": "123"                       (string, required)
 * }
 * 
 * Response Codes:
 * - 201 Created: Credit card was successfully created.
 * - 400 Bad Request: Invalid input provided or missing required fields.
 * - 500 Internal Server Error: Failed to create credit card due to server error.
 * 
 * Notes:
 * - `user_id`, `card_number`, `expiration_date`, and `cvc` are mandatory fields.
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/CreditCard.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$creditCard = new CreditCard($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

try {
    if (!$creditCard->doesUserExist($data['user_id'])) {
        throw new InvalidArgumentException("User does not exist.");
    }
    
    // Validate and set the credit card details
    $creditCard->setUserId($data['user_id']);
    $creditCard->setCardNumber($data['card_number']);
    $creditCard->setExpirationDate($data['expiration_date']);
    $creditCard->setCVC($data['cvc']);

    if ($creditCard->createCard()) {
        http_response_code(201); // Created
        echo json_encode(["success" => true, "message" => "Credit card created successfully."]);
    } else {
        throw new Exception("Failed to create credit card.");
    }

} catch (InvalidArgumentException $e) {
    // Handle validation errors
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} catch (Exception $e) {
    // Handle other errors (e.g., database issues)
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
